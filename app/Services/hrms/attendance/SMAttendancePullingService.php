<?php
namespace App\Services\hrms\attendance;

use App\enums\shift\Shifts;
use App\helper\CommonJobService;
use App\Models\SrpEmployeeDetails;
use App\Services\hrms\attendance\computation\SMFixedShiftComputation;
use App\Services\hrms\attendance\computation\SMRotaShiftCrossDayComputation;
use App\Services\hrms\attendance\computation\SMRotaShiftDayComputation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SMAttendancePullingService{

    private $companyId;
    private $pullingDate;
    private $uniqueKey;
    private $isClockOutPulling;

    private $tempData;
    private $attData;
    private $allEmpArr = [];
    private $multipleOccurrence = [];
    private $data = [];
    private $dateTime;
    private $pulledVia;
    private $chunkSize;

    public function __construct($companyId, $pullingDate, $isClockOutPulling)
    {
        Log::useFiles( CommonJobService::get_specific_log_file('attendance-clockIn') );

        $this->companyId = $companyId;
        $this->pullingDate = $pullingDate;
        $this->isClockOutPulling = $isClockOutPulling;
        $this->dateTime = Carbon::now()->format('Y-m-d H:i:s');
        $this->pulledVia = ($isClockOutPulling)? 1: 2;

        $this->uniqueKey = "{$this->companyId}" . rand(2, 500) . '' . Carbon::now()->timestamp;
        $this->chunkSize = 200;

    }

    function execute(){

        $this->insertToLogTb('execution started');

        DB::beginTransaction();

        try{

            if($this->isClockOutPulling){
                $this->deleteEntries();
            }

            if(!$this->isClockOutPulling){
                if(!$this->isAllDataPulled()){ return false; }
            }

            $this->step1();

            if($this->isClockOutPulling){
                $this->step2();
            }

            if(!$this->step3()){ return false; }

            $this->step4();

            $this->step5();

            DB::commit();

            Log::info('Data pulled successfully'.$this->log_suffix(__LINE__));
            return true;

        }
        catch(Exception $ex){

            DB::rollBack();

            $msg = "Exception \n";
            $msg .= "message : ".$ex->getMessage()."\n";;
            $msg .= "file : ".$ex->getFile()."\n";;
            $msg .= "line no : ".$ex->getLine()."\n";;

            Log::error($msg);
            return false;
        }

    }

    function isAllDataPulled(){
        $pending = DB::table('srp_erp_pay_empattendancetemptable')
            ->selectRaw('COUNT(autoID) AS pendingCount')
            ->where('companyID', $this->companyId)
            ->where('isUpdated', 0)
            ->whereDate('attDate', $this->pullingDate)
            ->value('pendingCount');

        if($pending == 0){
            $this->insertToLogTb('nothing to pull');

            Log::error('No records found for pulling'.$this->log_suffix(__LINE__));
            return (!$this->isClockOutPulling) ? false : true;
        }
        return true;
    }

    function step1(){
        $this->updateEmpIdList();
        $empIdList = $this->getEmpIdList();
        $this->step1ChukInsert($empIdList);

    }

    function step1ChukInsert($empIdList){

        $isUpdateWhere = (!$this->isClockOutPulling)? ' AND t.isUpdated = 0 ': '';
        $q = "SELECT t.autoID, l.empID, t.device_id, t.empMachineID, l.floorID, t.attDate, t.attTime, t.uploadType
        FROM srp_erp_pay_empattendancetemptable AS t
        JOIN srp_erp_empattendancelocation AS l ON l.deviceID = t.device_id AND t.empMachineID = l.empMachineID               
        WHERE t.companyID = {$this->companyId} AND t.attDate = '{$this->pullingDate}' {$isUpdateWhere}
        AND NOT EXISTS (
            SELECT * FROM srp_erp_pay_empattendancereview AS r
            WHERE r.companyID = {$this->companyId} AND r.attendanceDate = t.attDate AND r.empID = l.empID 
        )";

        $chunks = array_chunk($empIdList, $this->chunkSize);

        foreach ($chunks as $chunk) {
            $qWithEmpIds = $q . " AND l.empID IN (" . implode(",", $chunk) . ")";
            $tempAttData = DB::table(DB::raw("($qWithEmpIds) as tempTable"))
                ->orderBy('empID')
                ->orderBy('attTime')
                ->get()
                ->toArray();

            $this->tempData = $tempAttData;
            if (empty($this->tempData)) {
                Log::error('No records found for pulling step-1'.$this->log_suffix(__LINE__));
                return false;
            }

            $this->insertToTempTb();
        }

        return true;
    }

    public function getEmpIdList()
    {
        return SrpEmployeeDetails::select('EIdNo')
            ->where('Erp_companyID', $this->companyId)
            ->where('isDischarged', 0)
            ->where(function ($query) {
                $query->whereNull('dischargedDate')
                    ->orWhere('dischargedDate', '<=', $this->pullingDate);
            })
            ->where('isSystemAdmin', 0)
            ->where('empConfirmedYN', 1)
            ->pluck('EIdNo')
            ->toArray();
    }

    function updateEmpIdList(){

        $q = "SELECT t.autoID, t.emp_id as attEmpId, l.empID as mEmpId
        FROM srp_erp_pay_empattendancetemptable AS t
        JOIN srp_erp_empattendancelocation AS l ON l.deviceID = t.device_id AND t.empMachineID = l.empMachineID               
        WHERE t.companyID = {$this->companyId} AND t.attDate = '{$this->pullingDate}' 
        AND (t.emp_id = 0 OR t.emp_id IS NULL)
        AND NOT EXISTS (
            SELECT * FROM srp_erp_pay_empattendancereview AS r
            WHERE r.companyID = {$this->companyId} AND r.attendanceDate = t.attDate AND r.empID = l.empID 
        )";

        DB::table(DB::raw("($q) as tempTable"))
            ->orderBy('autoID')
            ->chunk($this->chunkSize, function ($tempAttData) {
                foreach ($tempAttData as $row) {
                    DB::table('srp_erp_pay_empattendancetemptable')
                        ->where('autoID', $row->autoID)
                        ->update(['emp_id' => $row->mEmpId]);
                }
            });

    }

    function insertToTempTb(){
        $temp = collect($this->tempData);
        unset($this->tempData);
        $temp = $temp->groupBy('empID')->toArray();

        $data = []; $autoIdArr = [];
        foreach ($temp as $empId=> $row) {
            $firstRow = get_object_vars($row[0]); //convert object to array

            $thisData = [];
            $autoIdArr[] = $firstRow['autoID'];

            $thisData['emp_id'] = $empId;
            $thisData['att_date'] = $firstRow['attDate'];
            $thisData['location_in'] = $firstRow['floorID'];
            $thisData['clock_in'] = $firstRow['attTime'];
            $thisData['device_id_in'] = $firstRow['device_id'];
            $thisData['machine_id_in'] = $firstRow['empMachineID'];
            $thisData['upload_type'] = $firstRow['uploadType'];
            $thisData['uniqueID'] = $this->uniqueKey;

            $clockOut = $location_out = $clockOutDevice = $clockOutMachine = $uploadType = null;

            $occurrences = count($row);
            if( $occurrences > 1 ){
                $lastIndex = $occurrences - 1;
                $lastRow = get_object_vars($row[$lastIndex]); //convert object to array

                $autoIdArr[] = $lastRow['autoID'];
                $clockOut = $lastRow['attTime'];
                $location_out = $lastRow['floorID'];
                $clockOutDevice = $lastRow['device_id'];
                $clockOutMachine = $lastRow['empMachineID'];
                $uploadType = $lastRow['uploadType'];

                if($occurrences > 2){
                    $this->multipleOccurrence[] = $empId;
                }
            }

            $thisData['clock_out'] = $clockOut;
            $thisData['location_out'] = $location_out;
            $thisData['device_id_out'] = $clockOutDevice;
            $thisData['machine_id_out'] = $clockOutMachine;
            $thisData['company_id'] = $this->companyId;
            $thisData['upload_type'] = $uploadType;

            $data[] = $thisData;

            DB::table('srp_erp_pay_empattendancetemptable')
                ->whereIn('autoID', array_column($row, 'autoID'))
                ->update([
                    'emp_id'=> $empId
                ]);
        }

        unset($temp);
        DB::table('attendance_temporary_tbl')->insert($data);

        DB::table('srp_erp_pay_empattendancetemptable')
            ->whereIn('autoID', $autoIdArr)
            ->update([
                'isUpdated'=> 1, 'timestamp'=> $this->dateTime
            ]);
    }

    function step2(){
        $q = "SELECT EIdNo, ECode, Ename2
        FROM srp_employeesdetails AS e         
        WHERE e.Erp_companyID = {$this->companyId} AND isSystemAdmin = 0 AND isDischarged = 0 AND empConfirmedYN = 1 
        AND NOT EXISTS (
            SELECT * FROM srp_erp_pay_empattendancereview AS r
            WHERE r.companyID = {$this->companyId} AND r.attendanceDate = '{$this->pullingDate}' AND r.empID = e.EIdNo 
        )  
        AND NOT EXISTS (
            SELECT * FROM attendance_temporary_tbl AS att
            WHERE att.company_id = {$this->companyId} AND att.att_date = '{$this->pullingDate}' AND att.emp_id = e.EIdNo
        )";

        $tempData = DB::table(DB::raw("($q) as tempTable"))->orderBy('EIdNo')->get()->toArray();

        if(empty($tempData)){
            return true;
        }

        $tempChunks = array_chunk($tempData, $this->chunkSize);

        foreach ($tempChunks as $chunk) {
            $this->step2InsertChunkData($chunk);
        }
    }

    function step2InsertChunkData($chunkedTempData){
        if(empty($chunkedTempData)){
            return true;
        }

        $data = [];

        foreach ($chunkedTempData as $chunkedRow) {
            $thisData = [];

            $thisData['emp_id'] = $chunkedRow->EIdNo;
            $thisData['att_date'] = $this->pullingDate;
            $thisData['company_id'] = $this->companyId;
            $thisData['uniqueID'] = $this->uniqueKey;

            $data[] = $thisData;
        }

        DB::table('attendance_temporary_tbl')->insert($data);

    }

    function step3(){
        $shiftQuery = $this->getShiftSubQuery();

        $q = "SELECT t.emp_id, e.ECode, e.Ename2, t.att_date, t.clock_in, t.clock_out, t.location_in, t.location_out, 
        t.upload_type, t.device_id_in, t.machine_id_in, t.machine_id_out, lm.leaveMasterID, lm.leaveHalfDay, 
        shd.onDutyTime, shd.offDutyTime, shd.weekDayNo, IF (IFNULL(shd.isHalfDay, 0), 1, 0) AS isHalfDay, 
        IF(IFNULL(calenders.holiday_flag, 0), 1, 0) AS isHoliday, shd.isWeekend, shd.gracePeriod, shd.isFlexyHour, 
        shd.flexyHrFrom, shd.flexyHrTo, e.isCheckInMust, shd.shiftID, shd.shiftType, shd.workingHour,
        t.company_id, shd.is_cross_day, '12:00:00' as crossDayCutOffTime, wrd.typeId, wrd.detailId
        FROM attendance_temporary_tbl AS t
        JOIN (
            SELECT EIdNo, ECode, Ename2, isCheckin AS isCheckInMust
            FROM srp_employeesdetails WHERE Erp_companyID = {$this->companyId}
        ) AS e ON e.EIdNo = t.emp_id        
        {$shiftQuery}
        LEFT JOIN ( 
            SELECT leaveMasterID, empID, startDate, endDate, ishalfDay as leaveHalfDay
            FROM srp_erp_leavemaster WHERE companyID = {$this->companyId} AND approvedYN = 1 AND cancelledYN is null
        ) AS lm ON lm.empID = t.emp_id AND t.att_date BETWEEN lm.startDate AND lm.endDate 
        LEFT JOIN ( 
            SELECT 	wd.id AS detailId, emp_id, wd.work_out_type_id AS typeId, att_date 
            FROM hr_workout_request_details AS wd
            JOIN hr_workout_request_master AS wm ON wm.id = wd.master_id 
            WHERE wd.company_id = {$this->companyId} AND wm.approved_yn = 1 AND wd.hr_is_approved = 1
        ) AS wrd ON wrd.emp_id = t.emp_id AND t.att_date = wrd.att_date 
        LEFT JOIN ( 
            SELECT * FROM srp_erp_calender WHERE companyID = {$this->companyId} 
            AND fulldate = '{$this->pullingDate}'
        ) AS calenders ON fulldate = t.att_date
        WHERE t.company_id = {$this->companyId} AND uniqueID = '{$this->uniqueKey}'";

        $this->attData = DB::select($q);

        if(empty($this->attData)){
            $this->insertToLogTb('No records found for pulling step-3');
            Log::error('No records found for pulling step-3'.$this->log_suffix(__LINE__));
            return false;
        }

        return true;
    }

    function step4(){
        $companyCode = '';

        foreach ($this->attData as $row) {
            $row = get_object_vars($row);
            $attDate = $row['att_date'];
            $empId = $row['emp_id'];
            $this->allEmpArr[] = $empId;
            $isCrossDay = $row['is_cross_day'];

            if ($row['shiftType'] == Shifts::FIXED || empty($row['shiftType'])) {
                $obj = new SMFixedShiftComputation($row, $this->companyId);
            } elseif ($isCrossDay) {
                $obj = new SMRotaShiftCrossDayComputation($row, $this->companyId);
            } else {
                $obj = new SMRotaShiftDayComputation($row, $this->companyId);
            }

            $obj->calculate();

            $shiftHours = ($row['shiftType'] == Shifts::OPEN)? $row['workingHour']: $obj->shiftHours;
            $shiftHours = (empty($shiftHours))? 0: $shiftHours;
            $locationOut = $isCrossDay ? $obj->clockOutFloorId : $row['location_out'];

            $this->data[] = [
                'empID' => $empId,
                'deviceID' => $row['device_id_in'],
                'machineID' => $row['machine_id_in'],
                'attendanceDate' => $attDate,
                'shift_id' => !empty($row['shiftID']) ? $row['shiftID'] : 0,
                'floorID' => $row['location_in'],
                'clockoutFloorID' => $locationOut,
                'gracePeriod' => $obj->gracePeriod,
                'onDuty' => $row['onDutyTime'],
                'offDuty' => $row['offDutyTime'],
                'is_cross_day' => $isCrossDay,

                'noPayAmount' => $obj->absDedAmount,
                'noPaynonPayrollAmount' => $obj->absDedNonAmount,
                'salaryCategoryID' => $obj->salCatId,

                'checkIn' => $obj->clockIn,
                'checkOut' => $obj->clockOut,
                'work_out_detail_id'=> $row['detailId'],
                'presentTypeID' => $obj->presentAbsentType,

                'normalTime' => ($row['isHalfDay'] == 1) ? 0.5 : 1,
                'lateHours' => $obj->lateHours,
                'lateFee' => $obj->lateFee,
                'earlyHours' => $obj->earlyHours,
                'OTHours' => $obj->overTimeHours,
                'realTime' => $obj->realTime,
                'shift_hours' => $shiftHours,

                'isNormalDay' => $obj->normalDayData['true_false'],
                'NDaysOT' => $obj->normalDayData['hours'],
                'normalDay' => $obj->normalDayData['realTime'],

                'isWeekEndDay' => $obj->weekendData['true_false'],
                'weekendOTHours' => $obj->weekendData['hours'],
                'weekend' => $obj->weekendData['realTime'],

                'isHoliday' => $obj->holidayData['true_false'],
                'holidayOTHours' => $obj->holidayData['hours'],
                'holiday' => $obj->holidayData['realTime'],

                'mustCheck' => $row['isCheckInMust'],
                'isMultipleOcc' => $this->moreThan2RecordsExists($empId),
                'flexyHrFrom' => !empty($obj->flexibleHourFrom) ? $obj->flexibleHourFrom : null,
                'flexyHrTo' => !empty($obj->flexibleHourTo) ? $obj->flexibleHourTo : null,
                'companyID' => $this->companyId,
                'companyCode' => $companyCode,
                'uploadType' => $row['upload_type'],
                'pulled_by' => 0, 'pulled_at' => $this->dateTime,
                'pulled_via' => $this->pulledVia,
                'actual_time' => $obj->actualWorkingHours,
                'official_work_time' => $obj->officialWorkTime
            ];

            $obj = null;
        }

        $this->insertToLogTb([
            'about to insert'=> array_column($this->data, 'empID')
        ]);

        Log::info(' step-4 passed '.$this->log_suffix(__LINE__));

        unset($this->attData);

    }

    function step5(){

        DB::table('aattendance_temporary_tbl')->where('uniqueID', $this->uniqueKey)->delete();
        DB::table('srp_erp_pay_empattendancedaterangetemp')->where('uniqueID', $this->uniqueKey)->delete();


        if (empty($this->data)) {
            Log::error('No records found for pulling step-5'.$this->log_suffix(__LINE__));
            return false;
        }

        DB::table('srp_erp_pay_empattendancereview')->insert($this->data);

        $this->update_ot_settlement_type();

        return true;
    }

    private function update_ot_settlement_type(){
        $emp_array = array_unique($this->allEmpArr);
        $lieuBasedEmp = $this->get_ot_settlement_type_of_lieuLeave($emp_array);

        if(empty($lieuBasedEmp)){
            return true;
        }

        DB::table('srp_erp_pay_empattendancereview')
            ->whereIn('empID', $lieuBasedEmp)
            ->whereDate('attendanceDate', $this->pullingDate)
            ->update(['settlementType'=> 2]);

        return true;
    }

    function get_ot_settlement_type_of_lieuLeave($emp_array){
        $data = DB::table('srp_employeesdetails AS e')
            ->join('srp_erp_employeegrade AS g', 'g.gradeID', '=', 'e.gradeID')
            ->where('g.isLieuLeave', 1)
            ->whereIn('e.EIdNo', $emp_array)
            ->get();

        if(empty($data)){
            return [];
        }

        $data = get_object_vars($data);

        return array_column($data, 'EIdNo');
    }

    private function deleteEntries(){

        $noOfRows = DB::table('srp_erp_pay_empattendancereview')
            ->where('companyID', $this->companyId)
            ->where('attendanceDate', $this->pullingDate)
            ->where('confirmedYN', 0)
            ->delete();

        $msg = "Number of rows deleted on 'srp_erp_pay_empattendancereview' table : {$noOfRows} 
                (date : {$this->pullingDate})";
        Log::info($msg.$this->log_suffix(__LINE__));
    }

    function moreThan2RecordsExists($empId){
        return ( in_array($empId, $this->multipleOccurrence) )? 1 : 0;
    }

    function log_suffix($line_no) : string{
        return " | companyId: $this->companyId \t on file:  " . __CLASS__ ." \tline no : {$line_no}";
    }

    public function insertToLogTb($logData, $logType = 'info'){
        $logData = json_encode($logData);

        $description = ($this->isClockOutPulling)? 'attendance-clock-out-job': 'attendance-real-time-sync';

        $data = [
            'company_id'=> $this->companyId,
            'module'=> 'HRMS',
            'description'=> $description,
            'scenario_id'=> 0,
            'processed_for'=> $this->pullingDate,
            'logged_at'=> $this->dateTime,
            'log_type'=> $logType,
            'log_data'=> $logData,
        ];

        DB::table('job_logs')->insert($data);
    }

    function getShiftSubQuery()
    {
        $shFixed = Shifts::FIXED;
        $shRota = Shifts::ROTA;

        $fixedJoin = "SELECT sm.shiftID, sd.onDutyTime, sd.offDutyTime, sd.isHalfDay, sd.weekDayNo, sd.isWeekend, 
                sd.gracePeriod, sm.isFlexyHour, sd.flexyHrFrom, sd.flexyHrTo, sm.shiftType, sd.workingHour, 
                sd.is_cross_day, she.schedule_date, sm.leave_deduction_rate, she.emp_id
                FROM hr_shift_schedule_details AS she 
                JOIN srp_erp_pay_shiftmaster AS sm ON  sm.shiftID = she.shift_id 
                JOIN srp_erp_pay_shiftdetails AS sd ON sd.shiftID = sm.shiftID 
                AND sd.weekDayNo = WEEKDAY(she.schedule_date)
                WHERE sm.companyID = {$this->companyId}
                AND sm.shiftType = {$shFixed} ";

        $rotaUnion = " UNION ALL 
                SELECT sm.shiftID, sd.onDutyTime, sd.offDutyTime, sd.isHalfDay, sd.weekDayNo, sd.isWeekend, 
                sd.gracePeriod, sm.isFlexyHour, sd.flexyHrFrom, sd.flexyHrTo, sm.shiftType, sd.workingHour, 
                sd.is_cross_day, she.schedule_date, sm.leave_deduction_rate, she.emp_id 
                FROM hr_shift_schedule_details AS she 
                JOIN srp_erp_pay_shiftmaster AS sm ON  sm.shiftID = she.shift_id 
                JOIN srp_erp_pay_shiftdetails AS sd ON sd.shiftID = sm.shiftID 
                WHERE sm.companyID = {$this->companyId}
                AND sm.shiftType = {$shRota} ";

        $OffDayUnion = " UNION ALL 
                SELECT shift_id AS shiftID, '' AS onDutyTime, '' AS offDutyTime, 0 AS isHalfDay,
                WEEKDAY(she.schedule_date) AS weekDayNo, 1 AS isWeekend, 
                '' AS gracePeriod, 1 AS isFlexyHour, '' AS flexyHrFrom, '' AS flexyHrTo, -1 AS shiftType, 
                0 AS workingHour, 0 AS is_cross_day,
                she.schedule_date, 1 AS leave_deduction_rate, she.emp_id 
                FROM hr_shift_schedule_details AS she
                WHERE she.company_id = {$this->companyId} 
                AND she.shift_id = 0 
                GROUP BY schedule_date, shift_id, emp_id ";


        return "LEFT JOIN({$fixedJoin} {$rotaUnion} {$OffDayUnion}) AS shd ON shd.emp_id = e.EIdNo 
                    AND t.att_date = shd.schedule_date";

    }
}

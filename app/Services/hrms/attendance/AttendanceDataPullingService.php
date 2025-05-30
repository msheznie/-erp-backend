<?php
namespace App\Services\hrms\attendance;

use App\enums\modules\Modules;
use App\enums\shift\Shifts;
use App\helper\SME;
use App\Models\SrpEmployeeDetails;
use App\Services\hrms\modules\HrModuleAssignService;
use Collator;
use Exception;
use Carbon\Carbon;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\hrms\attendance\AttendanceComputationService;

class AttendanceDataPullingService{
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
    private $weekendColumn;
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

            $this->weekendPolicy();
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
        $i = 1;
        foreach ($chunks as $chunk) {
            $qWithEmpIds = $q . " AND t.emp_id IN (" . implode(",", $chunk) . ")";
            $tempAttData = DB::table(DB::raw("($qWithEmpIds) as tempTable"))
                ->orderBy('empID')
                ->orderBy('attTime')
                ->get()
                ->toArray();

            $this->tempData = $tempAttData;
            if (empty($this->tempData)) {
                Log::error('No records found for pulling step-1 and chunk-'.$i.' '.$this->log_suffix(__LINE__));
                continue;
            }

            $this->insertToTempTb();
            $i++;
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
                if (empty($tempAttData)) {
                    return true;
                }
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

    function step2InsertChunkData($chunk){
        $data = [];
        foreach ($chunk as $key) {
            $thisData = [
                'emp_id' => $key->EIdNo,
                'att_date' => $this->pullingDate,
                'company_id' => $this->companyId,
                'uniqueID' => $this->uniqueKey,
            ];

            $data[] = $thisData;
        }

        DB::table('attendance_temporary_tbl')->insert($data);
    }

    function step3(){
        $q = "SELECT t.emp_id, e.ECode, e.Ename2, t.att_date, t.clock_in, t.clock_out, t.location_in, t.location_out, 
        t.upload_type, t.device_id_in, t.machine_id_in, t.machine_id_out, lm.leaveMasterID, lm.leaveHalfDay, 
        shd.onDutyTime, shd.offDutyTime, shd.weekDayNo, IF (IFNULL(shd.isHalfDay, 0), 1, 0) AS isHalfDay, 
        IF(IFNULL(calenders.holiday_flag, 0), 1, 0) AS isHoliday, {$this->weekendColumn} AS isWeekend, shd.gracePeriod,
        shd.isFlexyHour, shd.flexyHrFrom, shd.flexyHrTo, e.isCheckInMust, shd.shiftID, shd.shiftType, shd.workingHour,
        t.company_id, IF(wrd.typeId,wrd.typeId,trd.typeId) as typeId, wrd.detailId
        FROM attendance_temporary_tbl AS t
        JOIN (
            SELECT EIdNo, ECode, Ename2, isCheckin AS isCheckInMust
            FROM srp_employeesdetails WHERE Erp_companyID = {$this->companyId}
        ) AS e ON e.EIdNo = t.emp_id        
        LEFT JOIN (
        	SELECT * FROM srp_erp_pay_shiftemployees
        	WHERE companyID = {$this->companyId} AND ('{$this->pullingDate}' BETWEEN startDate and endDate )
            AND srp_erp_pay_shiftemployees.isActive = 1
        ) AS she ON she.empID = e.EIdNo
        LEFT JOIN (
            SELECT sm.shiftID, sd.onDutyTime, sd.offDutyTime, sd.isHalfDay, sd.weekDayNo, sd.isWeekend, 
            sd.gracePeriod, sm.isFlexyHour, sd.flexyHrFrom, sd.flexyHrTo, sm.shiftType, sd.workingHour 
            FROM srp_erp_pay_shiftdetails AS sd 
            JOIN srp_erp_pay_shiftmaster AS sm ON  sm.shiftID = sd.shiftID 
            WHERE sm.companyID = {$this->companyId}
        ) AS shd ON shd.shiftID = she.shiftID AND shd.weekDayNo = WEEKDAY(t.att_date) 
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
            SELECT req_emp_id_confirmed AS emp_id, 5 as typeId, date_travel, date_return 
            FROM hr_trip_request_master 
            WHERE company_id = {$this->companyId} AND rpt_manager_confirmed_yn = 1
        ) AS trd ON trd.emp_id = t.emp_id AND t.att_date BETWEEN trd.date_travel AND trd.date_return
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


            $obj = new AttendanceComputationService($row, $this->companyId);
            $obj->execute();

            $shiftHours = ($row['shiftType'] == Shifts::OPEN)? $row['workingHour']: $obj->shiftHours;
            $shiftHours = (empty($shiftHours))? 0: $shiftHours;

            $this->data[] = [
                'empID' => $empId,
                'deviceID' => $row['device_id_in'],
                'machineID' => $row['machine_id_in'],
                'attendanceDate' => $attDate,
                'shift_id' => !empty($row['shiftID']) ? $row['shiftID'] : 0,
                'floorID' => $row['location_in'],
                'clockoutFloorID' => $row['location_out'],
                'gracePeriod' => $obj->gracePeriod,
                'onDuty' => $row['onDutyTime'],
                'offDuty' => $row['offDutyTime'],

                'noPayAmount' => $obj->absDedAmount,
                'noPaynonPayrollAmount' => $obj->absDedNonAmount,
                'salaryCategoryID' => $obj->salCatId,

                'checkIn' => $row['clock_in'],
                'checkOut' => $row['clock_out'],
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

        DB::table('attendance_temporary_tbl')->where('uniqueID', $this->uniqueKey)->delete();
        DB::table('srp_erp_pay_empattendancedaterangetemp')->where('uniqueID', $this->uniqueKey)->delete();


        if (empty($this->data)) {
            Log::error('No records found for pulling step-5'.$this->log_suffix(__LINE__));
            return false;
        }

        DB::table('srp_erp_pay_empattendancereview')->insert($this->data);

        $this->updateOtSettlementType();

        return true;
    }

    private function updateOtSettlementType(){
        $empArray = array_unique($this->allEmpArr);
        $lieuBasedEmp = $this->getOtSettlementTypeOfLieuLeave($empArray);

        if(empty($lieuBasedEmp)){
            return true;
        }

        DB::table('srp_erp_pay_empattendancereview')
            ->whereIn('empID', $lieuBasedEmp)
            ->whereDate('attendanceDate', $this->pullingDate)
            ->where('isGeneralOT', '!=', 1)
            ->update(['settlementType'=> 2]);

        return true;
    }

    function getOtSettlementTypeOfLieuLeave($empArray){
        $lieuLeaveBaseOn = SME::policy($this->companyId, 'LLB', 'All');

        $data = DB::table('srp_employeesdetails AS e')
            ->join('srp_erp_employeegrade AS g', 'g.gradeID', '=', 'e.gradeID')
            ->where(['e.Erp_companyID' => $this->companyId, 'g.isLieuLeave' => 1])
            ->whereIn('e.EIdNo', $empArray);

        if ($lieuLeaveBaseOn == 2) {
            $data = DB::table('srp_employeesdetails as e')
                ->join('srp_employeedesignation as ed', 'ed.EmpID', '=', 'e.EIdNo')
                ->join('srp_designation as d', 'd.DesignationID', '=', 'ed.DesignationID')
                ->where(['d.isLieuLeave' => 1, 'e.Erp_companyID' => $this->companyId, 'ed.isMajor' => 1])
                ->whereIn('e.EIdNo', $empArray);
        }

        return $data->pluck('e.EIdNo')->toArray();
    }

    private function deleteEntries(){

        $noOfRows = DB::table('srp_erp_pay_empattendancereview')
            ->where('companyID', $this->companyId)
            ->where('attendanceDate', $this->pullingDate)
            ->where('confirmedYN', 0)
            ->delete();

        $msg = "Number of rows deleted on 'srp_erp_pay_empattendancereview' table : {$noOfRows} (date : {$this->pullingDate})";
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

    function weekendPolicy(){

        $this->weekendColumn = (SME::policy($this->companyId, 'LCW', 'LA'))
            ? "IFNULL(shd.isWeekend, 0)"
            : "IFNULL(calenders.weekend_flag, 0)";
    }
}

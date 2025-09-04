<?php
namespace App\Services\hrms\attendance;
use App\helper\CommonJobService;
use App\Services\hrms\attendance\computation\SMCrossDayOnlyComputation;
use App\Services\hrms\attendance\computation\SMRotaShiftIndividualPunchesComputation;
use App\Services\hrms\feature\FeatureFlagService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SMAttendanceCrossDayPullingService{
    protected $companyId;
    protected $pullingDate;
    protected $dateTime;
    protected $chunkSize;
    protected $reviewAttData = [];
    protected $bulkUpdateData = [];
    protected $bulkTempTableUpdates = [];

    protected $cutOffTime;
    protected $prvDate;


    protected $clockOutDataCache = [];
    protected $leaveDataCache = [];
    protected $shiftDataCache = [];
    protected $isFeatureEnabled;

    public function __construct($companyId, $pullingDate){
        Log::useFiles( CommonJobService::get_specific_log_file('attendance-cross-day-clockOut') );

        $this->companyId = $companyId;
        $this->pullingDate = $pullingDate;
        $this->dateTime = Carbon::now()->format('Y-m-d H:i:s');
        $this->cutOffTime = '12:00:00';
        $this->chunkSize = 500;
    }

    function execute()
    {
        $this->insertToLogTb('cross day execution started');
        DB::beginTransaction();
        try {
            $this->getUnConfirmAttendanceData();
            
            if (empty($this->reviewAttData)) {
                DB::commit();
                Log::info('No unconfirmed attendance data found'.$this->log_suffix(__LINE__));
                return true;
            }

            $this->preLoadRelatedData();
            $this->mapEachEmpData();
            $this->performBulkUpdates();
            $this->isFeatureEnabled = FeatureFlagService::isFeatureEnabled('shift_work_hr_cal');

            DB::commit();
            Log::info('Data cross day shifts pulled successfully'.$this->log_suffix(__LINE__));
            return true;

        } catch (Exception $ex) {
            DB::rollBack();

            $msg = "Exception \n";
            $msg .= "message : " . $ex->getMessage() . "\n";;
            $msg .= "file : " . $ex->getFile() . "\n";;
            $msg .= "line no : " . $ex->getLine() . "\n";;

            Log::error($msg);
            return false;
        }
    }

    function getUnConfirmAttendanceData(){
        $this->prvDate = Carbon::createFromFormat('Y-m-d', $this->pullingDate)
            ->timezone('Asia/Muscat')
            ->subDays(1)
            ->format('Y-m-d');

        $this->reviewAttData = DB::table('srp_erp_pay_empattendancereview')
            ->where('companyID', $this->companyId)
            ->where('is_cross_day', 1)
            ->where('confirmedYN', 0)
            ->whereDate('attendanceDate', $this->prvDate)
            ->get();
    }

    function preLoadRelatedData(){

        $empIds = $this->reviewAttData->pluck('empID')->toArray();
        $shiftIds = $this->reviewAttData->pluck('shift_id')->unique()->toArray();

        $this->preloadClockOutData($empIds);
        $this->preloadLeaveData($empIds);
        $this->preloadShiftData($shiftIds);
    }

    function preloadClockOutData($empIds){
        $clockOutData = DB::table('srp_erp_pay_empattendancetemptable as t')
            ->select('t.emp_id', 't.attTime', 't.autoID', 'l.floorID','t.attDate as clockOutDate')
            ->join("srp_erp_empattendancelocation AS l", function ($join) {
                $join->on("l.deviceID", "=", "t.device_id")
                    ->on("t.empMachineID", "=", "l.empMachineID");
            })
            ->where('t.companyID', $this->companyId)
            ->whereIn('t.emp_id', $empIds)
            ->where('t.attDate', $this->pullingDate)
            ->where('t.attTime', '<=', $this->cutOffTime)
            ->orderBy('t.attDate')
            ->orderBy('t.attTime')
            ->get()
            ->groupBy('emp_id');

        foreach ($clockOutData as $empId => $records) {
            $this->clockOutDataCache[$empId] = $records->last();
        }
    }

    function preloadLeaveData($empIds){
        $leaveData = DB::table('srp_erp_leavemaster')
            ->select('empID', 'leaveMasterID', 'ishalfDay')
            ->where('companyID', $this->companyId)
            ->whereIn('empID', $empIds)
            ->where('approvedYN', 1)
            ->whereNull('cancelledYN')
            ->whereDate('startDate', '<=', "'$this->pullingDate'")
            ->whereDate('endDate', '>=',  "'$this->pullingDate'")
            ->get()
            ->groupBy('empID');

        foreach ($leaveData as $empId => $records) {
            $this->leaveDataCache[$empId] = $records->last();
        }
    }

    function preloadShiftData($shiftIds){
        $shiftData = DB::table('srp_erp_pay_shiftmaster as sm')
            ->select('sm.shiftID', 'sd.onDutyTime', 'sd.offDutyTime', 'sd.isHalfDay', 'sd.weekDayNo','sd.isWeekend',
                'sm.isFlexyHour', 'sd.flexyHrFrom', 'sd.flexyHrTo', 'sm.shiftType', 'sd.workingHour',
                'sm.leave_deduction_rate', 'sm.work_hour_calc_method')
            ->join('srp_erp_pay_shiftdetails as sd', 'sd.shiftID', '=', 'sm.shiftID')
            ->where('sm.companyID', $this->companyId)
            ->where('sm.shiftType', 2)
            ->whereIn('sm.shiftID', $shiftIds)
            ->get()
            ->groupBy('shiftID');

        foreach ($shiftData as $shiftId => $records) {
            $this->shiftDataCache[$shiftId] = $records->last();
        }
    }

    function mapEachEmpData(){
        foreach ($this->reviewAttData as $key => $row){
            $curEmpId = $row->empID;
            $shiftId = $row->shift_id;
            $clock_in = $row->checkIn;
            $clock_out = $row->checkOut;

            $clockOutData = $this->clockOutDataCache[$curEmpId] ?? null;
            $leaveData = $this->leaveDataCache[$curEmpId] ?? null;
            $shiftData = $this->shiftDataCache[$shiftId] ?? null;

            $this->reviewAttData[$key]->clock_in = $clock_in;
            $this->reviewAttData[$key]->clock_out = $clock_out;

            $this->reviewAttData[$key]->clockOutDate =
                isset($clockOutData->clockOutDate) ? $clockOutData->clockOutDate : $this->prvDate;

            $this->reviewAttData[$key]->isClockInOutSet = isset($clockOutData->clockOutDate) ? 1 : 0;
            $this->reviewAttData[$key]->checkOut = isset($clockOutData->attTime) ? $clockOutData->attTime : null;
            $this->reviewAttData[$key]->autoId = isset($clockOutData->autoID) ? $clockOutData->autoID : null;
            $this->reviewAttData[$key]->clockOutFloorId = isset($clockOutData->floorID) ? $clockOutData->floorID : null;

            $this->reviewAttData[$key]->leaveMasterID =
                isset($leaveData->leaveMasterID) ? $leaveData->leaveMasterID : null;

            $this->reviewAttData[$key]->leaveHalfDay = isset($shiftData->ishalfDay) ? $shiftData->ishalfDay : null;

            $this->reviewAttData[$key]->crossDayCutOffTime = $this->cutOffTime;
            $this->reviewAttData[$key]->isFlexibleHour = $shiftData->isFlexyHour ?? 0;
            $this->reviewAttData[$key]->flexibleHourFrom = $shiftData->flexyHrFrom ?? null;
            $this->reviewAttData[$key]->flexibleHourTo = $shiftData->flexyHrTo ?? null;
            $this->reviewAttData[$key]->workHourCalcMethod = $shiftData->work_hour_calc_method;
            $this->reviewAttData[$key]->shiftID = $shiftData->shiftID ?? $shiftId;

            $curRow = $this->reviewAttData[$key];
            if($shiftData->work_hour_calc_method == 2 && $this->isFeatureEnabled){
                $this->computeIndividualPunches($curRow);
            }else{
                $this->computeAttendance($curRow);
            }
        }
    }

    function computeAttendance($curRow){
        $obj = new SMCrossDayOnlyComputation($curRow, $this->companyId);
        $obj->calculate();
        $attId = $curRow->ID;
        $autoId = $curRow->autoId;

        $updateData = [
            'att_id' => $attId,
            'gracePeriod'=> $curRow->gracePeriod,
            'checkOut'=> $obj->clockOut,
            'check_out_date'=> $obj->clockOutDate,
            'presentTypeID' => $obj->presentAbsentType,
            'clockoutFloorID' => $curRow->clockOutFloorId,
            'noPayAmount' => $obj->absDedAmount,
            'noPaynonPayrollAmount' => $obj->absDedNonAmount,
            'salaryCategoryID' => $obj->salCatId,
            'nonPayrollSalaryCategoryID' => $obj->nonSalCatId,
            'lateHours'=> $obj->lateHours,
            'lateFee'=> $obj->lateFee,
            'earlyHours'=> $obj->earlyHours,
            'OTHours' => $obj->overTimeHours,
            'realTime' => $obj->realTime,
            'isNormalDay' => $obj->normalDayData['true_false'],
            'NDaysOT' => $obj->normalDayData['hours'],
            'normalDay' => $obj->normalDayData['realTime'],
            'isWeekEndDay' => $obj->weekendData['true_false'],
            'weekendOTHours' => $obj->weekendData['hours'],
            'weekend' => $obj->weekendData['realTime'],
            'isHoliday' => $obj->holidayData['true_false'],
            'holidayOTHours' => $obj->holidayData['hours'],
            'holiday' => $obj->holidayData['realTime'],
            'flexyHrFrom' => !empty($obj->flexibleHourFrom) ? $obj->flexibleHourFrom : null,
            'flexyHrTo' => !empty($obj->flexibleHourTo) ? $obj->flexibleHourTo : null,
            'actual_time' => $obj->actualWorkingHours,
            'official_work_time' => $obj->officialWorkTime
        ];

        $this->bulkUpdateData[] = $updateData;

        if (!empty($autoId)) {
            $this->bulkTempTableUpdates[] = ['autoID' => $autoId];
        }
    }

    function computeIndividualPunches($curRow)
    {
        $data = [
            'emp_id' => $curRow->empID,
            'att_date' => $curRow->attendanceDate,
            'crossDayCutOffTime' => $curRow->crossDayCutOffTime,
            'is_cross_day' => $curRow->is_cross_day,
            'isHoliday' => $curRow->isHoliday,
            'isWeekend' => $curRow->isWeekEndDay,
            'leaveMasterID' => $curRow->leaveMasterID,
            'leaveHalfDay' => $curRow->leaveHalfDay,
            'typeId' => null,
            'onDutyTime' => $curRow->onDuty,
            'offDutyTime' => $curRow->offDuty,
            'gracePeriod' => $curRow->gracePeriod,
            'isFlexyHour' => $curRow->isFlexibleHour,
            'flexyHrFrom' => $curRow->flexibleHourFrom,
            'flexyHrTo' => $curRow->flexibleHourTo,
            'clock_in' => $curRow->clock_in,
            'clock_out' => $curRow->clock_out,
            'shiftID' => $curRow->shiftID
        ];

        $obj = new SMRotaShiftIndividualPunchesComputation($data, $this->companyId);
        $obj->calculate();
        $attId = $curRow->ID;
        $autoId = $curRow->autoId;

        $updateData = [
            'att_id' => $attId,
            'gracePeriod' => $curRow->gracePeriod,
            'checkIn' => $obj->clockIn,
            'checkOut' => $obj->clockOut,
            'check_out_date' => $obj->clockOutDate,
            'presentTypeID' => $obj->presentAbsentType,
            'clockoutFloorID' => $obj->clockOutFloorId,
            'noPayAmount' => $obj->absDedAmount,
            'noPaynonPayrollAmount' => $obj->absDedNonAmount,
            'salaryCategoryID' => $obj->salCatId,
            'nonPayrollSalaryCategoryID' => $obj->nonSalCatId,
            'lateHours' => $obj->lateHours,
            'lateFee' => $obj->lateFee,
            'earlyHours' => $obj->earlyHours,
            'OTHours' => $obj->overTimeHours,
            'realTime' => $obj->realTime,
            'isNormalDay' => $obj->normalDayData['true_false'],
            'NDaysOT' => $obj->normalDayData['hours'],
            'normalDay' => $obj->normalDayData['realTime'],
            'isWeekEndDay' => $obj->weekendData['true_false'],
            'weekendOTHours' => $obj->weekendData['hours'],
            'weekend' => $obj->weekendData['realTime'],
            'isHoliday' => $obj->holidayData['true_false'],
            'holidayOTHours' => $obj->holidayData['hours'],
            'holiday' => $obj->holidayData['realTime'],
            'flexyHrFrom' => !empty($obj->flexibleHourFrom) ? $obj->flexibleHourFrom : null,
            'flexyHrTo' => !empty($obj->flexibleHourTo) ? $obj->flexibleHourTo : null,
            'actual_time' => $obj->actualWorkingHours,
            'official_work_time' => $obj->officialWorkTime
        ];

        $this->bulkUpdateData[] = $updateData;

        if (!empty($autoId)) {
            $this->bulkTempTableUpdates[] = ['autoID' => $autoId];
        }
    }

    function performBulkUpdates(){
        if (!empty($this->bulkUpdateData)) {
            foreach ($this->bulkUpdateData as $updateData) {
                $attId = $updateData['att_id'];
                unset($updateData['att_id']);

                DB::table('srp_erp_pay_empattendancereview')
                    ->where('ID', $attId)
                    ->update($updateData);
            }
        }

        if (!empty($this->bulkTempTableUpdates)) {
            $autoIds = array_column($this->bulkTempTableUpdates, 'autoID');
            if (!empty($autoIds)) {
                DB::table('srp_erp_pay_empattendancetemptable')
                    ->whereIn('autoID', $autoIds)
                    ->update([
                        'isUpdated' => 1,
                        'timestamp' => $this->dateTime
                    ]);
            }
        }
    }

    public function insertToLogTb($logData, $logType = 'info'){
        $logData = json_encode($logData);

        $description = 'attendance-cross-day-clock-out-job';

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

    function log_suffix($line_no) : string{
        return " | companyId: $this->companyId \t on file:  " . __CLASS__ ." \tline no : {$line_no}";
    }

}

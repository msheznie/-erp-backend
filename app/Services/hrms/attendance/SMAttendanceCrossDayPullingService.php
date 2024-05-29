<?php
namespace App\Services\hrms\attendance;
use App\helper\CommonJobService;
use App\Services\hrms\attendance\computation\SMCrossDayOnlyComputation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SMAttendanceCrossDayPullingService{
    private $companyId;
    private $pullingDate;
    private $dateTime;
    public $chunkSize;
    public $reviewAttData = [];
    public $updateData = [];

    private $cutOffTime;

    private $prvDate;

    public function __construct($companyId, $pullingDate){
        Log::useFiles( CommonJobService::get_specific_log_file('attendance-cross-day-clockOut') );

        $this->companyId = $companyId;
        $this->pullingDate = $pullingDate;
        $this->dateTime = Carbon::now()->format('Y-m-d H:i:s');
        $this->cutOffTime = '12:00:00';
    }

    function execute()
    {
        $this->insertToLogTb('cross day execution started');
        DB::beginTransaction();
        try {

            $this->getUnConfirmAttendanceData();

            $this->mapEachEmpData();

            $this->updateUnConfirmAttendance();

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

    function mapEachEmpData(){
        foreach ($this->reviewAttData as $key => $row){
            $curEmpId = $row->empID;
            $shiftId = $row->shift_id;

            $clockOutData = $this->getEachEmpClockOutData($curEmpId);
            $leaveData = $this->getLeaveData($curEmpId);
            $shiftData = $this->getShiftData($shiftId);

            $this->reviewAttData[$key]->clockOutDate = $clockOutData->clockOutDate;
            $this->reviewAttData[$key]->checkOut = $clockOutData->attTime;
            $this->reviewAttData[$key]->autoId = $clockOutData->autoID;
            $this->reviewAttData[$key]->clockOutFloorId = $clockOutData->floorID;
            $this->reviewAttData[$key]->leaveMasterID = isset($leaveData->leaveMasterID) ?? $leaveData->leaveMasterID;
            $this->reviewAttData[$key]->leaveHalfDay = isset($shiftData->ishalfDay) ?? $shiftData->ishalfDay;
            $this->reviewAttData[$key]->crossDayCutOffTime = $this->cutOffTime;
            $this->reviewAttData[$key]->isFlexibleHour = $shiftData->isFlexyHour;
            $this->reviewAttData[$key]->flexibleHourFrom = $shiftData->flexyHrFrom;
            $this->reviewAttData[$key]->flexibleHourTo = $shiftData->flexyHrTo;



            $curRow = $this->reviewAttData[$key];

            $this->computeAttendance($curRow);
        }
    }

    function getEachEmpClockOutData($curEmpId){

        $attendances = DB::table('srp_erp_pay_empattendancetemptable as t')
            ->select('t.attTime', 't.autoID', 'l.floorID','t.attDate as clockOutDate')
            ->join("srp_erp_empattendancelocation AS l", function ($join) {
                $join->on("l.deviceID", "=", "t.device_id")
                    ->on("t.empMachineID", "=", "l.empMachineID");
            })
            ->where('t.companyID', $this->companyId)
            ->where('t.emp_id', $curEmpId)
            ->where('t.attDate', $this->pullingDate)
            ->where('t.attTime', '<=', $this->cutOffTime)
            ->orderBy('t.attDate')
            ->orderBy('t.attTime')
            ->get();
        return $attendances->last();

    }

    function getLeaveData($curEmpId){

        return DB::table('srp_erp_leavemaster')
            ->select('leaveMasterID', 'ishalfDay')
            ->where('empID', $curEmpId)
            ->where('companyID', $this->companyId)
            ->where('approvedYN', 1)
            ->whereNull('cancelledYN')
            ->whereDate('startDate', '<=', "'$this->pullingDate'")
            ->whereDate('endDate', '>=',  "'$this->pullingDate'")
            ->get()
            ->last();
    }

    function getShiftData($shiftId){
        return  DB::table('srp_erp_pay_shiftmaster as sm')
            ->select('sm.shiftID', 'sd.onDutyTime', 'sd.offDutyTime', 'sd.isHalfDay', 'sd.weekDayNo','sd.isWeekend',
                'sm.isFlexyHour', 'sd.flexyHrFrom', 'sd.flexyHrTo', 'sm.shiftType', 'sd.workingHour',
                'sm.leave_deduction_rate')
            ->join('srp_erp_pay_shiftdetails as sd', 'sd.shiftID', '=', 'sm.shiftID')
            ->where('sm.companyID', $this->companyId)
            ->where('sm.shiftType', 2)
            ->where('sm.shiftID', $shiftId)
            ->get()
            ->last();
    }

    function computeAttendance($curRow){
        $obj = new SMCrossDayOnlyComputation($curRow, $this->companyId);
        $obj->calculate();
        $attId = $curRow->ID;
        $autoId = $curRow->autoId;

        $this->updateData[] = [
            'gracePeriod'=> $curRow->gracePeriod,
            'checkOut'=> $obj->clockOut,
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


        foreach ($this->updateData as $data) {
            DB::table('srp_erp_pay_empattendancereview')
                ->where('ID', $attId)
                ->update($data);

            DB::table('srp_erp_pay_empattendancetemptable')
                ->where('autoID', $autoId)
                ->update([
                    'isUpdated' => 1,
                    'timestamp' => $this->dateTime
                ]);
        }

    }
    function updateUnConfirmAttendance(){

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
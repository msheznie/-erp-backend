<?php

namespace App\Services\hrms\attendance;

use App\enums\shift\Shifts;
use DateTime;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceComputationService
{
    protected $data;
    protected $companyId;
    public $onDutyTime;
    public $offDutyTime;
    public $clockIn;
    public $clockOut;
    public $gracePeriod;
    public $isFlexibleHour;
    public $flexibleHourFrom;
    public $flexibleHourTo;


    //computed values
    public $shiftHours = null;
    public $totalWorkingHours = null;
    public $realTime = 0;
    public $lateHours = 0;
    public $earlyHours = 0;
    public $overTimeHours = 0;
    public $lateFee = 0;
    public $presentAbsentType = '';
    public $cutOfWorkHrsPrvious = '00:00:00';
    public $cutOfWorkHrsNext = '24:00:00';
    public $officialWorkTime = 0;

    public $normalDayData = ['true_false' => 0, 'hours' => 0, 'realTime' => 0];
    public $weekendData = ['true_false' => 0, 'hours' => 0, 'realTime' => 0];
    public $holidayData = ['true_false' => 0, 'hours' => 0, 'realTime' => 0];
    public $calcClockOut = 0;

    //supporting values
    public $isFlexibleHourBaseComputation = false;
    public $isShiftHoursSet = false;
    public $isClockInOutSet = false;
    public $shiftHours_obj;
    public $clockIn_dt;
    public $clockOut_dt;
    public $onDuty_dt;
    public $dayType = 0; //[1=> normalDay, 2=> holiday, 3 => weekend]  

    public $isCrossDay;
    public $crossDayCutOffTime;
    public $uploadType;
    public $clockOutFloorId;


    public function __construct($data, $companyId)
    {
        Log::useFiles(CommonJobService::get_specific_log_file('attendance-clockIn'));

        $this->data = $data;
        $this->companyId = $companyId;
        $this->onDutyTime = trim($data['onDutyTime']);
        $this->offDutyTime = trim($data['offDutyTime']);
        $this->clockIn = trim($data['clock_in']);
        $this->clockOut = trim($data['clock_out']);
        $this->gracePeriod = trim($data['gracePeriod']);
        $this->isFlexibleHour = trim($data['isFlexyHour']);
        $this->flexibleHourFrom = trim($data['flexyHrFrom']);
        $this->flexibleHourTo = trim($data['flexyHrTo']);
        $this->isCrossDay = $data['is_cross_day'];
        $this->crossDayCutOffTime = $data['crossDayCutOffTime'];
    }

    public function execute()
    {
        $this->configDayType();
        $this->confIsFlexibleHourBaseComputation();

        if ($this->dayType == 1) {
            $this->calculateShiftHours();
        }

        if($this->data['shiftType'] == Shifts::ROTA && $this->isCrossDay){
            $this->setRotaShiftClockInAndClockOut();
        }

        $this->calculateWorkedHours();

        if (!$this->isClockInOutSet) {
            $this->otherComputation();

            if ($this->clockIn) {
                $this->lateFeeComputation();
            }

            return false;
        }

        $this->clockOut_dt = new DateTime($this->clockOut);
        $this->onDuty_dt = new DateTime($this->onDutyTime);

        if ($this->isFlexibleHourBaseComputation) {
            // flexible Hour Attendance 
            $this->basedOnFlexibleHoursComputation();
        } else {
            $this->generalComputation();
        }


        $this->otherComputation();

        if ($this->dayType == 1) {
            $this->lateFeeComputation();
        }
    }

    function setRotaShiftClockInAndClockOut(){
        $this->clockIn = '';
        $this->clockOut = '';

        $currentDate = date('Y-m-d', strtotime($this->data['att_date']));
        $nextDate = date('Y-m-d', strtotime($this->data['att_date'] . "+1 day"));
        $currentDateWithCutTime = $currentDate.' '.trim($this->crossDayCutOffTime);
        $nextDateWithCutTime = $nextDate.' '.trim($this->crossDayCutOffTime);

        $attTempRec = DB::table('srp_erp_pay_empattendancetemptable as t')
            ->select('t.autoID', 't.emp_id', 't.attDate', 't.in_out', 't.attTime', 'l.floorID', 't.uploadType')
            ->join("srp_erp_empattendancelocation AS l", function ($join) {
                $join->on("l.deviceID", "=", "t.device_id")
                    ->on("t.empMachineID", "=", "l.empMachineID");
            })
            ->where('t.companyID', $this->companyId)
            ->where('t.emp_id', $this->data['emp_id'])
            ->whereBetween('t.attDate', [$currentDate, $nextDate])
            ->whereBetween('t.attDateTime', [$currentDateWithCutTime, $nextDateWithCutTime])
            ->where('t.isUpdated', 0)
            ->orderBy('t.attDate', 'asc')
            ->orderBy('t.attTime', 'asc')
            ->get();
        $firstRecord = $attTempRec->first();
        $lastRecord = $attTempRec->last();
        $countRecord = $attTempRec->count();

        if(!empty($firstRecord)){
            $this->clockIn = $firstRecord->attTime;
        }

        if($countRecord > 1){
            $this->clockOut = $lastRecord->attTime;
            $this->uploadType = $lastRecord->uploadType;
            $this->clockOutFloorId = $lastRecord->floorID;
        }
    }

    public function calculateShiftHours()
    {
        if (empty($this->onDutyTime) || empty($this->offDutyTime)) {
            return false;
        }

        $this->isShiftHoursSet = true;

        $t1 = new DateTime($this->onDutyTime);
        $t2 = new DateTime($this->offDutyTime);

        if($this->isCrossDay){
            return $this->calculateCrossDayShiftHours($t1, $t2);
        }

        $this->shiftHours_obj = $t1->diff($t2);
        $hours = $this->shiftHours_obj->format('%h');
        $minutes = $this->shiftHours_obj->format('%i');

        $this->shiftHours = ($hours * 60) + $minutes;
    }

    public function calculateCrossDayShiftHours($onDuty, $offDuty){

        $nextDayOnTime = new DateTime($this->cutOfWorkHrsPrvious);
        $currentDayOffTime = new DateTime($this->cutOfWorkHrsNext);

        $currentDayShiftHr = $onDuty->diff($currentDayOffTime);
        $nextDayShiftHr = $nextDayOnTime->diff($offDuty);

        $hours = $currentDayShiftHr->h + $nextDayShiftHr->h;
        $minutes = $currentDayShiftHr->m + $nextDayShiftHr->m;

        $this->shiftHours = ($hours * 60) + $minutes;
    }

    public function calculateWorkedHours()
    {
        if (empty($this->clockIn) && empty($this->clockOut)) {

            $this->presentAbsentType = (empty($this->data['leaveMasterID']))
                ? 4 //absent
                : 5; //on leave

            if($this->dayType == 2){ 
                $this->presentAbsentType = 8;// holiday
            } 

            if($this->dayType == 3){ 
                $this->presentAbsentType = 9;//weekend
            }
            
            return false;
        }

        $this->presentAbsentType = 1;

        if ($this->isFlexibleHourBaseComputation) {
            $this->FlxCalculateActualWorkingHours();
            return;
        }

        if ($this->data['shiftType'] == Shifts::OPEN) { //if open shift
            $this->openShiftCalculateWorkedHours();
            return;
        }

        if ($this->data['shiftType'] == Shifts::ROTA && $this->isCrossDay) { //if rota shift
            $this->rotaShiftCalculateWorkedHours();
            return;
        }

        if (!empty($this->clockIn) && empty($this->clockOut)) {
            $this->lateHoursComputation();
            return false;
        }

        $this->isClockInOutSet = true; 

        $t1 = ($this->isShiftHoursSet && ($this->offDutyTime <= $this->clockOut))
            ? new DateTime($this->offDutyTime)
            : new DateTime($this->clockOut);

        $t2 = ($this->isShiftHoursSet && ($this->onDutyTime >= $this->clockIn))
            ? new DateTime($this->onDutyTime)
            : new DateTime($this->clockIn);

        $totWorkingHours_obj = $t1->diff($t2);
        $hours = $totWorkingHours_obj->format('%h');
        $minutes = $totWorkingHours_obj->format('%i');
        $this->totalWorkingHours = ($hours * 60) + $minutes;
        
        $this->calculateRealTime();
        $this->calculateOfficialWorkTime();
    }

    public function calculateRealTime(){
        if ($this->totalWorkingHours && $this->shiftHours) {
            $realtime = $this->shiftHours / $this->totalWorkingHours;
            $this->realTime = round($realtime, 1);
        }
    }

    public function calculateOfficialWorkTime(){
        if (empty($this->onDutyTime) || empty($this->offDutyTime)) {
            return false;
        }

        $t3 = ($this->isShiftHoursSet && ($this->offDutyTime <= $this->clockOut))
            ? new DateTime($this->offDutyTime)
            : new DateTime($this->clockOut);

        $t4 = ($this->isShiftHoursSet && ($this->onDutyTime >= $this->clockIn))
            ? new DateTime($this->onDutyTime)
            : new DateTime($this->clockIn);

        $officialWorkTimeObj = $t3->diff($t4);
        $hours = $officialWorkTimeObj->format('%h');
        $minutes = $officialWorkTimeObj->format('%i');
        $this->officialWorkTime = ($hours * 60) + $minutes;    
    }

    public function openShiftCalculateWorkedHours()
    {
        $this->otherComputation();  
        $t1 = new DateTime($this->clockOut);
        $t2 = new DateTime($this->clockIn);

        $totWorkingHours_obj = $t1->diff($t2);
        $hours = $totWorkingHours_obj->format('%h');
        $minutes = $totWorkingHours_obj->format('%i');
        //$this->totalWorkingHours = ($hours * 60) + $minutes;
        $this->totalWorkingHours = $this->calculateOpenShiftActualWorkingHrs();
        
        $shiftHours = ($this->data['shiftType'] == 1)? $this->data['workingHour']: $this->shiftHours;
        $shiftHours = (empty($shiftHours))? 0: $shiftHours;  
        $this->officialWorkTime = ($shiftHours > $this->totalWorkingHours) ? $this->totalWorkingHours : $shiftHours;

        if($this->holidayData['true_false'] == 1 || $this->presentAbsentType == 5){ 
            $this->officialWorkTime = 0;
        }  

        if ($this->totalWorkingHours && $this->data['workingHour']) {
            $realtime = $this->data['workingHour'] / $this->totalWorkingHours;
            $this->realTime = round($realtime, 1);

            $this->openShiftCommonComputations();
        }
    }

    public function openShiftCommonComputations()
    {
        if ($this->dayType != 1) {
            return; //if holiday or weekend no need to compute the OT or shortage (early-out) hours
        }

        if ($this->totalWorkingHours < $this->data['workingHour']) {
            //compute shortage
            $this->earlyHours = $this->data['workingHour'] - $this->totalWorkingHours;
        } else {
            //compute OT
            $this->overTimeHours = $this->totalWorkingHours - $this->data['workingHour'];
        }
    }

    public function FlxCalculateActualWorkingHours()
    {

        if (!empty($this->clockIn) && empty($this->clockOut)) {
            $this->flxLateHourComputation();
            return false;
        }

        $this->isClockInOutSet = true;

        $t1 = new DateTime($this->clockOut);
        $t2 = ($this->isShiftHoursSet && ($this->flexibleHourFrom >= $this->clockIn))
            ? new DateTime($this->flexibleHourFrom)
            : new DateTime($this->clockIn);

        $totWorkingHours_obj = $t1->diff($t2);
        $hours = $totWorkingHours_obj->format('%h');
        $minutes = $totWorkingHours_obj->format('%i');
        $this->totalWorkingHours = ($hours * 60) + $minutes;

        $this->calculateRealTime();
        $this->calculateOfficialWorkTime();
    }

    public function rotaShiftCalculateWorkedHours()
    {
        if($this->isFlexibleHourBaseComputation){
            return $this->basedOnFlexibleHoursComputation();
        }

        $this->otherComputation();
        $this->calculateIsCrossDayRotaShiftActualWorkingHrs();

        $this->calculateOfficialWorkTime();
        if($this->holidayData['true_false'] == 1 || $this->presentAbsentType == 5){
            $this->officialWorkTime = 0;
        }

        if ($this->totalWorkingHours && $this->shiftHours) {
            $this->rotaShiftCommonComputations();
        }

        $this->calculateRealTime();
        $this->lateHoursComputation();

    }

    function rotaShiftCommonComputations(){

        if ($this->dayType != 1) {
            return; //if holiday or weekend no need to compute the OT or shortage (early-out) hours
        }

        if ($this->totalWorkingHours < $this->shiftHours) {
            //compute shortage
            $this->earlyHours = $this->shiftHours - $this->totalWorkingHours;

        } else {
            //compute OT
            $this->overTimeHours = $this->totalWorkingHours - $this->shiftHours;

        }
    }

    function calculateIsCrossDayRotaShiftActualWorkingHrs()
    {

        $t1 = ($this->isShiftHoursSet && ($this->offDutyTime <= $this->clockOut))
            ? new DateTime($this->offDutyTime)
            : new DateTime($this->clockOut);

        $t2 = ($this->isShiftHoursSet && ($this->onDutyTime >= $this->clockIn))
            ? new DateTime($this->onDutyTime)
            : new DateTime($this->clockIn);

        $currentDayOffTime = new DateTime($this->cutOfWorkHrsNext);


        $currentDayDifference = $currentDayOffTime->diff($t2);


        $nextDayClockIn = new DateTime($this->cutOfWorkHrsPrvious);
        $nextDayDifference = $t1->diff($nextDayClockIn);
        $hours = $nextDayDifference->format('%h')+$currentDayDifference->format('%h');
        $minutes = $nextDayDifference->format('%i')+$currentDayDifference->format('%i');
        $this->totalWorkingHours =  ($hours * 60) + $minutes;

    }

    public function generalComputation()
    {

        $this->lateHoursComputation();

        $clockIn_dt = new DateTime($this->clockIn);
        $onDuty_dt = new DateTime($this->onDutyTime);

        $clockIn_dt_temp = ($this->clockIn >= $this->onDutyTime)
            ? $clockIn_dt->format('H:i:s')
            : $onDuty_dt->format('H:i:s');

        $clockIn_dt_temp2 = $clockIn_dt_temp;
        $clockIn_dt_temp = new DateTime($clockIn_dt_temp);
        $clockIn_dt_temp2 = new DateTime($clockIn_dt_temp2);

        $clockOut_dt_ot = $this->clockOut_dt;

        $this->earlyHourComputation($clockIn_dt_temp, $this->clockOut_dt);
        $this->overTimeComputation($clockIn_dt_temp2, $clockOut_dt_ot);
    }

    public function basedOnFlexibleHoursComputation()
    {
        $status = $this->flxValidations();
        if (!$status) {
            return false;
        }

        $flexibleHrFrom_dt = new DateTime($this->flexibleHourFrom);
        $this->gracePeriod = 0;
        
        $clockIn_dt = new DateTime($this->clockIn);
        
        if ($clockIn_dt->format('H:i:s') < $flexibleHrFrom_dt->format('H:i:s')) {
            $clockIn_dt = $flexibleHrFrom_dt;
            $this->clockIn = $this->flexibleHourFrom;
        }


        $this->flxLateHourComputation();
        $this->earlyHourComputation($clockIn_dt, $this->clockOut_dt);
        $this->overTimeComputation($clockIn_dt, $this->clockOut_dt);
    }

    private function getOnDutyTempTime()
    {
        $minutesToAdd = $this->gracePeriod;

        $tempOnDuty_dt = new DateTime($this->onDutyTime);
        return $tempOnDuty_dt->modify("+{$minutesToAdd} minutes");
    }

    function lateHoursComputation()
    { //late hour computation

        if (!$this->isShiftHoursSet) {
            return false;
        }

        if ($this->dayType != 1) {
            return false;
        }

        $tempOnDuty_dt = $this->getOnDutyTempTime();
        $clockIn_dt = new DateTime($this->clockIn);

        if ($clockIn_dt->format('H:i:s') > $tempOnDuty_dt->format('H:i:s')) {
            $this->presentAbsentType = 2; //presented but late

            $interval = $clockIn_dt->diff($tempOnDuty_dt);

            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
            $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
            $this->lateHours = $hours * 60 + $minutes;
        }
    }

    // late hour computation based on flexible hour
    public function flxLateHourComputation()
    {
        if ($this->dayType != 1) {
            return false;
        }

        if (!$this->isShiftHoursSet) {
            return false;
        }

        $clockIn_dt = new DateTime($this->clockIn);
        $flxHrTo_dt = new DateTime($this->flexibleHourTo);


        if ($clockIn_dt->format('H:i:s') > $flxHrTo_dt->format('H:i:s')) {
            $this->presentAbsentType = 2; //presented but late

            $interval = $clockIn_dt->diff($flxHrTo_dt);
            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
            $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
            $this->lateHours = $hours * 60 + $minutes;
        }
    }

    // early hour computation  [ same function for flexible hour and general ]
    public function earlyHourComputation($clockIn_dt, $clockOut_dt)
    {
        if ($this->dayType != 1) {
            return; //if holiday or weekend no need to compute the earl out hours
        }
 
        if (!$this->isShiftHoursSet) {
            return false;
        }

        $this->calcClockOut = clone $clockIn_dt;
        $this->calcClockOut->modify("+{$this->shiftHours} minutes");

        if ($this->calcClockOut > $clockOut_dt) {
            $interval = $this->calcClockOut->diff($clockOut_dt);
            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
            $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
            $this->earlyHours = $hours * 60 + $minutes;
        }
    }

    // OT computation  [ same function for flexible hour and general ]
    public function overTimeComputation($clockIn_dt_ot, $clockOut_dt_ot)
    {
        if ($this->dayType != 1) {
            return; //if holiday or weekend no need to compute the OT hours
        }

        if (!$this->isShiftHoursSet) {
            return; //if shift hours not defined
        }

        if ($clockOut_dt_ot <= $this->calcClockOut) {
            return true;
        }

        $currentDate = date('Y-m-d');

        $workingHours_obj = $clockIn_dt_ot->diff($clockOut_dt_ot);
        $totW = new DateTime($workingHours_obj->format("{$currentDate} %h:%i:%s"));
        $actW = new DateTime($this->shiftHours_obj->format("{$currentDate} %h:%i:%s"));

        if ($totW->format('h:i') > $actW->format('h:i')) {
            $overTime_obj = $actW->diff($totW);
            $hours = ($overTime_obj->format('%h') != 0) ? $overTime_obj->format('%h') : 0;
            $minutes = ($overTime_obj->format('%i') != 0) ? $overTime_obj->format('%i') : 0;
            $this->overTimeHours = $hours * 60 + $minutes;
        }
    }

    // flexible hour validation
    public function flxValidations()
    {
        if (empty($this->flexibleHourFrom) ) {
            $msg = 'Flexible hour from is required';
            Log::error($msg . $this->log_suffix(__LINE__));
            return false;
        }

        if (empty($this->flexibleHourTo) ) {
            $msg = 'Flexible hour to is required';
            Log::error($msg . $this->log_suffix(__LINE__));
            return false;
        }

        $onDuty_dt = new DateTime($this->onDutyTime);
        $flexibleHrFrom_dt = new DateTime($this->flexibleHourFrom);
        $flexibleHrTo_dt = new DateTime($this->flexibleHourTo);

        if ($flexibleHrFrom_dt->format('H:i:s') > $onDuty_dt->format('H:i:s')) {
            $msg = 'Flexible hour from cannot be greater than on duty time';
            Log::error($msg . $this->log_suffix(__LINE__));

            return false;
        }

        if ($flexibleHrTo_dt->format('H:i:s') < $onDuty_dt->format('H:i:s')) {
            $msg = 'Flexible hour to cannot be less than on duty time';
            Log::error($msg . $this->log_suffix(__LINE__));
            return false;
        }
        return true;
    }

    // Calculation for late Fee
    public function lateFeeComputation()
    {

        if (empty($this->lateHours)) {
            return false;
        }

        $empId = $this->data['emp_id'];
        $attendanceDate = $this->data['att_date'];


        $obj = new LateFeeComputationService($empId, $attendanceDate, $this->companyId);
        $amountForPerMinute = $obj->compute();

        $this->lateFee = ($amountForPerMinute > 0)
            ? $this->lateHours * $amountForPerMinute
            : 0;
    }

    function configDayType()
    {
        if ($this->data['isHoliday'] == 1) {
            $this->dayType = 2; // holiday
        } else if ($this->data['isWeekend'] == 1) {
            $this->dayType = 3; //weekend
        } else {
            $this->dayType = 1; //normal day
        }
    }

    function confIsFlexibleHourBaseComputation()
    {
        if ($this->isFlexibleHour == 1 && $this->flexibleHourFrom != null) {
            $this->isFlexibleHourBaseComputation = true;
            $this->gracePeriod = 0;
        }
    }

    function otherComputation()
    {

        if ($this->dayType == 3) {
            $this->overTimeHours = $this->totalWorkingHours;
            $this->weekendData = [
                'true_false' => 1, 'hours' => $this->totalWorkingHours, 'realTime' => $this->realTime
            ];
        }

        if ($this->dayType == 2) {
            $this->overTimeHours = $this->totalWorkingHours;
            $this->holidayData = [
                'true_false' => 1, 'hours' => $this->totalWorkingHours, 'realTime' => $this->realTime
            ];
        }

        if ($this->dayType == 1) {
            $this->normalDayData = [
                'true_false' => 1, 'hours' => $this->overTimeHours, 'realTime' => $this->realTime
            ];
        }


        if ($this->data['leaveHalfDay'] == 1) {
            $this->presentAbsentType = 7;
        }

        if ($this->clockIn == '00:00:00' || empty($this->clockIn)) {
            $this->clockIn = null;
        }

        if ($this->clockOut == '00:00:00' || empty($this->clockOut)) {
            $this->clockOut = null;
        }
    }

    function log_suffix($line_no): string
    {
        $msg = "( $this->data['emp_id'] |  $this->data['ECode'] | $this->data['Ename2'] )";
        $msg .= " | companyId: $this->companyId \t on file:  " . __CLASS__ . " \tline no : {$line_no}";
        return $msg;
    }

    function calculateOpenShiftActualWorkingHrs()
    {
        $attTempRec = DB::table('srp_erp_pay_empattendancetemptable')
            ->select('autoID', 'emp_id', 'attDate', 'in_out', 'attTime')
            ->where('companyID', $this->data['company_id'])
            ->where('emp_id', $this->data['emp_id'])
            ->whereDate('attDate', $this->data['att_date'])
            ->orderBy("autoID", "asc")
            ->get()->toArray();
 
        $actualWorkingHrs = 0;
        $previousDate = date('Y-m-d', strtotime($this->data['att_date'] . "-1 day"));
        $nextDate = date('Y-m-d', strtotime($this->data['att_date'] . "+1 day"));

        if (!empty($attTempRec)) { 
            $occurrences = count($attTempRec);
            $lastIndex = $occurrences - 1;
 
            foreach ($attTempRec as $key => $val) {
                 
                if ($key == 0 && $val->in_out == 2) { 
                    $previouseLastRecord = $this->previouseLastRecord($previousDate, $val->attTime);
                    $actualWorkingHrs += $previouseLastRecord;
                    continue;
                }
               

                if ($key ==  $lastIndex && $val->in_out == 1) { 
                    $nextDayFirstRecord = $this->nextDayFirstRecord($nextDate, $val->attTime);
                    $actualWorkingHrs += $nextDayFirstRecord; 
                    continue;
                }

                if ($val->in_out == 2) {
                    continue;
                }

                
                $nextKey = ($key + 1); 
                if (!array_key_exists($nextKey, $attTempRec)) {  
                    return $actualWorkingHrs;
                }

                if ($attTempRec[$nextKey]->in_out == 1) {
                    continue;
                }


                $t1 = new DateTime($attTempRec[$key]->attTime);
                $t2 = new DateTime($attTempRec[$nextKey]->attTime);
 

                $difference = $t1->diff($t2);
                $hours = $difference->format('%h');
                $minutes = $difference->format('%i');
                $actualWorkingHrs += $hours * 60 + $minutes;  
            }
 
        }
        return $actualWorkingHrs;
    }

    public function previouseLastRecord($previousDate, $attTime)
    {
       $previouseLastRecordTotal = 0; 
       $previouseLastRecord = $this->getAttendancetempRecord($previousDate,'DESC');
        if (!empty($previouseLastRecord) && $previouseLastRecord->in_out==1) {
            $t1 = new DateTime($this->cutOfWorkHrsPrvious);
            $t2 = new DateTime($attTime);
            $difference = $t1->diff($t2);
            $hours = $difference->format('%h');
            $minutes = $difference->format('%i');
            $previouseLastRecordTotal  += $hours * 60 + $minutes;
        }
        return $previouseLastRecordTotal;
    }

    public function nextDayFirstRecord($nextDate, $attTime)
    { 
        $nextDayFirstRecordTotal = 0; 

        $nextDayFirstRecord =  $this->getAttendancetempRecord($nextDate,'ASC');
        if (!empty($nextDayFirstRecord) && $nextDayFirstRecord->in_out==2) {
            $t1 = new DateTime($attTime);
            $t2 = new DateTime($this->cutOfWorkHrsNext);
            $difference = $t1->diff($t2);
            $hours = $difference->format('%h');
            $minutes = $difference->format('%i');
            $nextDayFirstRecordTotal += $hours * 60 + $minutes; 
        }

        return $nextDayFirstRecordTotal;
    }

    public function getAttendancetempRecord($date,$orderBy){ 
        $data = DB::table('srp_erp_pay_empattendancetemptable')
        ->select('autoID', 'emp_id', 'attDate', 'in_out', 'attTime')
        ->where('companyID', $this->data['company_id'])
        ->where('emp_id', $this->data['emp_id'])
        ->where('attDate', $date) 
        ->orderBy("autoID", $orderBy)
        ->first();

        return $data;
    } 
}
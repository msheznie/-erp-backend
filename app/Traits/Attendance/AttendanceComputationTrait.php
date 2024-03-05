<?php
namespace App\Traits\Attendance;
use App\enums\attendance\AbsentType;
use App\enums\attendance\AttDayType;
use App\Services\hrms\attendance\AbsentDayDeductionService;
use App\Services\hrms\attendance\LateFeeComputationService;
use Illuminate\Support\Facades\Log;
use DateTime;

trait AttendanceComputationTrait{

    public function configDayType(){

        if ($this->data['isHoliday'] == AttDayType::NORMAL_DAY) {
            $this->dayType = AttDayType::HOLIDAY;
        } else if ($this->data['isWeekend'] == AttDayType::NORMAL_DAY) {
            $this->dayType = AttDayType::WEEKEND;
        } else {
            $this->dayType = AttDayType::NORMAL_DAY;
        }

    }

    public function configClockinClockoutSet()
    {
        if (!empty($this->clockIn) && !empty($this->clockOut)) {
            $this->isClockInOutSet = true;
        }
    }

    public function calculateShiftHours()
    {
        if (empty($this->onDutyTime) || empty($this->offDutyTime)) {
            return false;
        }

        $this->isShiftHoursSet = true;

        $shiftOnDutyTimeObj = new DateTime($this->onDutyTime);
        $shiftOffDutyTimeObj = new DateTime($this->offDutyTime);
        $this->shiftHoursObj = $shiftOnDutyTimeObj->diff($shiftOffDutyTimeObj);
        $hours = $this->shiftHoursObj->format('%h');
        $minutes = $this->shiftHoursObj->format('%i');

        $this->shiftHours = ($hours * 60) + $minutes;
    }

    public function configPresentAbsentType()
    {
        if ($this->isClockInOutSet) {
            $this->presentAbsentType = AbsentType::ON_TIME;
            return;
        }

        $this->presentAbsentType = (empty($this->data['leaveMasterID']))
            ? AbsentType::ABSENT
            : AbsentType::ON_LEAVE;

        if ($this->data['leaveHalfDay'] == 1) {
            $this->presentAbsentType = AbsentType::HALF_DAY;
        }

        if ($this->dayType == AttDayType::HOLIDAY) {
            $this->presentAbsentType = AbsentType::HOLIDAY;
        }

        if ($this->dayType == AttDayType::WEEKEND) {
            $this->presentAbsentType = AbsentType::WEEKEND;
        }
    }

    function confIsFlexibleHourBaseComputation()
    {
        if ($this->isFlexibleHour == 1 && $this->flexibleHourFrom != null) {
            $this->isFlexibleHourBaseComputation = true;
            $this->gracePeriod = 0;
        }
    }

    public function flxGeneralComputation(){
        if (!$this->isShiftHoursSet || $this->dayType != AttDayType::NORMAL_DAY) {
            return false;
        }

        $this->flxValidations();
        $this->flxLateHourComputation();

        $flexibleHrFromDt = new DateTime($this->flexibleHourFrom);
        $this->gracePeriod = 0;
        $clockInDt = new DateTime($this->clockIn);

        if ($clockInDt->format('H:i:s') < $flexibleHrFromDt->format('H:i:s')) {
            $clockInDt = $flexibleHrFromDt;
            if($this->isClockInOutSet){
                $this->clockIn = $this->flexibleHourFrom;
            }
        }

        $this->earlyHourComputation($clockInDt, $this->clockOutDtObj);
        $this->overTimeComputation($clockInDt, $this->clockOutDtObj);
    }

    public function generalComputation()
    {
        if (!$this->isShiftHoursSet || $this->dayType != AttDayType::NORMAL_DAY) {
            return false;
        }

        $this->lateHoursComputation();

        if (!$this->isClockInOutSet){
            return false;
        }

        $clockInDt = new DateTime($this->clockIn);
        $onDutyDt = new DateTime($this->onDutyTime);

        $clockInDtTemp = ($this->clockIn >= $this->onDutyTime)
            ? $clockInDt->format('H:i:s')
            : $onDutyDt->format('H:i:s');

        $clockInDtTemp2 = $clockInDtTemp;
        $clockInDtTemp = new DateTime($clockInDtTemp);
        $clockInDtTemp2 = new DateTime($clockInDtTemp2);

        $clockOutDtOt = $this->clockOutDtObj;

        $this->earlyHourComputation($clockInDtTemp, $this->clockOutDtObj);
        $this->overTimeComputation($clockInDtTemp2, $clockOutDtOt);
    }
    public function flxCalculateActualTime()
    {

        if ($this->presentAbsentType != AbsentType::ON_TIME || !$this->isClockInOutSet) {
            return false;
        }

        $t1 = new DateTime($this->clockOut);
        $t2 = ($this->isShiftHoursSet && ($this->flexibleHourFrom >= $this->clockIn))
            ? new DateTime($this->flexibleHourFrom)
            : new DateTime($this->clockIn);

        $totWorkingHours_obj = $t1->diff($t2);
        $hours = $totWorkingHours_obj->format('%h');
        $minutes = $totWorkingHours_obj->format('%i');
        $this->actualWorkingHours = ($hours * 60) + $minutes;
    }

    public function calculateActualTime()
    {
        if ($this->presentAbsentType != AbsentType::ON_TIME || !$this->isClockInOutSet) {
            return false;
        }

        $t1 = new DateTime($this->clockOut);
        $t2 = ($this->isShiftHoursSet && $this->onDutyTime >= $this->clockIn)
            ? new DateTime($this->onDutyTime)
            : new DateTime($this->clockIn);

        $totWorkingHours_obj = $t1->diff($t2);
        $hours = $totWorkingHours_obj->format('%h');
        $minutes = $totWorkingHours_obj->format('%i');
        $this->actualWorkingHours = ($hours * 60) + $minutes;
    }

    function otherComputation()
    {

        if ($this->dayType == AttDayType::WEEKEND) {
            $this->overTimeHours = $this->actualWorkingHours;
            $this->weekendData = [
                'true_false' => 1, 'hours' => $this->actualWorkingHours, 'realTime' => $this->realTime
            ];
        }

        if ($this->dayType == AttDayType::HOLIDAY) {
            $this->overTimeHours = $this->actualWorkingHours;
            $this->holidayData = [
                'true_false' => 1, 'hours' => $this->actualWorkingHours, 'realTime' => $this->realTime
            ];
        }

        if ($this->dayType == AttDayType::NORMAL_DAY) {
            $this->normalDayData = [
                'true_false' => 1, 'hours' => $this->overTimeHours, 'realTime' => $this->realTime
            ];
        }


        if ($this->data['leaveHalfDay'] == 1) {
            $this->presentAbsentType =AbsentType::HALF_DAY;
        }

        if ($this->clockIn == '00:00:00' || empty($this->clockIn)) {
            $this->clockIn = null;
        }

        if ($this->clockOut == '00:00:00' || empty($this->clockOut)) {
            $this->clockOut = null;
        }
    }

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

    public function flxValidations(){
        if (empty($this->flexibleHourFrom) ) {
            $msg = 'Flexible hour from is required';
            Log::error($msg . $this->log_suffix(__LINE__));
            return false;
        }

        if (empty($this->flexibleHourTo)) {
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
    }

    function log_suffix($line_no): string
    {
        $msg = "( $this->data['emp_id'] |  $this->data['ECode'] | $this->data['Ename2'] )";
        $msg .= " | companyId: $this->companyId \t on file:  " . __CLASS__ . " \tline no : {$line_no}";
        return $msg;
    }

    function lateHoursComputation(){

        if (!$this->isShiftHoursSet) {
            return false;
        }

        if ($this->dayType != AttDayType::NORMAL_DAY) {
            return false;
        }

        $tempOnDutyDtObj = $this->getOnDutyTempTime();
        $clockInDtObj = new DateTime($this->clockIn);

        if ($clockInDtObj->format('H:i:s') > $tempOnDutyDtObj->format('H:i:s')) {
            $this->presentAbsentType = AbsentType::LATE;

            $interval = $clockInDtObj->diff($tempOnDutyDtObj);
            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
            $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
            $this->lateHours = $hours * 60 + $minutes;
        }
    }

    private function getOnDutyTempTime(){
        $minutesToAdd = $this->gracePeriod;

        $tempOnDuty_dt = new DateTime($this->onDutyTime);
        return $tempOnDuty_dt->modify("+{$minutesToAdd} minutes");
    }

    public function calculateRealTime(){

        if (!$this->isShiftHoursSet || !$this->isClockInOutSet) {
            return false;
        }

        if ($this->actualWorkingHours && $this->shiftHours) {
            $realtime = $this->shiftHours / $this->actualWorkingHours;
            $this->realTime = round($realtime, 1);
        }
    }

    public function calculateOfficialWorkTime(){
        if(!$this->isShiftHoursSet || !$this->isClockInOutSet ){
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

    public function earlyHourComputation($clockInDt, $clockOutDt){

        if (!$this->isClockInOutSet) {
            return false;
        }
        $this->calcClockOut = clone $clockInDt;
        $this->calcClockOut->modify("+{$this->shiftHours} minutes");

        if ($this->gracePeriod > 0) {
            $this->calculateEarlyHourBaseOnGracePeriod();
        } else {
            $this->calculateGeneralEarlyHour($clockOutDt);
        }
    }

    function calculateGeneralEarlyHour($clockOutDt){

        if ($this->calcClockOut > $clockOutDt) {
            if(!empty($clockOutDt)){
                $interval = $clockOutDt->diff($this->calcClockOut);
                $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
                $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
                $this->earlyHours = $hours * 60 + $minutes;
            }
        }
    }

    function calculateEarlyHourBaseOnGracePeriod(){

        $calcClockOutMinutes = $this->shiftHours - $this->actualWorkingHours;

        $calcClockOutDtmObj = new DateTime('@0');
        $calcClockOutDtmObj->modify('+' . $calcClockOutMinutes . ' minutes');

        $gracePeriodDtmObj = new DateTime('@0');
        $gracePeriodDtmObj->modify('+' . $this->gracePeriod . ' minutes');

        if($calcClockOutDtmObj  > $gracePeriodDtmObj){
            $interval = $calcClockOutDtmObj->diff($gracePeriodDtmObj);
            $hours = ($interval->h != 0) ? $interval->h : 0;
            $minutes = ($interval->i != 0) ? $interval->i : 0;
            $this->earlyHours = $hours * 60 + $minutes;
        }
    }

    public function overTimeComputation($clockInDtOT, $clockOutDtOT)
    {

        if (!$this->isClockInOutSet) {
            return false;
        }


        if ($clockOutDtOT <= $this->calcClockOut) {
            return true;
        }

        $currentDate = date('Y-m-d');

        $workingHours_obj = $clockInDtOT->diff($clockOutDtOT);
        $totW = new DateTime($workingHours_obj->format("{$currentDate} %h:%i:%s"));
        $actW = new DateTime($this->shiftHoursObj->format("{$currentDate} %h:%i:%s"));

        if ($this->isCrossDay) {
            $this->rotaCrossDayOverTimeComputation($actW, $totW);
        } else {
            $this->generalOvertimeComputation($actW, $totW);
        }

    }

    function rotaCrossDayOverTimeComputation($actW, $totW){
        if ($totW > $actW) {
            $overTimeObj = $actW->diff($totW);
            $hours = ($overTimeObj->format('%h') != 0) ? $overTimeObj->format('%h') : 0;
            $minutes = ($overTimeObj->format('%i') != 0) ? $overTimeObj->format('%i') : 0;
            $this->overTimeHours = $hours * 60 + $minutes;
        }
    }

    function generalOvertimeComputation($actW, $totW){
        if ($totW->format('h:i') > $actW->format('h:i')) {
            $overTimeObj = $actW->diff($totW);
            $hours = ($overTimeObj->format('%h') != 0) ? $overTimeObj->format('%h') : 0;
            $minutes = ($overTimeObj->format('%i') != 0) ? $overTimeObj->format('%i') : 0;
            $this->overTimeHours = $hours * 60 + $minutes;
        }
    }

    public function flxLateHourComputation()
    {

        if (!$this->clockIn) {
            return false;
        }

        $clockInDtObj = new DateTime($this->clockIn);
        $flxHrToDtObj = new DateTime($this->flexibleHourTo);


        if ($clockInDtObj->format('H:i:s') > $flxHrToDtObj->format('H:i:s')) {
            $this->presentAbsentType = AbsentType::LATE;

            $interval = $clockInDtObj->diff($flxHrToDtObj);
            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
            $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
            $this->lateHours = $hours * 60 + $minutes;
        }
    }

    public function computeAbsentDeductionAmount(){

        if($this->presentAbsentType != AbsentType::ABSENT){
            return;
        }

        $abDayDeductionCalc = new AbsentDayDeductionService($this->empId, $this->data['att_date'], $this->companyId);
        $abDayDeductionCalc = $abDayDeductionCalc->process();

        if (array_key_exists('pay', $abDayDeductionCalc)) {
            $this->absDedAmount= $abDayDeductionCalc['pay']['trAmount'];
            $this->salCatId = $abDayDeductionCalc['pay']['salaryCategoryId'];
        }

        if (array_key_exists('nonPay', $abDayDeductionCalc)) {
            $this->absDedNonAmount = $abDayDeductionCalc['nonPay']['trAmount'];
            $this->nonSalCatId = $abDayDeductionCalc['nonPay']['salaryCategoryId'];
        }
    }
}
<?php
namespace App\Services\hrms\attendance\computation;
use App\enums\attendance\AbsentType;
use App\enums\attendance\AttDayType;
use App\Services\hrms\attendance\AbsentDayDeductionService;
use App\Traits\Attendance\AttendanceComputationTrait;
use DateTime;
class SMCrossDayOnlyComputation{
    use AttendanceComputationTrait;
    public $crossDayCutOffTime;


    public $clockIn;
    public $clockOut;
    public $clockInDate;
    public $clockInDateTime;
    public $clockOutDate;
    public $clockOutDateTime;
    public $clockOutDtObj;
    public $clockInDtObj;
    public $onDutyTime;
    public $offDutyTime;
    public $onDutyDateTime;
    public $offDutyDateTime;
    public $shiftHoursObj;
    public $shiftHours;
    public $gracePeriod;


    public $companyId;
    public $empId;
    public $dayType;
    public $presentAbsentType;

    public $absDedAmount;
    public $absDedNonAmount;
    public $salCatId;
    public $nonSalCatId;
    public $isClockInOutSet;
    public $isGracePeriodSet;
    public $isFlexibleHourBaseComputation;
    public $isFlexibleHour;
    public $isShiftHoursSet = false;
    public $flexibleHourFrom;
    public $flexibleHourTo;

    public $actualWorkingHours = 0;
    public $officialWorkTime = 0;

    public $realTime = 0;
    public $lateHours = 0;
    public $earlyHours = 0;
    public $overTimeHours = 0;
    public $lateFee = 0;

    public $isCrossDay;

    public $data = [];


    public $normalDayData = ['true_false' => 0, 'hours' => 0, 'realTime' => 0];
    public $weekendData = ['true_false' => 0, 'hours' => 0, 'realTime' => 0];
    public $holidayData = ['true_false' => 0, 'hours' => 0, 'realTime' => 0];

    public function __construct($data, $companyId){
        $this->data = $data;

        $this->companyId = $companyId;
        $this->empId = $data->empID;
        $this->crossDayCutOffTime = $data->crossDayCutOffTime;
        $this->clockIn = $data->checkIn;
        $this->clockOut = $data->checkOut;
        $this->gracePeriod = $data->gracePeriod;
        $this->isFlexibleHour = $data->isFlexibleHour;
        $this->isGracePeriodSet = ($this->gracePeriod != 0) ? 1 : 0;
        $this->clockInDate = $data->attendanceDate;
        $this->clockOutDate = $data->clockOutDate;
        $this->flexibleHourFrom = $data->flexibleHourFrom;
        $this->flexibleHourTo = $data->flexibleHourTo;
        $this->clockInDateTime = $this->clockInDate .' '.$this->clockIn;
        $this->clockOutDateTime = $this->clockOutDate .' '.$this->clockOut;
        $this->isClockInOutSet = $data->isClockInOutSet;

        if($this->isClockInOutSet){
            $this->clockOutDtObj = new DateTime($this->clockOutDateTime);
            $this->clockInDtObj = new DateTime($this->clockInDateTime);
        }


        $this->onDutyTime = $data->onDuty;
        $this->offDutyTime = $data->offDuty;

        if (!empty($this->onDutyTime) && !empty($this->offDutyTime)) {
            $this->isShiftHoursSet = true;
        }

        $this->isCrossDay = $data->is_cross_day;

        $this->absDedAmount= 0;
        $this->salCatId = null;
        $this->absDedNonAmount =0;
        $this->nonSalCatId = null;


    }

    function calculate(){
        $this->configCrossDayType();

        if ($this->dayType == AttDayType::NORMAL_DAY) {
            $this->calculateRotaShiftHours();
        }

        $this->configCrossDayPresentAbsentType();

        if (!$this->isClockInOutSet && !in_array($this->dayType, [AttDayType::HOLIDAY, AttDayType::WEEKEND])) {
            $this->computeCrossDayAbsentDeductionAmount();
        }

        $this->confIsFlexibleHourBaseComputation();

        if ($this->isFlexibleHourBaseComputation) {
           $this->calculateCrossDayFlexyActualWorkingHour();
        }else{
            $this->rotaShiftCalculateActualWorkedHours();
        }

        $this->calculateRealTime();

        $this->calculateRotaShiftOfficialWorkHours();

        if($this->isFlexibleHourBaseComputation){
            $this->flxRotaGeneralComputation();
        }else{
            $this->rotaGeneralComputation();
        }

        $this->otherRotaComputation();
        $this->lateFeeComputation();
    }

    function configCrossDayType(){
        if ($this->data->isHoliday == 1) {
            $this->dayType = AttDayType::HOLIDAY;
        } else if ($this->data->isWeekEndDay == 1) {
            $this->dayType = AttDayType::WEEKEND;
        } else {
            $this->dayType = AttDayType::NORMAL_DAY;
        }
    }

    public function calculateRotaShiftHours()
    {
        if (empty($this->onDutyTime) || empty($this->offDutyTime)) {
            return false;
        }

        $this->isShiftHoursSet = true;

        $attDate = date('Y-m-d', strtotime($this->clockInDate));
        $attNextDate = date('Y-m-d', strtotime($this->clockInDate . "+1 day"));
        $this->onDutyDateTime = new DateTime($attDate . ' ' . $this->onDutyTime);
        $this->offDutyDateTime = new DateTime($attNextDate . ' ' . $this->offDutyTime);
        $this->shiftHoursObj = $this->offDutyDateTime->diff($this->onDutyDateTime);
        $hours = $this->shiftHoursObj->format('%h');
        $minutes = $this->shiftHoursObj->format('%i');
        $this->shiftHours = ($hours * 60) + $minutes;

    }

    public function configCrossDayPresentAbsentType()
    {
        if ($this->isClockInOutSet) {
            $this->presentAbsentType = AbsentType::ON_TIME;
            return;
        }

        $this->presentAbsentType = (empty($this->data->leaveMasterID))
            ? AbsentType::ABSENT
            : AbsentType::ON_LEAVE;

        if ($this->data->leaveHalfDay == 1) {
            $this->presentAbsentType = AbsentType::HALF_DAY;
        }

        if ($this->dayType == AttDayType::HOLIDAY) {
            $this->presentAbsentType = AbsentType::HOLIDAY;
        }

        if ($this->dayType == AttDayType::WEEKEND) {
            $this->presentAbsentType = AbsentType::WEEKEND;
        }
    }


    public function computeCrossDayAbsentDeductionAmount(){

        if($this->presentAbsentType != AbsentType::ABSENT){
            return;
        }

        $abDayDeductionObj = new AbsentDayDeductionService($this->empId, $this->data->attendanceDate, $this->companyId);
        $abDayDeductionData = $abDayDeductionObj->process();

        if (array_key_exists('pay', $abDayDeductionData)) {
            $this->absDedAmount= $abDayDeductionData['pay']['trAmount'];
            $this->salCatId = $abDayDeductionData['pay']['salaryCategoryId'];
        }

        if (array_key_exists('nonPay', $abDayDeductionData)) {
            $this->absDedNonAmount = $abDayDeductionData['nonPay']['trAmount'];
            $this->nonSalCatId = $abDayDeductionData['nonPay']['salaryCategoryId'];
        }
    }

    function calculateCrossDayFlexyActualWorkingHour(){

        $flexibleHourFromDateTime = $this->clockInDate.' '.$this->flexibleHourFrom;

        $out = new DateTime($this->clockOutDateTime);

        $in = ($this->isShiftHoursSet && ($flexibleHourFromDateTime >= $this->clockInDateTime))
            ? new DateTime($flexibleHourFromDateTime)
            : new DateTime($this->clockInDateTime);

        if (empty($out) || empty($in)) {
            return;
        }

        $totWorkingHoursObj = $out->diff($in);
        $hours = $totWorkingHoursObj->format('%h');
        $minutes = $totWorkingHoursObj->format('%i');
        $this->actualWorkingHours = ($hours * 60) + $minutes;
    }

    public function rotaShiftCalculateActualWorkedHours(){

        if ($this->presentAbsentType != AbsentType::ON_TIME || !$this->isClockInOutSet) {
            return false;
        }

        $out = $this->clockOutDtObj;

        if(empty($out)){
            return false;
        }

        $in = ($this->isShiftHoursSet && $this->onDutyDateTime >= $this->clockInDtObj)
            ? $this->onDutyDateTime
            : $this->clockInDtObj;


        $totWorkingHoursObj = $out->diff($in);
        $hours = $totWorkingHoursObj->format('%h');
        $minutes = $totWorkingHoursObj->format('%i');
        $this->actualWorkingHours = ($hours * 60) + $minutes;

    }

    function calculateRotaShiftOfficialWorkHours(){

        if(!$this->isShiftHoursSet || !$this->isClockInOutSet ){
            return false;
        }

        $out = ($this->isShiftHoursSet && ($this->offDutyDateTime <= $this->clockOutDtObj))
            ? $this->offDutyDateTime
            : $this->clockOutDtObj;

        $in = ($this->isShiftHoursSet && ($this->onDutyDateTime >= $this->clockInDtObj))
            ? $this->onDutyDateTime
            : $this->clockInDtObj;

        if(empty($out)|| empty($in)){
            return false;
        }

        $officialWorkTimeObj = $out->diff($in);
        $hours = $officialWorkTimeObj->format('%h');
        $minutes = $officialWorkTimeObj->format('%i');
        $this->officialWorkTime = ($hours * 60) + $minutes;
    }

    public function flxRotaGeneralComputation(){
        if (!$this->isShiftHoursSet || $this->dayType != AttDayType::NORMAL_DAY) {
            return false;
        }
        $this->flxValidations();
        $this->flxLateHourComputation();


        $flexibleHrFromDt = $this->clockInDate.' '.$this->flexibleHourFrom;
        $flexibleHrFromDtObj = new DateTime($flexibleHrFromDt);

        $this->gracePeriod = 0;
        $clockInDt = $this->clockInDtObj;

        if ($clockInDt < $flexibleHrFromDtObj) {
            $clockInDt = $flexibleHrFromDtObj;
            if($this->isClockInOutSet){
                $this->clockIn = $this->flexibleHourFrom;
            }

        }

        $this->earlyHourComputation($clockInDt, $this->clockOutDtObj);
        $this->overTimeComputation($clockInDt, $this->clockOutDtObj);
    }

    public function rotaGeneralComputation(){

        $this->lateHoursComputation();


        $clockInDt = new DateTime($this->clockInDateTime);

        $onDutyDt = $this->onDutyDateTime;


        $clockInDtTemp = ($this->clockInDtObj >= $this->onDutyDateTime)
            ? $clockInDt->format('Y-m-d H:i:s')
            : $onDutyDt->format('Y-m-d H:i:s');

        $clockInDtTemp2 = $clockInDtTemp;

        $clockInDtTemp = new DateTime($clockInDtTemp);
        $clockInDtTemp2 = new DateTime($clockInDtTemp2);


        $this->earlyHourComputation($clockInDtTemp, $this->clockOutDtObj);
        $this->overTimeComputation($clockInDtTemp2, $this->clockOutDtObj);
    }

    function otherRotaComputation()
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


        if ($this->data->leaveHalfDay == 1) {
            $this->presentAbsentType =AbsentType::HALF_DAY;
        }

        if ($this->clockIn == '00:00:00' || empty($this->clockIn)) {
            $this->clockIn = null;
        }

        if ($this->clockOut == '00:00:00' || empty($this->clockOut)) {
            $this->clockOut = null;
        }
    }
}

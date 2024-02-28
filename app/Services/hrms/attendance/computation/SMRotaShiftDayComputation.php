<?php
namespace App\Services\hrms\attendance\computation;

use App\enums\attendance\AbsentType;
use App\enums\attendance\AttDayType;
use App\Traits\Attendance\AttComputationVariableTrait;
use App\Traits\Attendance\AttendanceComputationTrait;
use App\Services\hrms\attendance\AbsentDayDeductionService;
use DateTime;

class SMRotaShiftDayComputation{
    use AttendanceComputationTrait;
    use AttComputationVariableTrait;
    public function __construct($data, $companyId){

        $this->loadShiftCommonVariables($data, $companyId);
        $this->isGracePeriodSet = ($this->gracePeriod != 0) ? 1 : 0;
    }
    function calculate(){
        $this->configDayType();

        $this->confIsFlexibleHourBaseComputation();

        if ($this->dayType == AttDayType::NORMAL_DAY) {
            $this->calculateShiftHours();
        }

        $this->calculateWorkedHours();

        if (!$this->isClockInOutSet) {
            $this->otherComputation();

            if ($this->clockIn) {
                $this->lateFeeComputation();
            }

            return false;
        }

        $this->clockOutDtObj = new DateTime($this->clockOut);
        $this->onDutyDtObj = new DateTime($this->onDutyTime);

        if ($this->isFlexibleHourBaseComputation) {
            $this->basedOnFlexibleHoursComputation();
        } else {
            $this->generalComputation();
        }

        $this->otherComputation();

        if ($this->dayType == AttDayType::NORMAL_DAY) {
            $this->lateFeeComputation();
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

    public function calculateWorkedHours()
    {
        if (empty($this->clockIn) && empty($this->clockOut)) {

            $this->presentAbsentType = (empty($this->data['leaveMasterID']))
                ? AbsentType::ABSENT
                : AbsentType::ON_LEAVE;

            if (!in_array($this->dayType, [AttDayType::HOLIDAY, AttDayType::WEEKEND])) {
                $this->computeAbsentDeductionAmount();
            }

            if($this->dayType == AttDayType::HOLIDAY){
                $this->presentAbsentType = AbsentType::HOLIDAY;
            }

            if($this->dayType == AttDayType::WEEKEND){
                $this->presentAbsentType = AbsentType::WEEKEND;
            }

            return false;
        }

        $this->presentAbsentType = AbsentType::ON_TIME;

        if ($this->isFlexibleHourBaseComputation) {
            $this->FlxCalculateActualWorkingHours();
            return;
        }

        if (!empty($this->clockIn) && empty($this->clockOut)) {
            $this->lateHoursComputation();
            return false;
        }

        $this->isClockInOutSet = true;

        $t1 = ($this->isShiftHoursSet && ($this->offDutyTime <= $this->clockOut))
            ? new DateTime($this->clockOut)
            : new DateTime($this->offDutyTime);

        $t2 = ($this->isShiftHoursSet && ($this->onDutyTime >= $this->clockIn))
            ? new DateTime($this->onDutyTime)
            : new DateTime($this->clockIn);

        $totWorkingHours_obj = $t1->diff($t2);
        $hours = $totWorkingHours_obj->format('%h');
        $minutes = $totWorkingHours_obj->format('%i');
        $this->actualWorkingHours = ($hours * 60) + $minutes;

        $this->calculateRealTime();
        $this->calculateOfficialWorkTime();
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
        $this->actualWorkingHours = ($hours * 60) + $minutes;

        $this->calculateRealTime();
        $this->calculateOfficialWorkTime();

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

    public function generalComputation()
    {

        $this->lateHoursComputation();

        $clockInDt = new DateTime($this->clockIn);
        $onDutyDt = new DateTime($this->onDutyTime);

        $clockInDtTemp = ($this->clockIn >= $this->onDutyTime)
            ? $clockInDt->format('H:i:s')
            : $onDutyDt->format('H:i:s');

        $clockInDtTemp2 = $clockInDtTemp;
        $clockInDtTemp = new DateTime($clockInDtTemp);
        $clockInDtTemp2 = new DateTime($clockInDtTemp2);

        $clockOutDtOT = $this->clockOutDtObj;

        $this->earlyHourComputation($clockInDtTemp, $this->clockOutDtObj);
        $this->overTimeComputation($clockInDtTemp2, $clockOutDtOT);
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
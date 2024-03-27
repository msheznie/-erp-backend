<?php
namespace App\Traits\Attendance;
use Carbon\Carbon;

trait AttComputationVariableTrait{

    public $data;
    public $dayType = 0;
    public $onDutyTime;
    public $offDutyTime;
    public $isShiftHoursSet = false;
    public $clockIn;
    public $clockOut;
    public $gracePeriod;
    public $isGracePeriodSet =0;
    public $onDutyDtObj;
    public $shiftHoursObj;
    public $shiftHours = null;
    public $shiftId;
    public $isFlexibleHour;
    public $flexibleHourFrom;
    public $flexibleHourTo;
    public $presentAbsentType = '';
    public $empId;
    public $actualWorkingHours = 0;
    public $officialWorkTime = 0;
    public $isFlexibleHourBaseComputation = false;
    public $realTime = 0;
    public $lateHours = 0;
    public $earlyHours = 0;
    public $overTimeHours = 0;
    public $lateFee = 0;
    public $isClockInOutSet = false;
    public $clockOutDtObj;
    public $clockInDtObj;
    public $calcClockOut = 0;
    public $oTClockInDt;
    public $oTClockOutDt;
    public $normalDayData = ['true_false' => 0, 'hours' => 0, 'realTime' => 0];
    public $weekendData = ['true_false' => 0, 'hours' => 0, 'realTime' => 0];
    public $holidayData = ['true_false' => 0, 'hours' => 0, 'realTime' => 0];
    public $absDedAmount;
    public $absDedNonAmount;
    public $salCatId;
    public $nonSalCatId;
    public $isCrossDay = 0;
    public $companyId;
    public $dateTime;

    function loadShiftCommonVariables($data, $companyId){
        $this->data = $data;
        $this->onDutyTime = trim($data['onDutyTime']);
        $this->offDutyTime = trim($data['offDutyTime']);
        $this->clockIn = trim($data['clock_in']);
        $this->clockOut = trim($data['clock_out']);
        $this->gracePeriod = trim($data['gracePeriod']);
        $this->shiftId = trim($data['shiftID']);
        $this->isFlexibleHour = trim($data['isFlexyHour']);
        $this->flexibleHourFrom = trim($data['flexyHrFrom']);
        $this->flexibleHourTo = trim($data['flexyHrTo']);
        $this->empId = trim($data['emp_id']);
        $this->companyId = $companyId;
        $this->dateTime = Carbon::now()->format('Y-m-d H:i:s');
    }
}
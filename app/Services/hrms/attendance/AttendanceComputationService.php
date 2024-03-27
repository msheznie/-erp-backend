<?php

namespace App\Services\hrms\attendance;

use App\enums\attendance\AbsentType;
use App\enums\attendance\AttDayType;
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
    public $actualWorkingHours = 0;
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

    public $empId;
    public $absDedAmount;
    public $absDedNonAmount;
    public $salCatId;
    public $nonSalCatId;

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
        $this->empId = trim($data['emp_id']);
    }

    public function execute(){
        $this->configDayType();
        $this->configClockinClockoutSet();

        if ($this->dayType == AttDayType::NORMAL_DAY) {
            $this->calculateShiftHours();
        }

        $this->configPresentAbsentType();

        if (!$this->isClockInOutSet && !in_array($this->dayType, [AttDayType::HOLIDAY, AttDayType::WEEKEND])) {
            $this->computeAbsentDeductionAmount();
        }

        $this->confIsFlexibleHourBaseComputation();

        if ($this->isFlexibleHourBaseComputation) {
            $this->flxCalculateActualTime();
        } else if ($this->data['shiftType'] == Shifts::OPEN) {
            return $this->openShiftCalculateWorkedHours();
        } else {
            $this->calculateActualTime();
        }

        $this->calculateRealTime();
        $this->calculateOfficialWorkTime();

        //late in, early out, overtime
        if($this->isFlexibleHourBaseComputation){
            $this->clockOut_dt = new DateTime($this->clockOut);
            $this->flxGeneralComputation();
        }else{
            $this->clockOut_dt = new DateTime($this->clockOut);
            $this->generalComputation();
        }

        $this->otherComputation();
        $this->lateFeeComputation();
    }

    function configDayType()
    {
        if ($this->data['isHoliday'] == 1) {
            $this->dayType = AttDayType::HOLIDAY;
        } else if ($this->data['isWeekend'] == 1) {
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

        $t1 = new DateTime($this->onDutyTime);
        $t2 = new DateTime($this->offDutyTime);
        $this->shiftHours_obj = $t1->diff($t2);
        $hours = $this->shiftHours_obj->format('%h');
        $minutes = $this->shiftHours_obj->format('%i');

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

    public function calculateOfficialWorkTime()
    {
        if(!$this->isShiftHoursSet || !$this->isClockInOutSet ){
            return false;
        }

        $t3 = ($this->offDutyTime <= $this->clockOut)
            ? new DateTime($this->offDutyTime)
            : new DateTime($this->clockOut);

        $t4 = ($this->onDutyTime >= $this->clockIn)
            ? new DateTime($this->onDutyTime)
            : new DateTime($this->clockIn);

        $officialWorkTimeObj = $t3->diff($t4);
        $hours = $officialWorkTimeObj->format('%h');
        $minutes = $officialWorkTimeObj->format('%i');
        $this->officialWorkTime = ($hours * 60) + $minutes;
    }

    public function calculateRealTime()
    {

        if (!$this->isShiftHoursSet || !$this->isClockInOutSet) {
            return false;
        }

        if ($this->actualWorkingHours && $this->shiftHours) {
            $realtime = $this->shiftHours / $this->actualWorkingHours;
            $this->realTime = round($realtime, 1);
        }
    }

    public function computeAbsentDeductionAmount()
    {

        if ($this->presentAbsentType != AbsentType::ABSENT) {
            return;
        }

        $abDayDeductionCalc = new AbsentDayDeductionService($this->empId, $this->data['att_date'], $this->companyId);
        $abDayDeductionCalc = $abDayDeductionCalc->process();

        if (array_key_exists('pay', $abDayDeductionCalc)) {
            $this->absDedAmount = $abDayDeductionCalc['pay']['trAmount'];
            $this->salCatId = $abDayDeductionCalc['pay']['salaryCategoryId'];
        }

        if (array_key_exists('nonPay', $abDayDeductionCalc)) {
            $this->absDedNonAmount = $abDayDeductionCalc['nonPay']['trAmount'];
            $this->nonSalCatId = $abDayDeductionCalc['nonPay']['salaryCategoryId'];
        }
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

    private function getOnDutyTempTime()
    {
        $minutesToAdd = $this->gracePeriod;

        $tempOnDuty_dt = new DateTime($this->onDutyTime);
        return $tempOnDuty_dt->modify("+{$minutesToAdd} minutes");
    }

    function lateHoursComputation()
    {
        if (!$this->clockIn) {
            return false;
        }

        $tempOnDuty_dt = $this->getOnDutyTempTime();
        $clockIn_dt = new DateTime($this->clockIn);

        if ($clockIn_dt->format('H:i:s') > $tempOnDuty_dt->format('H:i:s')) {
            $this->presentAbsentType = AbsentType::LATE;

            $interval = $clockIn_dt->diff($tempOnDuty_dt);

            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
            $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
            $this->lateHours = $hours * 60 + $minutes;
        }
    }

    // early hour computation  [ same function for flexible hour and general ]
    public function earlyHourComputation($clockIn_dt, $clockOut_dt)
    {
        if (!$this->isClockInOutSet) {
            return false;
        }

        $this->calcClockOut = clone $clockIn_dt;
        $this->calcClockOut->modify("+{$this->shiftHours} minutes");

        if ($this->gracePeriod > 0) {
            $this->calculateEarlyHourBaseOnGracePeriod();
        } else {
            $this->calculateGeneralEarlyHour($clockOut_dt);
        }
    }

    function calculateGeneralEarlyHour($clockOut_dt)
    {

        if ($this->calcClockOut > $clockOut_dt) {
            $interval = $this->calcClockOut->diff($clockOut_dt);
            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
            $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
            $this->earlyHours = $hours * 60 + $minutes;
        }
    }

    function calculateEarlyHourBaseOnGracePeriod()
    {

        $calcClockOutMinutes = $this->shiftHours - $this->actualWorkingHours;

        $calcClockOutDtmObj = new DateTime('@0');
        $calcClockOutDtmObj->modify('+' . $calcClockOutMinutes . ' minutes');

        $gracePeriodDtmObj = new DateTime('@0');
        $gracePeriodDtmObj->modify('+' . $this->gracePeriod . ' minutes');

        if ($calcClockOutDtmObj > $gracePeriodDtmObj) {
            $interval = $calcClockOutDtmObj->diff($gracePeriodDtmObj);
            $hours = ($interval->h != 0) ? $interval->h : 0;
            $minutes = ($interval->i != 0) ? $interval->i : 0;
            $this->earlyHours = $hours * 60 + $minutes;
        }
    }

    // OT computation  [ same function for flexible hour and general ]
    public function overTimeComputation($clockIn_dt_ot, $clockOut_dt_ot)
    {

        if (!$this->isClockInOutSet) {
            return false;
        }

        if ($clockOut_dt_ot <= $this->calcClockOut) {
            return true;
        }

        $currentDate = date('Y-m-d');

        $workingHours_obj = $clockIn_dt_ot->diff($clockOut_dt_ot);
        $totW = new DateTime($workingHours_obj->format("{$currentDate} %h:%i:%s"));
        $actW = new DateTime($this->shiftHours_obj->format("{$currentDate} %h:%i:%s"));

        if ($totW->format('H:i') > $actW->format('H:i')) {
            $overTime_obj = $actW->diff($totW);
            $hours = ($overTime_obj->format('%h') != 0) ? $overTime_obj->format('%h') : 0;
            $minutes = ($overTime_obj->format('%i') != 0) ? $overTime_obj->format('%i') : 0;
            $this->overTimeHours = $hours * 60 + $minutes;
        }
    }

    public function lateFeeComputation()
    {

        if (empty($this->lateHours)) {
            return false;
        }

        $attendanceDate = $this->data['att_date'];

        $obj = new LateFeeComputationService($this->empId, $attendanceDate, $this->companyId);
        $amountForPerMinute = $obj->compute();

        $this->lateFee = ($amountForPerMinute > 0)
            ? $this->lateHours * $amountForPerMinute
            : 0;
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

        if ($this->clockIn == '00:00:00' || empty($this->clockIn)) {
            $this->clockIn = null;
        }

        if ($this->clockOut == '00:00:00' || empty($this->clockOut)) {
            $this->clockOut = null;
        }
    }

    function confIsFlexibleHourBaseComputation()
    {
        if ($this->isFlexibleHour == 1 && $this->flexibleHourFrom != null) {
            $this->isFlexibleHourBaseComputation = true;
            $this->gracePeriod = 0;
        }
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

        $totWorkingHoursObj = $t1->diff($t2);
        $hours = $totWorkingHoursObj->format('%h');
        $minutes = $totWorkingHoursObj->format('%i');
        $this->actualWorkingHours = ($hours * 60) + $minutes;
    }

    public function flxGeneralComputation()
    {
        if (!$this->isShiftHoursSet || $this->dayType != AttDayType::NORMAL_DAY) {
            return false;
        }

        $this->flxValidations();
        $this->flxLateHourComputation();

        $flexibleHrFrom_dt = new DateTime($this->flexibleHourFrom);
        $this->gracePeriod = 0;
        $clockIn_dt = new DateTime($this->clockIn);

        if ($clockIn_dt->format('H:i:s') < $flexibleHrFrom_dt->format('H:i:s')) {
            $clockIn_dt = $flexibleHrFrom_dt;
            $this->clockIn = $this->flexibleHourFrom;
        }

        $this->earlyHourComputation($clockIn_dt, $this->clockOut_dt);
        $this->overTimeComputation($clockIn_dt, $this->clockOut_dt);
    }

    public function flxValidations()
    {
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

    public function flxLateHourComputation()
    {
        if (!$this->clockIn) {
            return false;
        }

        $clockIn_dt = new DateTime($this->clockIn);
        $flxHrTo_dt = new DateTime($this->flexibleHourTo);

        if ($clockIn_dt->format('H:i:s') > $flxHrTo_dt->format('H:i:s')) {
            $this->presentAbsentType = AbsentType::LATE;

            $interval = $clockIn_dt->diff($flxHrTo_dt);
            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
            $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
            $this->lateHours = $hours * 60 + $minutes;
        }
    }

    public function openShiftCalculateWorkedHours()
    {
        $this->otherComputation();
        $this->actualWorkingHours =  $this->calculateOpenShiftActualWorkingHrs();
        $shiftHours = ($this->data['shiftType'] == 1)? $this->data['workingHour']: $this->shiftHours;
        $shiftHours = (empty($shiftHours))? 0: $shiftHours;
        $this->officialWorkTime = ($shiftHours > $this->actualWorkingHours) ? $this->actualWorkingHours : $shiftHours;
        if($this->holidayData['true_false'] == 1 || $this->presentAbsentType == 5){
            $this->officialWorkTime = 0;
        }

        if ($this->actualWorkingHours && $this->data['workingHour']) {
            $realtime = $this->data['workingHour'] / $this->actualWorkingHours;
            $this->realTime = round($realtime, 1);
            $this->openShiftCommonComputations();
        }
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
    public function openShiftCommonComputations()
    {
        if ($this->dayType != AttDayType::NORMAL_DAY) {
            return; //if holiday or weekend no need to compute the OT or shortage (early-out) hours
        }

        if ($this->actualWorkingHours < $this->data['workingHour']) {
            //compute shortage
            $this->earlyHours = $this->data['workingHour'] - $this->actualWorkingHours;
        } else {
            //compute OT
            $this->overTimeHours = $this->actualWorkingHours - $this->data['workingHour'];
        }
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
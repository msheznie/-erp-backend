<?php
namespace App\Services\hrms\attendance\computation;

use App\enums\attendance\AbsentType;
use App\enums\attendance\AttDayType;
use App\Traits\Attendance\AttComputationVariableTrait;
use App\Traits\Attendance\AttendanceComputationTrait;
use DateTime;
use Illuminate\Support\Facades\DB;

class SMRotaShiftCrossDayComputation{
    use AttendanceComputationTrait;
    use AttComputationVariableTrait;

    public $crossDayCutOffTime;
    public $clockInDateTime;
    public $clockOutDateTime;
    public $clockInDate;
    public $clockOutDate;
    public $uploadType;
    public $clockOutFloorId;
    public $onDutyDateTime;
    public $offDutyDateTime;

    public function __construct($data, $companyId){

        $this->loadShiftCommonVariables($data, $companyId);
        $this->crossDayCutOffTime = $data['crossDayCutOffTime'];
        $this->isCrossDay = $data['is_cross_day'];
        $this->isGracePeriodSet = ($this->gracePeriod != 0) ? 1 : 0;
        $this->clockIn = '';
        $this->clockOut = '';
    }

    function calculate(){
        $this->configDayType();
        $this->setRotaShiftClockInAndClockOut();
        $this->configClockinClockoutSet();

        if ($this->dayType == AttDayType::NORMAL_DAY) {
            $this->calculateRotaShiftHours();
        }

        $this->configPresentAbsentType();

        if (!$this->isClockInOutSet && !in_array($this->dayType, [AttDayType::HOLIDAY, AttDayType::WEEKEND])) {
            $this->computeAbsentDeductionAmount();
        }

        $this->confIsFlexibleHourBaseComputation();

        if ($this->isFlexibleHourBaseComputation) {
            $this->calculateFlexyIsCrossDayActualWorkingHour();
        }else{
            $this->rotaShiftCalculateActualWorkedHours();
        }

        $this->calculateRealTime();
        $this->calculateRotaShiftOfficialWorkHours();

        //late in, early out, overtime
        if($this->isFlexibleHourBaseComputation){
            $this->flxRotaGeneralComputation();
        }else{
            $this->rotaGeneralComputation();
        }

        $this->otherComputation();
        $this->lateFeeComputation();
    }

    public function calculateRotaShiftHours()
    {
        if (empty($this->onDutyTime) || empty($this->offDutyTime)) {
            return false;
        }

        $this->isShiftHoursSet = true;

        $attDate = date('Y-m-d', strtotime($this->data['att_date']));
        $attNextDate = date('Y-m-d', strtotime($this->data['att_date'] . "+1 day"));
        $this->onDutyDateTime = new DateTime($attDate . ' ' . $this->onDutyTime);
        $this->offDutyDateTime = new DateTime($attNextDate . ' ' . $this->offDutyTime);
        $this->shiftHoursObj = $this->offDutyDateTime->diff($this->onDutyDateTime);
        $hours = $this->shiftHoursObj->format('%h');
        $minutes = $this->shiftHoursObj->format('%i');
        $this->shiftHours = ($hours * 60) + $minutes;

    }

    function setRotaShiftClockInAndClockOut(){
        $autoIdArr = [];

        $currentDate = date('Y-m-d', strtotime($this->data['att_date']));
        $nextDate = date('Y-m-d', strtotime($this->data['att_date'] . "+1 day"));
        $currentDateWithCutTime = $currentDate.' '.trim($this->crossDayCutOffTime);
        $nextDateWithCutTime = $nextDate.' '.trim($this->crossDayCutOffTime);

        $attTempRec = DB::table('srp_erp_pay_empattendancetemptable as t')
            ->select('t.autoID', 't.emp_id', 't.attDate', 't.in_out', 't.attTime', 't.attDateTime', 'l.floorID',
                't.uploadType')
            ->join("srp_erp_empattendancelocation AS l", function ($join) {
                $join->on("l.deviceID", "=", "t.device_id")
                    ->on("t.empMachineID", "=", "l.empMachineID");
            })
            ->where('t.companyID', $this->companyId)
            ->where('t.emp_id', $this->data['emp_id'])
            ->whereBetween('t.attDate', [$currentDate, $nextDate])
            ->whereBetween('t.attDateTime', [$currentDateWithCutTime, $nextDateWithCutTime])
            ->orderBy('t.attDate', 'asc')
            ->orderBy('t.attTime', 'asc')
            ->get();
        $firstRecord = $attTempRec->first();
        $lastRecord = $attTempRec->last();
        $countRecord = $attTempRec->count();

        if(!empty($firstRecord)){
            $this->clockIn = $firstRecord->attTime;
            $this->clockInDateTime = $firstRecord->attDateTime;
            $this->clockInDtObj = new DateTime($this->clockInDateTime);
            $this->clockInDate = $firstRecord->attDate;
            $autoIdArr[] = $firstRecord->autoID;
        }

        if($countRecord > 1){
            $this->clockOut = $lastRecord->attTime;
            $this->clockOutDateTime = $lastRecord->attDateTime;
            $this->clockOutDtObj = new DateTime($this->clockOutDateTime);
            $this->clockOutDate = $lastRecord->attDate;
            $this->uploadType = $lastRecord->uploadType;
            $this->clockOutFloorId = $lastRecord->floorID;
            $autoIdArr[] = $lastRecord->autoID;
        }

        if(!empty($autoIdArr)){
            $this->updateUsedRotaRecords($autoIdArr);
        }

    }

    function updateUsedRotaRecords($autoIdArr){
        DB::table('srp_erp_pay_empattendancetemptable')
            ->whereIn('autoID', $autoIdArr)
            ->update([
                'isUpdated' => 1,
                'timestamp' => $this->dateTime
            ]);
    }

    function calculateFlexyIsCrossDayActualWorkingHour(){

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

        $t1 = $this->clockOutDtObj;
        $t2 = ($this->isShiftHoursSet && $this->onDutyDtObj >= $this->clockInDtObj)
            ? $this->onDutyDtObj
            : $this->clockInDtObj;

        $totWorkingHoursObj = $t1->diff($t2);
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

        $officialWorkTimeObj = $out->diff($in);
        $hours = $officialWorkTimeObj->format('%h');
        $minutes = $officialWorkTimeObj->format('%i');
        $this->officialWorkTime = ($hours * 60) + $minutes;

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
}
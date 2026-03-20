<?php
namespace App\Services\hrms\attendance\computation;
use App\enums\attendance\AbsentType;
use App\enums\attendance\AttDayType;
use App\Traits\Attendance\AttComputationVariableTrait;
use App\Traits\Attendance\AttendanceComputationTrait;
use DateTime;
use Illuminate\Support\Facades\DB;

class SMRotaShiftIndividualPunchesComputation
{
    use AttendanceComputationTrait;
    use AttComputationVariableTrait;
    public $attTempRecords = [];
    private $crossDayCutOffTime;
    public $clockOutFloorId;
    public $clockInDate;
    public $clockOutDate;
    public $onDutyDateTime;
    public $offDutyDateTime;

    public function __construct($data, $companyId) {
        $this->loadShiftCommonVariables($data, $companyId);
        $this->clockIn = $this->clockOut = '';
        $this->crossDayCutOffTime = $data['crossDayCutOffTime'];
        $this->isCrossDay = $data['is_cross_day'];
    }

    function calculate() {
        $this->configDayType();
        $this->confIsFlexibleHourBaseComputation();
        $this->getAttendanceTempTableRecords();
        $this->calculateActualTimeIndividualPunches();
        $this->configClockinClockoutSet();

        if ($this->dayType == AttDayType::NORMAL_DAY) {
            $this->calculateRotaShiftHours();
        }

        if(!in_array($this->presentAbsentType, [AbsentType::EXCEPTION, AbsentType::MISSED_PUNCH])){
            $this->configPresentAbsentType();
        }

        if (!$this->isClockInOutSet && !in_array($this->dayType, [AttDayType::HOLIDAY, AttDayType::WEEKEND])) {
            $this->computeAbsentDeductionAmount();
        }

        $this->calculateRealTime();
        $this->calculateOfficialTimeIndividualPunches();
        $this->individualPunchesGeneralComputation();
        $this->otherComputation();
        $this->lateFeeComputation();
    }

    function individualPunchesGeneralComputation() {
        if (!$this->isShiftHoursSet || $this->dayType != AttDayType::NORMAL_DAY) {
            return false;
        }

        if ($this->isFlexibleHourBaseComputation){
            $this->flxLateHourComputation();
        }else{
            $this->lateHoursComputation();
        }

        $this->workedHrEarlyOutComputation();
        $this->workedHrOverTimeComputation();
    }

    public function getAttendanceTempTableRecords(){
        $currentDate = date('Y-m-d', strtotime($this->data['att_date']));
        $nextDate = date('Y-m-d', strtotime($this->data['att_date'] . "+1 day"));

        $currentDateWithCutTime = $currentDate . ' ' . trim($this->crossDayCutOffTime);
        $nextDateWithCutTime = $nextDate . ' ' . trim($this->crossDayCutOffTime);

        $this->attTempRecords = DB::table('srp_erp_pay_empattendancetemptable as t')
            ->select('t.autoID', 't.emp_id', 't.attDate', 't.attDateTime', 't.in_out',
                't.attTime', 'l.floorID')
            ->join('srp_erp_empattendancelocation as l', function($join) {
                $join->on('l.deviceID', '=', 't.device_id')
                    ->on('t.empMachineID', '=', 'l.empMachineID');
            })
            ->where('t.companyID', $this->companyId)
            ->where('t.emp_id', $this->data['emp_id'])
            ->whereBetween('t.attDate', [$currentDate, $nextDate])
            ->whereBetween('t.attDateTime', [$currentDateWithCutTime, $nextDateWithCutTime])
            ->orderBy('t.attDate', 'ASC')
            ->orderBy('t.attTime', 'ASC')
            ->get()
            ->toArray();

        if (empty($this->attTempRecords)) {
            return false;
        }

        return $this->attTempRecords;
    }

    function calculateActualTimeIndividualPunches(){
        $totalMinutes = 0;
        $inDateTime = null;
        $firstClockInSet = false;

        $attDate = date('Y-m-d', strtotime($this->data['att_date']));

        $onDutyDateTime = $this->onDutyTime ? new DateTime($attDate . ' ' . $this->onDutyTime) : null;

        $flexibleHourFrom = $this->flexibleHourFrom ? new DateTime($attDate.' '.$this->flexibleHourFrom) : null;
        $actualIn = ($this->isFlexibleHourBaseComputation) ? $flexibleHourFrom : $onDutyDateTime;

        foreach ($this->attTempRecords as $record){
            $attDateTime = new DateTime($record->attDateTime);
            if ($record->in_out == 1) {
                if ($inDateTime != null) {
                    $this->presentAbsentType = AbsentType::EXCEPTION;
                }

                $inDateTime = $attDateTime;

                if (!$firstClockInSet){
                    $this->clockInDate = $record->attDate;
                    $firstClockInSet = true;
                }
                continue;
            }

            if ($record->in_out == 2){
                if ($inDateTime == null){
                    $this->presentAbsentType = AbsentType::EXCEPTION;
                    continue;
                }

                $outDateTime = $attDateTime;
                $inDateTime = $actualIn != null && ($inDateTime < $actualIn) ? clone $actualIn : $inDateTime;

                if ($outDateTime > $inDateTime){
                    if ($totalMinutes == 0) {
                        $this->clockIn = $inDateTime->format('H:i:s');
                    }

                    $workingDuration = $outDateTime->diff($inDateTime);
                    $totalMinutes += ($workingDuration->h * 60) + $workingDuration->i;
                    $this->clockOut = $record->attTime;
                    $this->clockOutDate = $record->attDate;
                    $this->clockOutFloorId = $record->floorID;
                }

                $inDateTime = null;
            }
        }

        $this->configMissedPunch();
        $this->updateAttendanceTempTable();
        $crossDayActualTime = $this->calculateCrossDayActualTime();
        $this->actualWorkingHours = $totalMinutes + $crossDayActualTime;
    }

    function calculateOfficialTimeIndividualPunches(){
        if (!$this->isShiftHoursSet || !$this->isClockInOutSet){
            return false;
        }

        $totalMinutes = 0;
        $inDateTime = null;

        foreach ($this->attTempRecords as $record){
            $attDateTime = new DateTime($record->attDateTime);
            if ($record->in_out == 1){
                $inDateTime = $attDateTime;
            }

            if ($record->in_out == 2 && $inDateTime !== null){
                $actualInDtm  = $inDateTime < $this->onDutyDateTime ? clone $this->onDutyDateTime : $inDateTime;
                $actualOutDtm = $attDateTime > $this->offDutyDateTime ? clone $this->offDutyDateTime : $attDateTime;

                if ($actualOutDtm > $actualInDtm) {
                    $workingDuration = $actualOutDtm->diff($actualInDtm);
                    $totalMinutes += ($workingDuration->h * 60) + $workingDuration->i;
                }

                $inDateTime = null;
            }
        }

        $this->officialWorkTime = $totalMinutes;
    }

    public function workedHrEarlyOutComputation(){
        if (($this->actualWorkingHours > $this->shiftHours) || $this->actualWorkingHours == 0) {
            return false;
        }

        $this->earlyHours = $this->shiftHours - $this->actualWorkingHours;

        if ($this->gracePeriod > 0){
            $this->calculateEarlyHourBaseOnGracePeriod();
        }
    }

    public function workedHrOverTimeComputation(){
        if ($this->actualWorkingHours < $this->shiftHours){
            return false;
        }

        $this->overTimeHours = $this->actualWorkingHours - $this->shiftHours;
    }

    public function calculateCrossDayActualTime(){
        $actualWorkingHrs = 0;
        $previousDate = date('Y-m-d', strtotime($this->data['att_date'] . "-1 day"));
        $nextDate = date('Y-m-d', strtotime($this->data['att_date'] . "+1 day"));
        $occurrences = count($this->attTempRecords);
        $lastIndex = $occurrences - 1;

        foreach ($this->attTempRecords as $key => $val){
            if ($key == 0 && $val->in_out == 2) {
                $previouseLastRecord = $this->previousLastRecord($previousDate, $val->attDateTime);
                $actualWorkingHrs += $previouseLastRecord;
                continue;
            }

            if ($key == $lastIndex && $val->in_out == 1){
                if ($lastIndex != 0) {
                    $this->presentAbsentType = AbsentType::EXCEPTION;
                }

                $nextDayFirstRecord = $this->nextDayFirstRecord($nextDate, $val->attDateTime);
                $actualWorkingHrs += $nextDayFirstRecord;
            }
        }

        return $actualWorkingHrs;
    }

    public function getAttendanceTempFirstOrLastRecord($date, $orderBy)
    {
        return DB::table('srp_erp_pay_empattendancetemptable')
            ->select('autoID', 'emp_id', 'attDate', 'in_out', 'attTime')
            ->where('companyID', $this->companyId)
            ->where('emp_id', $this->data['emp_id'])
            ->where('attDate', $date)
            ->orderBy('autoID', $orderBy)
            ->first();
    }

    public function previousLastRecord($previousDate, $attDateTime){
        $actualTime = 0;

        if (empty($this->flexibleHourFrom) && empty($this->onDutyTime)){
            return $actualTime;
        }

        $previousLastRecord = $this->getAttendanceTempFirstOrLastRecord($previousDate, 'DESC');

        if (!empty($previousLastRecord) && $previousLastRecord['in_out'] == 1) {
            $this->clockIn = ($this->isFlexibleHourBaseComputation) ? $this->flexibleHourFrom : $this->onDutyTime;

            $clockInDtm = new DateTime($previousDate. ' ' .$this->clockIn);

            $firstOutRecord = new DateTime($attDateTime);
            if ($clockInDtm < $firstOutRecord) {
                $workingDuration = $firstOutRecord->diff($clockInDtm);
                $actualTime = ($workingDuration->h * 60) + $workingDuration->i;
            }
        }

        return $actualTime;
    }

    public function nextDayFirstRecord($nextDate, $attTime){
        $nextDayFirstRecord =$this->getAttendanceTempFirstOrLastRecord($nextDate, 'ASC');
        $actualTime = 0;

        if (!empty($nextDayFirstRecord) && $nextDayFirstRecord['in_out'] == 2) {
            $this->clockOut = '23:59:00';
            $lastInRecord = new DateTime($attTime);
            $clockOut = new DateTime($nextDayFirstRecord['attDate']. ' ' .'12:00:00');
            $workingDuration = $clockOut->diff($lastInRecord);
            $actualTime = ($workingDuration->h * 60) + $workingDuration->i;
        }

        return $actualTime;
    }

    public function updateAttendanceTempTable()
    {
        if (empty($this->attTempRecords)) {
            return;
        }

        foreach ($this->attTempRecords as $record) {
            DB::table('srp_erp_pay_empattendancetemptable')
                ->where('autoID', $record->autoID)
                ->update(['isUpdated' => 1]);
        }
    }

}

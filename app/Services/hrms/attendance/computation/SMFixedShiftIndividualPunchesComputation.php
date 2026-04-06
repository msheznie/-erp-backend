<?php
namespace App\Services\hrms\attendance\computation;
use App\enums\attendance\AbsentType;
use App\enums\attendance\AttDayType;
use App\Traits\Attendance\AttComputationVariableTrait;
use App\Traits\Attendance\AttendanceComputationTrait;
use DateTime;
use Illuminate\Support\Facades\DB;

class SMFixedShiftIndividualPunchesComputation
{
    use AttendanceComputationTrait;
    use AttComputationVariableTrait;
    public $attTempRecords = [];
    public $recordsToUpdate = [];

    public function __construct($data, $companyId) {
        $this->loadShiftCommonVariables($data, $companyId);
        $this->clockIn = $this->clockOut = '';
    }

    function calculate() {
        $this->configDayType();
        $this->confIsFlexibleHourBaseComputation();
        $this->getAttendanceTempTableRecords();
        $this->calculateActualTimeIndividualPunches();
        $this->configClockinClockoutSet();

        if ($this->dayType == AttDayType::NORMAL_DAY) {
            $this->calculateShiftHours();
        }

        if (!empty($this->data['leaveMasterID']) && $this->data['leaveHalfDay'] != 1) {
            $this->configOnLeavePresentType();
            return;
        }

        if(!in_array($this->presentAbsentType, [AbsentType::EXCEPTION, AbsentType::MISSED_PUNCH])){
            $this->configPresentAbsentType();
        }

        if (!$this->isClockInOutSet && !in_array($this->dayType, [AttDayType::HOLIDAY, AttDayType::WEEKEND])) {
            $this->computeAbsentDeductionAmount();
        }

        $this->calculateRealTime();
        $this->calculateOfficialTimeIndividualPunches();

        //late in, early out, overtime
        $this->individualPunchesGeneralComputation();

        $this->otherComputation();
        $this->lateFeeComputation();
    }

    function individualPunchesGeneralComputation() {
        if (!$this->isShiftHoursSet || $this->dayType != AttDayType::NORMAL_DAY) {
            return false;
        }

        if ($this->isFlexibleHourBaseComputation) {
            $this->flxValidations();
            $this->flxLateHourComputation();
        } else {
            $this->lateHoursComputation();
        }

        $this->workedHrEarlyOutComputation();
        $this->workedHrOverTimeComputation();
    }

    public function getAttendanceTempTableRecords() {
        $this->attTempRecords = DB::table('srp_erp_pay_empattendancetemptable AS t')
            ->select('t.autoID', 't.emp_id', 't.attDate', 't.attDateTime', 't.in_out', 't.attTime')
            ->where([
                't.companyID' => $this->companyId,
                't.emp_id' => $this->data['emp_id'],
                't.attDate' => date('Y-m-d', strtotime($this->data['att_date']))
            ])
            ->join('srp_erp_empattendancelocation as l', function($join) {
                $join->on('l.deviceID', '=', 't.device_id')
                    ->on('t.empMachineID', '=', 'l.empMachineID');
            })
            ->orderBy('t.attDateTime', 'ASC')
            ->get()
            ->toArray();


        if (empty($this->attTempRecords)) {
            return false;
        }
    }

    function calculateActualTimeIndividualPunches() {
        $totalMinutes = $inTempAutoId = 0;
        $inTime = $missedPunchTime = $missedPunchType = null;
        $onDutyTime = $this->onDutyTime ? new DateTime($this->onDutyTime) : null;
        $flexibleHourFrom = $this->flexibleHourFrom ? new DateTime($this->flexibleHourFrom) : null;
        $actualIn = ($this->isFlexibleHourBaseComputation) ? $flexibleHourFrom : $onDutyTime;

        foreach ($this->attTempRecords as $record)
        {
            $attTime = new DateTime($record->attTime);
            $missedPunchTime = $attTime;
            $missedPunchType = $record->in_out;

            if ($record->in_out == 1) {
                if ($inTime != null) {
                    $this->presentAbsentType = AbsentType::EXCEPTION;
                }

                $inTime = $attTime;
                $inTempAutoId = $record->autoID;
                continue;
            }

            if ($record->in_out == 2) {
                if ($inTime == null) {
                    $this->presentAbsentType = AbsentType::EXCEPTION;
                    continue;
                }

                $outTime = $attTime;
                $clockInTime = $inTime;
                $inTime = $actualIn != null && ($inTime < $actualIn) ? clone $actualIn : $inTime;

                if ($outTime > $inTime) {
                    if ($totalMinutes == 0) {
                        $this->clockIn = $clockInTime->format('H:i:s');
                    }
                    $workingDuration = $outTime->diff($inTime);
                    $totalMinutes += ($workingDuration->h * 60) + $workingDuration->i;
                    $this->clockOut = $record->attTime;
                    $this->recordsToUpdate[] = $inTempAutoId;
                    $this->recordsToUpdate[] = $record->autoID;
                }

                $inTime = null;
            }
        }

        $this->configMissedPunch($missedPunchTime, $missedPunchType);

        $crossDayActualTime = $this->calculateCrossDayActualTime();
        $this->actualWorkingHours = $totalMinutes + $crossDayActualTime;

        $this->updateAttendanceTempTable();
    }

    function calculateOfficialTimeIndividualPunches() {
        if (!$this->isShiftHoursSet){
            return false;
        }

        $totalMinutes = 0;
        $inTime = null;
        $onDutyTime = new DateTime($this->onDutyTime);
        $offDutyTime = new DateTime($this->offDutyTime);

        foreach ($this->attTempRecords as $record)
        {
            $attTime = new DateTime($record->attTime);
            if ($record->in_out == 1) {
                $inTime = $attTime;
            }

            if ($record->in_out == 2 && $inTime !== null) {
                $actualIn  = $inTime < $onDutyTime ? clone $onDutyTime : $inTime;
                $actualOut = $attTime > $offDutyTime ? clone $offDutyTime : $attTime;

                if ($actualOut > $actualIn) {
                    $workingDuration = $actualOut->diff($actualIn);
                    $totalMinutes += ($workingDuration->h * 60) + $workingDuration->i;
                }

                $inTime = null;
            }
        }

        $this->officialWorkTime = $totalMinutes;
    }

    public function workedHrEarlyOutComputation() {
        if (($this->actualWorkingHours > $this->shiftHours) || $this->actualWorkingHours == 0) {
            return false;
        }

        if ($this->gracePeriod > 0) {
            $this->calculateEarlyHourBaseOnGracePeriod();
        } else {
            $this->earlyHours = $this->shiftHours - $this->actualWorkingHours;
        }
    }

    public function workedHrOverTimeComputation() {
        if ($this->actualWorkingHours < $this->shiftHours) {
            return false;
        }

        $this->overTimeHours = $this->actualWorkingHours - $this->shiftHours;
    }

    public function calculateCrossDayActualTime()
    {
        $actualWorkingHrs = $preLastRecord = 0;
        $previousDate = date('Y-m-d', strtotime($this->data['att_date'] . "-1 day"));
        $nextDate = date('Y-m-d', strtotime($this->data['att_date'] . "+1 day"));
        $occurrences = count($this->attTempRecords);
        $lastIndex = $occurrences - 1;

        foreach ($this->attTempRecords as $key => $val) {
            if ($key == 0 && $val->in_out == 2) {
                $preLastRecord = $this->previousLastRecord($previousDate, $val->attTime);

                if ($preLastRecord > 0){
                    $actualWorkingHrs += $preLastRecord;
                    $this->recordsToUpdate[] = $val->autoID;
                }

                continue;
            }

            if ($key == $lastIndex && $val->in_out == 1) {
                if ($lastIndex != 0) {
                    $this->presentAbsentType = AbsentType::EXCEPTION;
                }

                $nextDayFirstRecord = $this->nextDayFirstRecord($nextDate, $val->attDateTime, $preLastRecord);

                if ($nextDayFirstRecord > 0){
                    $actualWorkingHrs += $nextDayFirstRecord;
                    $this->recordsToUpdate[] = $val->autoID;
                }
            }
        }

        return $actualWorkingHrs;
    }

    public function getAttendanceTempFirstOrLastRecord($date, $orderBy) {
        return DB::table('srp_erp_pay_empattendancetemptable')
            ->select('autoID', 'emp_id', 'attDate', 'in_out', 'attTime')
            ->where([
                'companyID' => $this->companyId,
                'emp_id' => $this->data['emp_id'],
                'attDate' => $date,
            ])
            ->orderBy('autoID', $orderBy)
            ->first();
    }

    public function previousLastRecord($previousDate, $attTime) {
        $actualTime = 0;

        if (empty($this->flexibleHourFrom) && empty($this->onDutyTime)){
            return $actualTime;
        }

        $previousLastRecord = $this->getAttendanceTempFirstOrLastRecord($previousDate, 'DESC');

        if (!empty($previousLastRecord) && $previousLastRecord->in_out == 1) {
            $scheduledInTime =
                ($this->isFlexibleHourBaseComputation) ? $this->flexibleHourFrom : $this->onDutyTime;
            $clockIn = new DateTime($scheduledInTime);
            $firstOutRecord = new DateTime($attTime);
            if ($clockIn < $firstOutRecord) {
                $this->clockIn = null;
                $workingDuration = $firstOutRecord->diff($clockIn);
                $actualTime = ($workingDuration->h * 60) + $workingDuration->i;

                if (empty($this->clockOut)){
                   $this->clockOut = $attTime;
                }

                $this->presentAbsentType = AbsentType::EXCEPTION;
            }
        }

        return $actualTime;
    }

    public function nextDayFirstRecord($nextDate, $attTime, $preLastRecord) {
        $nextDayFirstRecord =$this->getAttendanceTempFirstOrLastRecord($nextDate, 'ASC');
        $actualTime = 0;

        if (!empty($nextDayFirstRecord) && $nextDayFirstRecord->in_out == 2) {
            $this->clockOut = null;
            $lastInRecord = new DateTime($attTime);
            $clockOut = new DateTime('24:00:00');
            $workingDuration = $clockOut->diff($lastInRecord);
            $actualTime = ($workingDuration->h * 60) + $workingDuration->i;

            if (empty($this->clockIn) && $preLastRecord == 0){
               $this->clockIn = $attTime;
            }

            $this->presentAbsentType = AbsentType::EXCEPTION;
        }

        return $actualTime;
    }

    public function updateAttendanceTempTable()
    {
        $allTempIds = array_column($this->attTempRecords, 'autoID');

        if (empty($allTempIds)) return;

        $updatedIds = $this->recordsToUpdate ? $this->recordsToUpdate : [];
        $notUpdatedIds = array_diff($allTempIds, $updatedIds);

        if (!empty($updatedIds)) {
            DB::table('srp_erp_pay_empattendancetemptable')
                ->whereIn('autoID', $updatedIds)
                ->update(['isUpdated' => 1]);
        }

        if (!empty($notUpdatedIds)) {
            DB::table('srp_erp_pay_empattendancetemptable')
                ->whereIn('autoID', $notUpdatedIds)
                ->update(['isUpdated' => 0]);
        }
    }
}

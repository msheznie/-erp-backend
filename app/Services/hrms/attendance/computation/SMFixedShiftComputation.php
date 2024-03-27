<?php
namespace App\Services\hrms\attendance\computation;
use App\enums\attendance\AttDayType;
use App\Traits\Attendance\AttComputationVariableTrait;
use App\Traits\Attendance\AttendanceComputationTrait;
use DateTime;
class SMFixedShiftComputation{

    use AttendanceComputationTrait;
    use AttComputationVariableTrait;

    public function __construct($data, $companyId){

        $this->loadShiftCommonVariables($data, $companyId);
    }
    public function calculate(){
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
        } else {
            $this->calculateActualTime();
        }


        $this->calculateRealTime();
        $this->calculateOfficialWorkTime();

        //late in, early out, overtime
        if($this->isFlexibleHourBaseComputation){
            $this->clockOutDtObj = new DateTime($this->clockOut);
            $this->flxGeneralComputation();
        }else{
            $this->clockOutDtObj = new DateTime($this->clockOut);
            $this->generalComputation();
        }

        $this->otherComputation();
        $this->lateFeeComputation();
    }
}
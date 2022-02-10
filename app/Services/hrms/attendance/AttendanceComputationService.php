<?php
namespace App\Services\hrms\attendance;

use DateTime;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;

class AttendanceComputationService{
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
    
    public $normalDayData = ['true_false'=> 0, 'hours'=> 0, 'realTime'=> 0];
    public $weekendData = ['true_false'=> 0, 'hours'=> 0, 'realTime'=> 0];
    public $holidayData = ['true_false'=> 0, 'hours'=> 0, 'realTime'=> 0];


    //supporting values
    public $isShiftHoursSet = false;
    public $isClockInOutSet = false;
    public $shiftHours_obj;
    public $clockIn_dt;
    public $clockOut_dt;
    public $onDuty_dt;
    
    public function __construct($data, $companyId){
        Log::useFiles( CommonJobService::get_specific_log_file('attendance-clockIn') );

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

    }

    public function execute(){
        $this->calculateActualWorkingHours();
        $this->calculateWorkedHours();

        if(!$this->isShiftHoursSet || !$this->isClockInOutSet){
            if($this->isShiftHoursSet){
                $this->otherComputation();
            }
            return false;
        }

        $this->clockIn_dt = new DateTime($this->clockIn);
        $this->clockOut_dt = new DateTime($this->clockOut);
        $this->onDuty_dt = new DateTime($this->onDutyTime);

        if($this->isFlexibleHour == 1 && $this->flexibleHourFrom != null ){ 
            // flexible Hour Attendance 
            $status = $this->basedOnFlexibleHoursComputation();
            if(!$status){
                return false;
            }
        }
        else{
            $this->generalComputation();
        }

        $this->lateFeeComputation();
        $this->otherComputation();
    }

    function otherComputation(){ 
        $isNormalDay = true; //normal day

        if ($this->data['isWeekend'] == 1) {
            $isNormalDay = false;
            $this->weekendData = [
                'true_false'=> 1, 'hours'=> $this->totalWorkingHours, 'realTime'=> $this->realTime
            ];
        }

        if($this->data['isHoliday'] == 1) {
            $isNormalDay = false;
            $this->holidayData = [
                'true_false'=> 1, 'hours'=> $this->totalWorkingHours, 'realTime'=> $this->realTime
            ];
        }

        if($isNormalDay) {            
            $this->normalDayData = [
                'true_false'=> 1, 'hours'=> $this->overTimeHours, 'realTime'=> $this->realTime
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

    public function calculateActualWorkingHours(){
        if(empty($this->onDutyTime) || empty($this->offDutyTime)){            
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

    public function calculateWorkedHours(){
        if (empty($this->clockIn) || empty($this->clockOut)) {

            $this->presentAbsentType = (empty($this->data['leaveMasterID']))
                ? 4 //absent
                : 5; //on leave
            
            return false;            
        } 

        $this->isClockInOutSet = true;

        $t1 = ( $this->isShiftHoursSet && ($this->offDutyTime <= $this->clockOut) ) 
            ? new DateTime($this->offDutyTime)
            : new DateTime($this->clockOut);

        $t2 = ( $this->isShiftHoursSet && ($this->onDutyTime >= $this->clockIn) ) 
            ? new DateTime($this->onDutyTime)
            : new DateTime($this->clockIn);
         
        $totWorkingHours_obj = $t1->diff($t2);
        $hours = $totWorkingHours_obj->format('%h');
        $minutes = $totWorkingHours_obj->format('%i');
        $this->totalWorkingHours = ($hours * 60) + $minutes;

        if ($this->totalWorkingHours && $this->shiftHours) {
            $realtime = $this->shiftHours / $this->totalWorkingHours;
            $this->realTime = round($realtime, 1);
        }
    }   

    public function generalComputation(){
        
        $minutesToAdd = $this->gracePeriod;
        $tempOnDuty_dt = $this->onDuty_dt;
        $tempOnDuty_dt->modify("+{$minutesToAdd} minutes");

        $this->presentAbsentType = 1; //presented on time

        //late hour computation
        if ($this->clockIn_dt->format('H:i:s') > $tempOnDuty_dt->format('H:i:s')) {
            $this->presentAbsentType = 2; //presented but late

            $interval = $this->clockIn_dt->diff($tempOnDuty_dt);

            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
            $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
            $this->lateHours = $hours * 60 + $minutes;
        }

        $clockIn_dt_temp = ($this->clockIn >= $this->onDutyTime)
            ? $this->clockIn_dt->format('H:i:s')
            : $this->onDuty_dt->format('H:i:s');

        $clockIn_dt_temp2 = $clockIn_dt_temp;
        $clockIn_dt_temp = new DateTime($clockIn_dt_temp);
        $clockIn_dt_temp2 = new DateTime($clockIn_dt_temp2);

        $clockOut_dt_ot = $this->clockOut_dt;

        $this->earlyHourComputation($clockIn_dt_temp, $this->clockOut_dt);
        $this->overTimeComputation($clockIn_dt_temp2, $clockOut_dt_ot);
    }

    public function basedOnFlexibleHoursComputation(){
        $status = $this->flxValidations();
        if(!$status){
            return false;
        }
                        
        
        $flexibleHrFrom_dt = new DateTime($this->flexibleHourFrom);
        $flexibleHrTo_dt = new DateTime($this->flexibleHourTo);

        if($flexibleHrTo_dt->format('H:i:s') > $this->onDuty_dt->format('H:i:s')){
            $this->gracePeriod = 0;
        }

        if($this->clockIn_dt->format('H:i:s') < $flexibleHrFrom_dt->format('H:i:s')){
            $this->clockIn_dt = $flexibleHrFrom_dt;
            $this->clockIn = $this->flexibleHourFrom;
        }

        $this->presentAbsentType = 1; //presented on time 
        $this->flxLateHourComputation($this->clockIn_dt, $flexibleHrTo_dt);
        $this->earlyHourComputation($this->clockIn_dt, $this->clockOut_dt);
        $this->overTimeComputation($this->clockIn_dt, $this->clockOut_dt);
    }

    // late hour computation based on flexible hour
    public function flxLateHourComputation($clockIn_dt, $flexibleHrTo_dt){ 
        if ($clockIn_dt->format('H:i:s') > $flexibleHrTo_dt->format('H:i:s')) {
            $this->presentAbsentType = 2; //presented but late

            $interval = $clockIn_dt->diff($flexibleHrTo_dt);
            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
            $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
            $this->lateHours = $hours * 60 + $minutes;
        }
    }

    // early hour computation  [ same function for flexible hour and general ]
    public function earlyHourComputation($clockIn_dt, $clockOut_dt){                
        $clockIn_dt->modify("+{$this->shiftHours} minutes");

        if ($clockIn_dt > $clockOut_dt) {            
            $interval = $clockIn_dt->diff($clockOut_dt);
            $hours = ($interval->format('%h') != 0) ? $interval->format('%h') : 0;
            $minutes = ($interval->format('%i') != 0) ? $interval->format('%i') : 0;
            $this->earlyHours = $hours * 60 + $minutes;
        }
    }

    // OT computation  [ same function for flexible hour and general ]
    public function overTimeComputation($clockIn_dt_ot, $clockOut_dt_ot){         
        if ($this->clockOut <= $this->offDutyTime) {
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
    public function flxValidations(){ 
        if( empty($this->flexibleHourFrom) || empty($this->flexibleHourTo)){
            $msg = 'flexible hour to is required';
            Log::error($msg.$this->log_suffix(__LINE__));
            return false;
        }
        
        $onDuty_dt = new DateTime($this->onDuty);                    
        $flexibleHrFrom_dt = new DateTime($this->flexibleHourFrom);
        $flexibleHrTo_dt = new DateTime($this->flexibleHourTo);

        if($flexibleHrFrom_dt->format('H:i:s') >= $onDuty_dt->format('H:i:s')){
            $msg = 'flexible Hour From Should be less than On Duty Time';
            Log::error($msg.$this->log_suffix(__LINE__));
            
            return false;
        }

        if($flexibleHrTo_dt->format('H:i:s') < $onDuty_dt->format('H:i:s')){            
            $msg = 'flexible hour to cannot be less than On Duty Time';
            Log::error($msg.$this->log_suffix(__LINE__));
            return false;
        }
    }
    
    public function lateFeeComputation(){
        //TODO: 
        /**** Calculation for late Fee ****/
        return false;
        /* if(empty($this->lateHours)){
            return false;
        }

        $empId = $this->data['emp_id'];
        $attendanceDate = $this->data['att_date'];

        $this->ci->load->helper('actions/attendance/late_fee_computation_helper');
        $obj = new late_fee_computation_helper($this->lateHours, $empId, $attendanceDate);
        $amountForPerMinute = $obj->compute();

        $this->lateFee = ($amountForPerMinute > 0)
            ? $this->lateHours * $amountForPerMinute
            : 0; */
    }

    function log_suffix($line_no) : string
    {        
        $msg = "( $this->data['emp_id'] |  $this->data['ECode'] | $this->data['Ename2'] )";
        $msg .= " | companyId: $this->companyId \t on file:  " . __CLASS__ ." \tline no : {$line_no}";
        return $msg;
    }
}
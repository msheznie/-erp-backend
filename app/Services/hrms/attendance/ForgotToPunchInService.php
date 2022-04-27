<?php
namespace App\Services\hrms\attendance;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\helper\NotificationService;

class ForgotToPunchInService{
    private $companyId;
    private $date;
    private $time;

    private $mailSubject = 'Attendance Notification';

    private $dayId;
    private $shiftMasters;
    
    public function __construct($companyId, $date, $time)
    {
        $this->companyId = $companyId;
        $this->date = $date;
        $this->time = $time;
    }

    public function run(){
        $this->getDayId();
        $this->getShiftMasters();
        
        if($this->shiftMasters->count() == 0){
            return;
        }

        $this->processOverShifts();
    }

    public function getShiftMasters(){ 
        //TODO:  validate the on duty time with the job processing time
        $this->shiftMasters = DB::table('srp_erp_pay_shiftmaster AS m')
            ->selectRaw("m.shiftID, m.Description, d.dayID, d.onDutyTime, d.offDutyTime, d.dayID")
            ->join('srp_erp_pay_shiftdetails AS d', 'd.shiftID', '=', 'm.shiftID')
            ->where('d.dayID', $this->dayId)
            //->where('d.onDutyTime', 0)
            ->where('d.isWeekend', 0)
            ->where('m.shiftID', 2)
            ->where('m.companyID', $this->companyId)
            ->get();
    }

    public function processOverShifts(){
        foreach ($this->shiftMasters as $key => $shift) {
            //TODO: validate whether this job processed for this shift master

            $empArr = $this->empAssignedWithShift($shift->shiftID);
            if(empty($empArr)){
                continue;
            }

            $onLeaveEmp = $this->empOnLeave($empArr);
            $empArr = array_values( array_diff($empArr, $onLeaveEmp) ); //except on leave employees
            if(empty($empArr)){
                continue;
            }

            $notPunched = $this->empForgotToPunchIn($empArr);
            if($notPunched->count() == 0){
                continue;
            }
            
            $this->notify($notPunched);
        }
    }
    
    public function empAssignedWithShift($shiftId){
        $empArr = DB::table('srp_erp_pay_shiftemployees')
            ->select('empID') 
            ->where('shiftID', $shiftId) 
            ->whereRaw("('{$this->date}' BETWEEN startDate and endDate)")
            ->where('companyID', $this->companyId)
            ->get();

        if($empArr->count() == 0){
            return [];
        }
        
        return $empArr->pluck('empID')->toArray();
    }

    public function empOnLeave($empArr){
        $onLeaveEmp = DB::table('srp_erp_leavemaster')
            ->select('empID') 
            ->where('companyID', $this->companyId)
            ->where('approvedYN', 1)
            ->whereIn('empID', $empArr) 
            ->whereRaw("('{$this->date}' BETWEEN startDate and endDate)")            
            ->get();

        if($onLeaveEmp->count() == 0){
            return [];
        }
        
        return $onLeaveEmp->pluck('empID')->toArray();
    }

    public function empForgotToPunchIn($empArr){        
        $notPunched = DB::table('srp_employeesdetails AS e')
            ->selectRaw('EIdNo, ECode, Ename2, EEmail')     
            ->where('e.Erp_companyID', $this->companyId)
            ->whereIn('e.EIdNo', $empArr)
            ->whereNotExists(function($q) use ($empArr){
                $q->select('l.empID')
                ->from('srp_erp_pay_empattendancetemptable AS t')
                ->join('srp_erp_empattendancelocation AS l', function($join){
                    $join->on('l.deviceID', '=', 't.device_id')
                        ->on('t.empMachineID', '=', 'l.empMachineID');
                })                
                ->where('t.companyID', $this->companyId)
                ->where('t.attDate', $this->date)
                ->whereIn('l.empID', $empArr)
                ->whereColumn('e.EIdNo', 'l.empID')
                ->groupBy('l.empID');
            })                       
            ->get(); 
        
        return $notPunched;
    }

    public function notify($empArr){        
        foreach ($empArr as $emp) {            
            $mail_body = "Dear {$emp->Ename2},<br/>";
            $mail_body .= "You have missed punching in"; 

            //echo '<pre>'; print_r($mail_body); echo '</pre>'; exit;

            $empEmail = $emp->EEmail;
            $empEmail = 'nasik@osos.om';
            $subject = $this->mailSubject;

            NotificationService::emailNotification($this->companyId, $subject, $empEmail, $mail_body);
        }
    }

    public function getDayId(){
        $dayName = Carbon::parse($this->date)->format('l');        
        $this->dayId = DB::table('srp_weekdays')
            ->select('DayID')
            ->where('DayDesc', $dayName)
            ->value('DayID');        
    }

    /* TODO:     
     - check employee leave ( half day leave concern)
    */
}
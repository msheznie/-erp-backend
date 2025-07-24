<?php
namespace App\Services\hrms\attendance;

use App\enums\modules\Modules;
use App\enums\shift\Shifts;
use App\Services\hrms\modules\HrModuleAssignService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\helper\NotificationService;
use App\Traits\AbsentNotificationTrait;

class AbsentNotificationNonCrossDayService{
    private $companyId;
    private $date;
    private $time;
    private $processedFor;
    private $setupHour;

    private $mailSubject = 'Absent Notification';

    private $dayId;
    private $dayName;
    private $proceedShifts = [];
    private $shiftMasters;
    private $isShiftModule;
    use AbsentNotificationTrait;

    public function __construct($companyId, $date, $time, $companyScenarioId)
    {
        $this->companyId = $companyId;
        $this->date = $date;
        $this->time = $time;

        $this->processedFor = Carbon::parse($date.' '.$time)->format('Y:m:d H:i:s');
        $this->isShiftModule = HrModuleAssignService::checkModuleAvailability($this->companyId, Modules::SHIFT);
        $this->setupHour = ($hour = NotificationService::getHours($companyScenarioId)) === '' ? 2 : $hour;

    }

    public function run(){

        if($this->isShiftModule){
            return $this->newShiftProcess();
        }

        return $this->defaultProcess();

    }

    function newShiftProcess(){
        if($this->isHoliday()){
            return;
        }

        $this->getDayId();
        $this->loadProceedShifts();
        $this->getShiftData();

        if($this->shiftMasters->count() == 0){
            $this->insertToLogTb(
                [ 'message'=> 'No data found to proceed Absent Notification']
            );
            return;
        }

        $this->processOverShifts();

    }

    function getShiftData(){

        $shFixed = Shifts::FIXED;
        $shRota = Shifts::ROTA;

        $offDutyTime = Carbon::parse($this->time)->subHours($this->setupHour)->format('H:i:s');

        $fixedJoin = "SELECT sm.shiftID, sd.onDutyTime, sd.offDutyTime, sm.shiftType, sd.is_cross_day, 
                she.schedule_date, sm.Description
                FROM hr_shift_schedule_details AS she 
                JOIN srp_erp_pay_shiftmaster AS sm ON  sm.shiftID = she.shift_id 
                JOIN srp_erp_pay_shiftdetails AS sd ON sd.shiftID = sm.shiftID 
                AND sd.weekDayNo = WEEKDAY(she.schedule_date)
                WHERE sm.companyID = {$this->companyId}
                AND sm.shiftType = {$shFixed} 
                AND she.schedule_date = '{$this->date}'
                AND sd.is_cross_day = 0 ";

        $rotaUnion = " UNION ALL 
                SELECT sm.shiftID, sd.onDutyTime, sd.offDutyTime, sm.shiftType, sd.is_cross_day, she.schedule_date, 
                sm.Description
                FROM hr_shift_schedule_details AS she 
                JOIN srp_erp_pay_shiftmaster AS sm ON  sm.shiftID = she.shift_id 
                JOIN srp_erp_pay_shiftdetails AS sd ON sd.shiftID = sm.shiftID 
                WHERE sm.companyID = {$this->companyId}
                AND sm.shiftType = {$shRota} 
                AND she.schedule_date = '{$this->date}'
                AND sd.is_cross_day = 0";

        $query = $fixedJoin . $rotaUnion;

        $data = DB::table(DB::raw("($query) as shiftDetails"))
            ->where(DB::raw('TIME(shiftDetails.offDutyTime)'), '<=', $offDutyTime);

        if ($this->proceedShifts) {
            $data = $data->whereNotIn('shiftDetails.shiftID', $this->proceedShifts);
        }

        $data = $data->groupBy('shiftDetails.shiftID')->get();

        $this->shiftMasters = $data;
    }

    function defaultProcess(){

        if($this->isHoliday()){
            return;
        }

        $this->getDayId();
        $this->loadProceedShifts();
        $this->getShiftMasters();
         
        if($this->shiftMasters->count() == 0){
            $this->insertToLogTb(
                [ 'message'=> 'No data found to proceed Absent Notification']
            );
            return;
        }

        $this->processOverShifts();
    }

    public function getShiftMasters(){

        $offDutyTime = Carbon::parse($this->time)->subHours($this->setupHour)->format('H:i:s');

        $data = DB::table('srp_erp_pay_shiftmaster AS m')
            ->selectRaw("m.shiftID, m.Description, d.dayID, d.onDutyTime, d.offDutyTime, d.dayID")
            ->join('srp_erp_pay_shiftdetails AS d', 'd.shiftID', '=', 'm.shiftID')
            ->where('d.dayID', $this->dayId)
            ->where('d.offDutyTime', '<=', $offDutyTime)
            ->where('d.isWeekend', 0)
            ->where('m.companyID', $this->companyId);

        if($this->proceedShifts){
            $data = $data->whereNotIn('m.shiftID', $this->proceedShifts);
        }
        
        $data = $data->get();

        $this->shiftMasters = $data;
    }

    public function processOverShifts(){
        foreach ($this->shiftMasters as $key => $shift) {

            $empArr = $this->empAssignedWithShift($shift->shiftID);

            if(empty($empArr)){
                $this->insertToLogTb(
                    [
                        'shiftId'=> $shift->shiftID,
                        'message'=> 'No employee assigned to this shift'
                    ],
                    'data'
                );
                continue;
            }

            $onLeaveEmp = $this->empOnLeave($empArr);

            if($onLeaveEmp){
                $this->insertToLogTb(
                    [ 'shiftId'=> $shift->shiftID, 'onLeaveEmp'=> $onLeaveEmp]
                );
            }

            $empArr = array_values( array_diff($empArr, $onLeaveEmp) ); 
            if(empty($empArr)){
                $this->insertToLogTb(
                    [
                        'shiftId'=> $shift->shiftID,
                        'message'=> 'Looks like all employees are on leave assigned with this shift'
                    ],
                    'data'
                );
                continue;
            }

            $empOnTrip = $this->empOnTrip($empArr);

            if(!empty($empOnTrip)){
                $empOnTripString = implode(', ', $empOnTrip);
                $this->insertToLogTb(
                    [
                        'shiftId'=> $shift->shiftID,
                        'message'=> "Following employees on trip : $empOnTripString"
                    ],
                    'data'
                );
            }

            $empArr = array_values( array_diff($empArr, $empOnTrip) );

            if(empty($empArr)){
                $this->insertToLogTb(
                    [
                        'shiftId'=> $shift->shiftID,
                        'message'=> 'Looks like all employees are on trip assigned with this shift'
                    ],
                    'data'
                );
                continue;
            }

            $notPunched = $this->empForgotToPunchIn($empArr);
            if($notPunched->count() == 0){
                $this->insertToLogTb(
                    [
                        'shiftId'=> $shift->shiftID,
                        'message'=> 'All employees assigned to this shift have already punched in or out'
                    ],
                    'data'
                );
                continue;
            }
            
            $this->insertToLogTb(
                [
                    'shiftId'=> $shift->shiftID,
                    'message'=> 'Employees who are not punched-in and punched-out with assigned shift',
                    'notPunchedEmp'=> $notPunched->pluck('EIdNo')->toArray()
                ],
                'data'
            );

            $this->notify($notPunched, $shift);
        }
    }
    
    public function empAssignedWithShift($shiftId){
        if ($this->isShiftModule) {

            $empArr = DB::table('hr_shift_schedule_details')
                ->select('emp_id as empID')
                ->where('shift_id', $shiftId)
                ->where('schedule_date', $this->date)
                ->where('company_id', $this->companyId)
                ->get();
        } else {

            $empArr = DB::table('srp_erp_pay_shiftemployees')
                ->select('empID')
                ->where('shiftID', $shiftId)
                ->whereRaw("('{$this->date}' BETWEEN startDate and endDate)")
                ->where('companyID', $this->companyId)
                ->get();

        }

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

    public function empOnTrip($empArr) {
        $onTripEmp = DB::table('hr_trip_request_master')
            ->select('req_emp_id_confirmed as empID')
            ->where('company_id', $this->companyId)
            ->where('rpt_manager_confirmed_yn', 1)
            ->whereIn('req_emp_id_confirmed', $empArr)
            ->whereRaw("('{$this->date}' BETWEEN date_travel AND date_return)")
            ->get();

        if($onTripEmp->count() == 0){
            return [];
        }

        return $onTripEmp->pluck('empID')->toArray();
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

    public function notify($empArr, $shiftDet){
        $bodyContent = $this->getShiftDetView($shiftDet);
        $inValidEmails = [];
        
        foreach ($empArr as $emp) {
            $mailBody = "Dear {$emp->Ename2},<br/>";
            $mailBody .= "You have missed both clock-in and clock-out today ({$this->date}), and your attendance has been marked as Absent"; 
            $mailBody .= $bodyContent;

            $empEmail = $emp->EEmail;
            $subject = $this->mailSubject;

            if (!filter_var($empEmail, FILTER_VALIDATE_EMAIL)) {
                $inValidEmails[] = $emp->EIdNo;
            } 
            else
            {
                NotificationService::emailNotification($this->companyId, $subject, $empEmail, $mailBody);
            }
        }

        if (!empty($inValidEmails)) {
            $this->insertToLogTb(
                [
                    'message' => 'Employees who have invalid email address',
                    'invalidEmail' => $inValidEmails
                ],
                'data'
            );
        }
    }

    function getShiftDetView($shiftDet){

        return $this->getEmailBodyContent($shiftDet);
    }

    public function loadProceedShifts(){

        $data = DB::table('job_logs')
        ->selectRaw("`log_data`->'$.\"shiftId\"' AS shiftId")
        ->where('company_id', $this->companyId)
        ->whereDate('processed_for', $this->date)
        ->where('scenario_id', 50)
        ->where('log_type', 'data') 
        ->get();

        if($data->count() == 0){
            return;
        }

        $data = $data->pluck('shiftId')->toArray(); 
        
        $this->proceedShifts = array_values( array_filter($data) );

        $this->insertToLogTb(
            [ 'processedShifts'=> $this->proceedShifts ]
        );
    }

    public function getDayId(){
        $this->dayName = Carbon::parse($this->date)->format('l');        
        
        $this->dayId = DB::table('srp_weekdays')
            ->select('DayID')
            ->where('DayDesc', $this->dayName)
            ->value('DayID');        
    }

    public function isHoliday(){
                
        $holiday = DB::table('srp_erp_calender')
            ->select('holiday_flag')
            ->where('companyID', $this->companyId)
            ->where('fulldate', $this->date)
            ->value('holiday_flag');
            
        if($holiday){
            $this->insertToLogTb(
                [ 'message'=> 'Holiday']
            );
        }
        
        return $holiday;
    }

    public function insertToLogTb($logData, $logType = 'info'){
        $logData = json_encode($logData);

        $data = [
            'company_id'=> $this->companyId,
            'module'=> 'Attendance',
            'scenario_id'=> 50,
            'description'=>'Absent Notification',
            'processed_for'=> $this->processedFor,
            'logged_at'=> Carbon::now(),
            'log_type'=> $logType,
            'log_data'=> $logData,
        ];

        DB::table('job_logs')->insert($data);
    }
}

<?php
namespace App\Services\hrms\attendance;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\helper\NotificationService;
use Illuminate\Support\Facades\Log;

class ForgotToPunchInService{
    private $companyId;
    private $date;
    private $time;
    private $processedFor;

    private $mailSubject = 'Attendance Notification';

    private $dayId;
    private $dayName;
    private $proceedShifts = [];
    private $shiftMasters;
    
    public function __construct($companyId, $date, $time)
    {
        $this->companyId = $companyId;
        $this->date = $date;
        $this->time = $time;

        $this->processedFor = Carbon::parse($date.' '.$time)->format('Y:m:d H:i:s');
    }

    public function run(){

        $isHoliday = $this->isHoliday();

        if($isHoliday){
            return;
        }

        $this->getDayId();
        $this->loadProceedShifts();
        $this->getShiftMasters();
         
        if($this->shiftMasters->count() == 0){
            $this->insertToLogTb(
                [ 'message'=> 'No data found to proceed (punch-in)'], 'info'
            );
            return;
        }

        $this->processOverShifts();
    }

    public function getShiftMasters(){
        // we have to check the punch-in status after two hours from on-duty-time 
        $onDutyTime = Carbon::parse($this->time)->subHours(2)->format('H:i:s');

        $data = DB::table('srp_erp_pay_shiftmaster AS m')
            ->selectRaw("m.shiftID, m.Description, d.dayID, d.onDutyTime, d.offDutyTime, d.dayID")
            ->join('srp_erp_pay_shiftdetails AS d', 'd.shiftID', '=', 'm.shiftID')
            ->where('d.dayID', $this->dayId)
            ->where('d.onDutyTime', '<=', $onDutyTime)
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
            $shiftID = $shift->shiftID;
            $empArr = $this->empAssignedWithShift($shiftID);

            if(empty($empArr)){
                $this->insertToLogTb(
                    [
                        'shiftId'=> $shiftID,
                        'message'=> 'No employee assigned to this shift (punch-in)'
                    ],
                    'data'
                );
                continue;
            }

            $onLeaveEmp = $this->empOnLeave($empArr);

            if($onLeaveEmp){
                $this->insertToLogTb(
                    [ 'shiftId'=> $shiftID, 'onLeaveEmp'=> $onLeaveEmp]
                );
            }

            $empArr = array_values( array_diff($empArr, $onLeaveEmp) ); //except on leave employees
            if(empty($empArr)){
                $this->insertToLogTb(
                    [
                        'shiftId'=> $shiftID,
                        'message'=> 'Looks like all employees are on leave assigned with this shift (punch-in)'
                    ],
                    'data'
                );
                continue;
            }

            $notPunched = $this->empForgotToPunchIn($empArr);
            if($notPunched->count() == 0){
                $this->insertToLogTb(
                    [
                        'shiftId'=> $shiftID,
                        'message'=> 'Looks like all employees are punched-in assigned with this shift'
                    ],
                    'data'
                );
                continue;
            }
            
            $this->insertToLogTb(
                [
                    'shiftId'=> $shiftID,
                    'message'=> 'Employees who are not punched-in yet assigned with this shift',
                    'notPunchedEmp'=> $notPunched->pluck('EIdNo')->toArray()
                ],
                'data'
            );

            $this->notify($notPunched, $shift);
        }
    }
    
    public function empAssignedWithShift($shiftId){
        $empArr = DB::table('srp_erp_pay_shiftemployees')
            ->select('empID') 
            ->where('shiftID', $shiftId) 
            ->whereRaw("('{$this->date}' BETWEEN startDate and endDate)")
            ->where('companyID', $this->companyId)
            // ->where('isActive', 1)
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

    public function notify($empArr, $shiftDet){
        $bodyContent = $this->getShiftDetView($shiftDet);

        foreach ($empArr as $emp) {
            $mailBody = "Dear {$emp->Ename2},<br/>";
            $mailBody .= "You have missed to clock in today ({$this->date})."; 
            $mailBody .= $bodyContent;

            $empEmail = $emp->EEmail;
            $subject = $this->mailSubject;

            NotificationService::emailNotification($this->companyId, $subject, $empEmail, $mailBody);
        }
    }

    function getShiftDetView($shiftDet){
        $body = "<br/><br/><b>Shift Details</b><br/>";
        $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center;border: 1px solid black;">Shift</th>
                        <th style="text-align: center;border: 1px solid black;">Day</th>
                        <th style="text-align: center;border: 1px solid black;">Clock In</th>
                        <th style="text-align: center;border: 1px solid black;">Clock Out</th>                                            
                    </tr>
                </thead>';
        $body .= '<tbody>';
        $body .= '<tr>
                    <td style="text-align:left;border: 1px solid black;">'.$shiftDet->Description.'</td>
                    <td style="text-align:left;border: 1px solid black;">'.$this->dayName.'</td>
                    <td style="text-align:left;border: 1px solid black;">'.$shiftDet->onDutyTime.'</td>
                    <td style="text-align:left;border: 1px solid black;">'.$shiftDet->offDutyTime.'</td>
                 </tr>
                 </tbody>
                 </table>';
        
        return $body;
    }

    public function loadProceedShifts(){
        //punch-in verification already processed shifts

        $data = DB::table('job_logs')
        ->selectRaw("`log_data`->'$.\"shiftId\"' AS shiftId")
        ->where('company_id', $this->companyId)
        ->whereDate('processed_for', $this->date)
        ->where('scenario_id', 15)
        ->where('log_type', 'data') // to validate we have to check only the log type => data
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
                [ 'message'=> 'Holiday'], 'info'
            );
        }
        
        return $holiday;
    }

    public function insertToLogTb($logData, $logType = 'info'){
        $logData = json_encode($logData);

        $data = [
            'company_id'=> $this->companyId,
            'module'=> 'HRMS',
            'scenario_id'=> 15,
            'processed_for'=> $this->processedFor,
            'logged_at'=> Carbon::now(),
            'log_type'=> $logType,
            'log_data'=> $logData,
        ];

        DB::table('job_logs')->insert($data);
    }
}
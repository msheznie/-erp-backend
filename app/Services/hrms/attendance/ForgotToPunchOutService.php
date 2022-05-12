<?php 
namespace App\Services\hrms\attendance;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\helper\NotificationService;

class ForgotToPunchOutService{
    private $companyId;
    private $date;
    private $processedFor;

    private $mailSubject = 'Attendance Notification';

    private $dayId;
    private $notPunched;
    
    public function __construct($companyId, $date)
    {
        $this->companyId = $companyId;
        $this->date = $date;

        $this->processedFor = Carbon::parse($date)->format('Y:m:d');
    }

    public function run(){
        $this->empForgotToPunchOut();

        if($this->notPunched->count() == 0){
            $this->insertToLogTb(
                [ 'message'=> 'No data found to proceed (punch-out)'], 'info'
            );
            return;
        }

        $empArr = $this->getEmployeeDet();
        if($empArr->count() == 0){
            $this->insertToLogTb(
                [ 'message'=> 'Employee details not found to proceed (punch-out)'], 'info'
            );
            return;
        }

        $this->insertToLogTb(
            [ 'notPunchedOutEmp'=> $empArr->pluck('EIdNo')->toArray() ], 'info'
        );

        $this->notify($empArr);
        
    }

    public function empForgotToPunchOut(){
        $this->notPunched = DB::table('srp_erp_pay_empattendancetemptable AS t')
            ->select('l.empID')
            ->join('srp_erp_empattendancelocation AS l', function($join){
                $join->on('l.deviceID', '=', 't.device_id')
                    ->on('t.empMachineID', '=', 'l.empMachineID');
            })
            ->where('t.companyID', $this->companyId)
            ->where('t.attDate', $this->date)
            ->groupBy('l.empID')
            ->having(DB::raw('count(t.autoID)'), 1)
            ->pluck('l.empID'); 
    }
    
    public function getEmployeeDet(){
        $empArr = $this->notPunched->toArray();

        return DB::table('srp_employeesdetails AS e')
            ->selectRaw('EIdNo, ECode, Ename2, EEmail')            
            ->whereIn('e.EIdNo', $empArr)
            ->where('e.Erp_companyID', $this->companyId)            
            ->get(); 
    }

    public function notify($empArr){ 
        foreach ($empArr as $emp) {
            $mailBody = "Dear {$emp->Ename2},<br/>";
            $mailBody .= "You have missed to clock out yesterday ({$this->date}).";  

            $empEmail = $emp->EEmail;
            $subject = $this->mailSubject;

            NotificationService::emailNotification($this->companyId, $subject, $empEmail, $mailBody);
        }
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
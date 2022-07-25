<?php
namespace App\Services\hrms\attendance;

use Carbon\Carbon;
use App\Models\SrpEmployeeDetails;
use Illuminate\Support\Facades\DB;
use App\helper\NotificationService;

class AttendanceWeeklySummaryService{
    private $companyId;
    private $date;
    private $processedFor;

    private $mailSubject = 'Attendance Notification';

    private $weekStart;
    private $weekEnd;
    private $shortageList;
    private $empArr;
    
    public function __construct($companyId, $date)
    {
        $this->companyId = $companyId;
        $this->date = $date;

        $this->processedFor = Carbon::parse($date)->format('Y:m:d');
    }

    public function run(){
        $this->getDatePeriod();
        $this->getRec();

        if($this->shortageList->count() == 0){
            $this->insertToLogTb(
                [ 'message'=> 'No data found to proceed (weekly-summary)'], 'info'
            );
            return;
        }

        $empArr = $this->shortageList->pluck('empID')->toArray();
        $this->empArr = array_unique($empArr);
        $this->insertToLogTb(        
            [ 
                'weeklySummaryEmp'=> $this->empArr, 
                'weekStart'=> $this->weekStart, 'weekEnd'=> $this->weekEnd
            ], 'info'
        );

        $this->loadEmpData();
        $this->sendNotification();
    }

    public function getRec()
    {
        $this->shortageList = DB::table('srp_erp_pay_empattendancereview AS t')
            ->selectRaw("t.empID, t.checkIn, t.checkOut,
            TIME_FORMAT( TIMEDIFF(t.offDuty, t.onDuty), '%H:%i') AS shiftHours ,
            TIME_FORMAT( TIMEDIFF(t.checkOut, t.checkIn), '%H:%i') AS workedHours
            ")
            ->where('t.companyID', $this->companyId)
            ->whereIn('t.presentTypeID', [1, 2]) //present and late to shift
            ->where('t.isNormalDay', 1)
            ->whereBetween('t.attendanceDate', [$this->weekStart, $this->weekEnd])            
            ->whereNotNull('t.onDuty')
            ->whereNotNull('t.offDuty')
            ->whereNotNull('t.checkIn')
            //even though checkOut time is not set, we are going to consider that day. and work hours will be zero for that day            
            //->whereNotNull('t.checkOut')
            ->get();
    }

    function sendNotification(){
        $this->shortageList = $this->shortageList->groupBy('empID');
        
        foreach ($this->shortageList as $empId => $row) {
            $empResp = $this->getEmpData($empId);

            if(!$empResp['status']){
                continue;
            } 

            $mailBody = "Dear {$empResp['data']['Ename2']},<br/>";            
            $mailBody .= $this->detailTbView($row); 

            $empEmail = $empResp['data']['EEmail'];
            $subject = $this->mailSubject;

            NotificationService::emailNotification($this->companyId, $subject, $empEmail, $mailBody);

        }
    }

    function detailTbView($data){
        $summary = $this->getSummaryData($data); 
                
        $headerStyle = "text-align:left;border: 1px solid black; font-weight: bold; padding-left: 15px";
        $detailStyle = "text-align:left;border: 1px solid black; padding-left: 15px";

        $body = "your attendance summary for this week ( {$this->weekStart} to {$this->weekEnd})";
        $body .= "<br/><br/>";
        $body .= '<table style="width:80%; max-width:450px; border: 1px solid black;border-collapse: collapse;">';
        $body .= '<tbody>';
        $body .= '<tr>
                    <td style="'.$headerStyle.'">Total present days</td>
                    <td style="'.$detailStyle.'">'.$summary['totPresentDays'].'</td>               
                 </tr>
                 <tr>
                    <td style="'.$headerStyle.'">Total shift hours</td>
                    <td style="'.$detailStyle.'">'.$summary['totShiftHours'].'</td>               
                 </tr>
                 <tr>
                    <td style="'.$headerStyle.'">Worked hours</td>
                    <td style="'.$detailStyle.'">'.$summary['totWorkHours'].'</td>               
                 </tr>
                 <tr>
                    <td style="'.$headerStyle.'">Shortage</td>
                    <td style="'.$detailStyle.'">'.$summary['shortage'].'</td>               
                 </tr>                 
                 </tbody>
                 </table>';
        
        return $body;
    }

    function getSummaryData($data){
        $totPresentDays = $data->count();
        $totShiftHours = $this->getTotalHours( $data->pluck('shiftHours')->toArray() );
        $totWorkHours = $this->getTotalHours( $data->pluck('workedHours')->toArray() );

        $shortage = '-';

        if($totShiftHours > $totWorkHours){
            $shortage = $totShiftHours - $totWorkHours;
            $shortage = AttendanceDailySummaryService::totalMinutesToHours($shortage);
        }

        $totShiftHours = AttendanceDailySummaryService::totalMinutesToHours($totShiftHours);
        $totWorkHours = AttendanceDailySummaryService::totalMinutesToHours($totWorkHours);

        return [
            'totPresentDays'=> $totPresentDays,
            'totShiftHours'=> $totShiftHours,
            'totWorkHours'=> $totWorkHours,
            'shortage'=> $shortage,
        ];
    }

    function getTotalHours($hoursArr){
        $totMinutes = 0;

        foreach($hoursArr as $value){
            $totMinutes += AttendanceDailySummaryService::hoursToMinutes($value);
        }

        return $totMinutes;
    }

    function getEmpData($empId){
        if(!array_key_exists($empId, $this->empData)){
            return ['status'=> false];
        }
        
        return [
            'status'=> true, 
            'data'=> $this->empData[$empId][0]
        ];
    }

    function loadEmpData(){
        $this->empData = SrpEmployeeDetails::select('EIdNo', 'ECode', 'Ename2', 'EEmail')
            ->where('Erp_companyID', $this->companyId)
            ->whereIn('EIdNo', $this->empArr)
            ->get();

        if($this->empData->count() == 0){
            $this->empData = [];
            return;
        }

        $this->empData = $this->empData->groupBy('EIdNo')->toArray();
    }

    function getDatePeriod(){
        /* 
            As per the story we are going to run this service on friday
            so the week start date should be on sunday
            and week end date should be on thursday (Assuming only 5 days will be working days)
        */

        $this->weekStart = Carbon::parse($this->date)->subDays(5)->format('Y-m-d');
        $this->weekEnd = Carbon::parse($this->date)->subDay()->format('Y-m-d');
    }

    public function insertToLogTb($logData, $logType = 'info'){
        $logData = json_encode($logData);

        $data = [
            'company_id'=> $this->companyId,
            'module'=> 'HRMS',
            'scenario_id'=> 17,
            'processed_for'=> $this->processedFor,
            'logged_at'=> Carbon::now(),
            'log_type'=> $logType,
            'log_data'=> $logData,
        ];

        DB::table('job_logs')->insert($data);
    }
}
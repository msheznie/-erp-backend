<?php
namespace App\Services\hrms\attendance;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\helper\NotificationService;

class AttendanceDailySummaryService{

    private $companyId;
    private $date;
    private $processedFor;

    private $mailSubject = 'Attendance Notification';    
    private $shortageList;
    
    public function __construct($companyId, $date)
    {
        $this->companyId = $companyId;
        $this->date = $date;

        $this->processedFor = Carbon::parse($date)->format('Y:m:d');
    }

    public function run(){
        $this->getRec();

        if($this->shortageList->count() == 0){
            $this->insertToLogTb(
                [ 'message'=> 'No data found to proceed (work-hour-shortage)'], 'info'
            );
            return;
        }

        $this->insertToLogTb(
            [ 'shortageEmp'=> $this->shortageList->pluck('empID')->toArray() ], 'info'
        );
        $this->sendNotification();
    }

    public function getRec()
    {
        $this->shortageList = DB::table('srp_erp_pay_empattendancereview AS t')
            ->selectRaw("t.empID, e.ECode, e.Ename2, e.EEmail, t.checkIn, t.checkOut,
            TIME_FORMAT( TIMEDIFF(t.offDuty, t.onDuty), '%H:%i') AS shiftHours ,
            TIME_FORMAT( TIMEDIFF(t.checkOut, t.checkIn), '%H:%i') AS workedHours
            ")
            ->join('srp_employeesdetails AS e', 'e.EIdNo', '=', 't.empID')
            ->where('t.companyID', $this->companyId)
            ->whereDate('t.attendanceDate', $this->date)
            ->whereIn('t.presentTypeID', [1, 2]) //present and late to shift
            ->where('t.isNormalDay', 1)            
            ->whereNotNull('t.onDuty')
            ->whereNotNull('t.offDuty')
            ->whereNotNull('t.checkIn')
            ->whereNotNull('t.checkOut')
            ->whereRaw("TIMEDIFF(t.checkOut , t.checkIn) < TIMEDIFF(t.offDuty, t.onDuty)")            
            ->get();
    }

    function sendNotification(){
        foreach($this->shortageList as $row){
            $mailBody = "Dear {$row->Ename2},<br/>";
            $mailBody .= "you have a shortage as showing below";
            $mailBody .= $this->detailTbView($row);

            $empEmail = $row->EEmail;
            $subject = $this->mailSubject;

            NotificationService::emailNotification($this->companyId, $subject, $empEmail, $mailBody);
        }
    }

    function detailTbView($data){
        
        $shortage = $this->calculateShortage($data);
        $checkIn = date('h:i A', strtotime($data->checkIn));
        $checkOut = date('h:i A', strtotime($data->checkOut));
        
        
        $headerStyle = "text-align:left;border: 1px solid black; font-weight: bold; padding-left: 15px";
        $detailStyle = "text-align:left;border: 1px solid black; padding-left: 15px";

        $body = "<br/><br/>";
        $body .= '<table style="width:80%; max-width:450px; border: 1px solid black;border-collapse: collapse;">';
        $body .= '<tbody>';
        $body .= '<tr>
                    <td style="'.$headerStyle.'">Date</td>
                    <td style="'.$detailStyle.'">'.$this->date.'</td>               
                 </tr>
                 <tr>
                    <td style="'.$headerStyle.'">Clock in</td>
                    <td style="'.$detailStyle.'">'.$checkIn.'</td>               
                 </tr>
                 <tr>
                    <td style="'.$headerStyle.'">Clock out</td>
                    <td style="'.$detailStyle.'">'.$checkOut.'</td>               
                 </tr>
                 <tr>
                    <td style="'.$headerStyle.'">Worked hours</td>
                    <td style="'.$detailStyle.'">'.$data->workedHours.'</td>               
                 </tr>
                 <tr>
                    <td style="'.$headerStyle.'">Shortage</td>
                    <td style="'.$detailStyle.'">'.$shortage.'</td>               
                 </tr>
                 </tbody>
                 </table>';
        
        return $body;
    }

    function calculateShortage($data){
        $totWorkedHoursInMinute = self::hoursToMinutes($data->workedHours);
        $totShiftHoursInMinute = self::hoursToMinutes($data->shiftHours);
        $totShortageInMinute = $totShiftHoursInMinute - $totWorkedHoursInMinute;
        return self::totalMinutesToHours($totShortageInMinute);
    }

    public static function hoursToMinutes($hoursAndMinute){
        $hours = date('H', strtotime($hoursAndMinute)) * 60;
        return $hours + date('i', strtotime($hoursAndMinute));
    }

    public static function totalMinutesToHours($totalMinutes){
        $minutes = ($totalMinutes % 60);
        $minutes = str_pad($minutes, 2, 0, STR_PAD_LEFT);
        
        return floor($totalMinutes / 60).':'.$minutes;
    }

    public function insertToLogTb($logData, $logType = 'info'){
        $logData = json_encode($logData);

        $data = [
            'company_id'=> $this->companyId,
            'module'=> 'HRMS',
            'scenario_id'=> 16,
            'processed_for'=> $this->processedFor,
            'logged_at'=> Carbon::now(),
            'log_type'=> $logType,
            'log_data'=> $logData,
        ];

        DB::table('job_logs')->insert($data);
    }
}
<?php

namespace App\Services\hrms\travelRequest;

use Collator;
use Exception;
use Carbon\Carbon;
use App\helper\CommonJobService;
use App\Models\Company;
use App\Models\NotificationCompanyScenario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class TravelRequestNotificationService
{
    private $companyId;
    private $id;
    private $date;
    private $isScenarioActive;
    private $notifyList;
    private $tripMaster;
    private $pdfName;
    private $tripRequestBookings;
    private $documentCode;


    public function __construct($companyId,  $id,$tripMaster,$tripRequestBookings)
    {
        $this->companyId = $companyId;
        $this->id = $id;
        $this->date = Carbon::now()->format('Y-m-d H:i:s');
        $this->isScenarioActive = false;
        $this->notifyList = [];
        $this->tripMaster = $tripMaster;
        $this->pdfName = null;
        $this->tripRequestBookings = $tripRequestBookings;
        $this->documentCode = $this->tripMaster['document_code'];
    }

    function execute()
    { 
        $this->validateNotificationScenarioActive();
        if (!$this->isScenarioActive) {
            return false;
        }
        $this->validateNotifyEmployeeExists();
        if (empty($this->notifyList)) {
            return false;
        }
        $this->sendEmail();
        $this->insertToLogTb([ 'Document Code'=> $this->documentCode ,'Message'=> 'execution successfully completed']);
    }

    public function sendEmail()
    { 
        $this->insertToLogTb([ 'Document Code'=> $this->documentCode ,'Message'=> 'Email Function Triggered']);
        $msg = '';
        $logType = 'info';
        $this->generateTravelRequestPdf(); 
        $dataEmail['attachmentFileName'] = $this->pdfName; 
    

        foreach ($this->notifyList as $val) {
            if(($val['employee']['discharegedYN'] == 0) && ($val['employee']['ActivationFlag'] == -1) && ($val['employee']['empLoginActive'] == 1) && ($val['employee']['empActive'] == 1)){
                $dataEmail['empEmail'] = $val['employee']['empEmail'];
                $dataEmail['companySystemID'] = $this->companyId;
                $temp = '<p>Dear ' . $val['employee']['empFullName'] . ', <br /></p><p> Please find the attached document
            '.$this->documentCode.' for your further arrangements and action.</p>';
                $dataEmail['emailAlertMessage'] = $temp;
                $dataEmail['alertMessage'] = trans('email.travel_request_alert', ['documentCode' => $this->documentCode]);
                $sendEmail = \Email::sendEmailErp($dataEmail);
                if (!$sendEmail["success"]) {
                    $msg = "Travel request notification not sent for {$val['employee']['empID']} | {$val['employee']['empFullName']} ";
                    $logType = 'error';
                }else {
                    $msg = "Travel request notification sent for {$val['employee']['empID']} | {$val['employee']['empFullName']} ";
                }
                $this->insertToLogTb(['Document Code'=> $this->documentCode ,'Message'=> $msg],$logType);
            }
        }
    }

    public function validateNotificationScenarioActive()
    {
        $notificationCompanyScenario = $this->getScenarioEmployees();
        $this->isScenarioActive = (!empty($notificationCompanyScenario)) ? true : false;
        if (!$this->isScenarioActive) {
            $this->insertToLogTb(['Document Code'=> $this->documentCode ,'Message'=> 'Notification scenario Does not exist or 
            does not active'],'error');  
        }
    }

    public function validateNotifyEmployeeExists()
    {
        $getNotifyEmployees = $this->getScenarioEmployees(true);
        $this->notifyList = (!empty($getNotifyEmployees) ? $getNotifyEmployees['user'] : []);
        if (empty($this->notifyList)) { 
            $this->insertToLogTb(['Document Code'=> $this->documentCode ,'Message'=> 'Employees Does not exists'],'error');  
        }
    }

    public function getScenarioEmployees($getEmployees = false)
    { 
        $getScenarioEmployees =  NotificationCompanyScenario::select('id')
            ->where('scenarioID', 20)
            ->where('companyID', $this->companyId)
            ->where('isActive', 1);

        if ($getEmployees) {
            $getScenarioEmployees = $getScenarioEmployees->with(['user' => function ($q) {
                $q->select('id', 'empID', 'companyScenarionID', 'isActive')
                    ->where('isActive', '=', 1)
                    ->with(['employee' => function ($q3) {
                        $q3->select('employeeSystemID', 'empFullName', 'empEmail', 'empID');
                    }]);
            }])
                ->whereHas('user', function ($query) {
                    $query->where('isActive', '=', 1);
                });
        }

        return $getScenarioEmployees->first();
    }

    public function generateTravelRequestPdf()
    {
        $companyData = $this->getCompanyData(); 
        $data = [
            'company' => $companyData,
            'masterData' => $this->tripMaster,
            'tripRequestBookings'=> $this->tripRequestBookings
        ];
        
        $html = view('print.travel_request', $data);
        $pdf = \App::make('dompdf.wrapper');

        $path = public_path() . '/uploads/emailAttachment';

        if (!file_exists($path)) {
            File::makeDirectory($path, 0777, true, true);
        }
        
        $nowTime = time();
        $documentCode = str_replace("/", "_", $this->documentCode);
        $fileName = "{$path}/travel_request_{$documentCode}_{$nowTime}.pdf";        

        $pdf->loadHTML($html)->setPaper('a4', 'portrait')->save($fileName);
        $this->pdfName = realpath($fileName);
        
        
        $this->insertToLogTb([ 'Document Code'=> $this->documentCode ,'Message'=> 'Email PDF generated']);
    }

    public function getCompanyData(){ 
        return Company::where('companySystemID', $this->companyId)
        ->first();
    }

    public function insertToLogTb($logData, $logType = 'info')
    {
        $logData = json_encode($logData);
        $data = [
            'company_id' => $this->companyId,
            'module' => 'HRMS',
            'description' => 'Travel Request Notification Scenario',
            'scenario_id' => 20,
            'processed_for' => $this->date,
            'logged_at' => $this->date,
            'log_type' => $logType,
            'log_data' => $logData,
        ];
        DB::table('job_logs')->insert($data);
    }
}

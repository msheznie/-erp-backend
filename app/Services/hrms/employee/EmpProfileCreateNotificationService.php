<?php

namespace App\Services\hrms\employee;

use Carbon\Carbon;
use App\Models\HrmsEmployeeManager;
use App\Models\NotificationCompanyScenario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class EmpProfileCreateNotificationService
{
    private $companyId;
    private $id;
    private $date;
    private $isScenarioActive;
    private $notifyList;
    private $masterDet;
    private $documentCode;
    private $dataType;


    public function __construct($companyId, $id, $masterDet, $dataType)
    {
        $this->companyId = $companyId;
        $this->id = $id;
        $this->date = Carbon::now()->format('Y-m-d H:i:s');
        $this->isScenarioActive = false;
        $this->notifyList = [];
        $this->masterDet = $masterDet;
        $this->documentCode = '';
        $this->dataType = $dataType;
    }

    function execute()
    { 
        if (empty($this->masterDet)) {
            $this->insertToLogTb(['Document Code' => '', 'Message' => 'Master details not found', 'error']);
            return false;
        }

        //$this->documentCode = $this->masterDet['documentCode'];
        $this->documentCode = 123;

        $this->validateNotificationScenarioActive();
        if (!$this->isScenarioActive) {
            return false;
        }

        $this->validateNotifyEmployeeExists();
        if (empty($this->notifyList)) {
            return false;
        }       

        $this->sendEmail();
        $this->insertToLogTb(['Document Code' => $this->documentCode, 'Message' => 'execution successfully completed']);
    }
  
    public function validateNotificationScenarioActive()
    {
        $notificationCompanyScenario = $this->getScenarioEmployees();
        $this->isScenarioActive = (!empty($notificationCompanyScenario)) ? true : false;
        if (!$this->isScenarioActive) {
            $this->insertToLogTb(['Document Code' => $this->documentCode, 'Message' => 'Notification scenario Does not exist or 
            does not active'], 'error');  
        }
    }

    public function validateNotifyEmployeeExists()
    {
        $getNotifyEmployees = $this->getScenarioEmployees(true);
        $this->notifyList = (!empty($getNotifyEmployees) ? $getNotifyEmployees['user'] : []);
        if (empty($this->notifyList)) { 
            $this->insertToLogTb(['Document Code' => $this->documentCode, 'Message' => 'Employees Does not exists'], 'error');
        }
    }

    public function getScenarioEmployees($getEmployees = false)
    { 
        $getScenarioEmployees =  NotificationCompanyScenario::select('id')
            ->where('scenarioID', 44)
            ->where('companyID', $this->companyId)
            ->where('isActive', 1);

        if ($getEmployees) {
            $getScenarioEmployees = $getScenarioEmployees->with(['user' => function ($q) {
                $q->select('id', 'empID', 'companyScenarionID', 'isActive', 'applicableCategoryID')
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

    public function sendEmail()
    { 
        $this->insertToLogTb(['Document Code' => $this->documentCode, 'Message' => 'Email Function Triggered']);
        $msg = '';
        $logType = 'info';

        foreach ($this->notifyList as $val) {

            $mailTo = '';
            $name = '';
            $applicableCatDesc = '';
            
            $applicableCatDesc = 'Employee';
            $mailTo = $val['employee']['empEmail'];
            $name = $val['employee']['empFullName'];
            

            $mailBody = "Dear {$name},<br/>";
            $mailBody .= $this->email_body();
    
            $subject = 'Employee profile creation notification';
    
            $emails = [
                'companySystemID' => $this->companyId,
                'alertMessage' => $subject,
                'empEmail' => $mailTo,
                'emailAlertMessage' => $mailBody
            ];
            $sendEmail = \Email::sendEmailErp($emails);

            if (!$sendEmail["success"]) {
                $msg = "Employee profile creation notification not sent for {$applicableCatDesc} {$name} "; 
                $logType = 'error';
                $this->insertToLogTb(['Document Code' => $this->documentCode, 'Message' => $msg], $logType); 
            } else {
                $msg = "Employee profile creation notification sent for {$applicableCatDesc} {$name} ";
                $this->insertToLogTb(['Document Code' => $this->documentCode, 'Message' => $msg]); 
            }
        }
    }

    public function email_body()
    {
        $str = "<br/>";
        $str = "<br/>A new Employee profile has been created for your further action as the following information:";

        if($this->dataType == 'single')
        {
            $str .= ".<br/><b> Date </b> : " . $this->masterDet['currentDate'];
            $str .= ".<br/><b> Employee </b> : " . $this->masterDet['Ename2'];
            $str .= ".<br/><b> Employment Type </b> : " . $this->masterDet['Description'];
            $str .= ".<br/><b> Gender </b> : " . $this->masterDet['name'];
            $str .= ".<br/><b> Country </b> : " . $this->masterDet['countryName'];
            $str .= ".<br/><b> Grade </b> : " . $this->masterDet['gradeDescription'];
            $str .="<br><br>Thank You";
            return $str;
        }
        else
        {
            $str .= "<br/><b> Date </b>: " . date('Y-m-d');
            $str .= "<br>";
            $str .= "<table border='1' style='width:100%'>";
            $str .= "<tr>";
            $str .= "<th><b>#</b></th>";
            $str .= "<th><b>Employee</b></th>";
            $str .= "<th><b>Employment Type</b></th>";
            $str .= "<th><b>Gender</b></th>";
            $str .= "<th><b>Country</b></th>";
            $str .= "<th><b>Grade</b></th>";
            $str .= "</tr>";
            $i = 1;
            foreach ($this->masterDet as $key => $value) {
                $str .= "<tr>";
                $str .= "<td>" . $i . "</td>";
                $str .= "<td>" . $value['Ename2'] . "</td>";
                $str .= "<td>" . $value['Description'] . "</td>";
                $str .= "<td>" . $value['name'] . "</td>";
                $str .= "<td>" . $value['countryName'] . "</td>";
                $str .= "<td>" . $value['gradeDescription'] . "</td>";
                $str .= "</tr>";
                $i++;
            }

            $str .= "</table><br><br>";
            $str .= "Thank You";
            return $str;

            
        }
    }

    public function insertToLogTb($logData, $logType = 'info')
    {
        $logData = json_encode($logData);
        $data = [
            'company_id' => $this->companyId,
            'module' => 'HRMS',
            'description' => 'Employee profile creation notification scenario',
            'scenario_id' => 44,
            'processed_for' => $this->date,
            'logged_at' => $this->date,
            'log_type' => $logType,
            'log_data' => $logData,
        ];
        DB::table('job_logs')->insert($data);
    }
}

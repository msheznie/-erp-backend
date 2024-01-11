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


    public function __construct($companyId, $id, $masterDet)
    {
        $this->companyId = $companyId;
        $this->id = $id;
        $this->date = Carbon::now()->format('Y-m-d H:i:s');
        $this->isScenarioActive = false;
        $this->notifyList = [];
        $this->masterDet = $masterDet;
        $this->documentCode = '';
    }

    function execute()
    { 
        if (empty($this->masterDet)) {
            $this->insertToLogTb(['Document Code' => '', 'Message' => 'Master details not found', 'error']);
            return false;
        }

        $columnValues = array_column($this->masterDet, 'ECode');
        $this->documentCode = implode(',', $columnValues);

        $this->insertToLogTb(['Document Code' => $this->documentCode, 'Message' => 'Master details not found', 'error']);

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
        $mailTo = '';
        $name = '';
        $applicableCatDesc = '';

        foreach ($this->notifyList as $val) {

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
        $this->insertToLogTb(['Count' => count($this->masterDet), 'Message' => '']);
        $str = "<br/>";
        $str = "<br/>A new Employee profile has been created for your further action as the following information:";
   if(count($this->masterDet) == 1)
        {
            foreach ($this->masterDet as $key => $value) {
                $str .= ".<br/><b> Date </b> : " . $value['currentDate'];
                $str .= ".<br/><b> Employee </b> : " . $value['Ename2'];
                $str .= ".<br/><b> Employment Type </b> : " . $value['Description'];
                $str .= ".<br/><b> Gender </b> : " . $value['name'];
                $str .= ".<br/><b> Country </b> : " . $value['countryName'];
                $str .= ".<br/><b> Grade </b> : " . $value['gradeDescription'];
                $str .="<br><br>Thank You";
            }
                return $str;
            
        }
        else
        {
            $str .= "<br/><b> Date </b>: " . date('Y-m-d');
            $str .= "<br>";
            $str .= "<table style='border-collapse: collapse;border: 1px solid black;' style='width:100%'>";
            $str .= "<tr style='border: 1px solid black;padding: 8px;'>";
            $str .= "<th style='border: 1px solid black;padding: 8px;'><b>#</b></th>";
            $str .= "<th style='border: 1px solid black;padding: 8px;'><b>Employee</b></th>";
            $str .= "<th style='border: 1px solid black;padding: 8px;'><b>Employment Type</b></th>";
            $str .= "<th style='border: 1px solid black;padding: 8px;'><b>Gender</b></th>";
            $str .= "<th style='border: 1px solid black;padding: 8px;'><b>Country</b></th>";
            $str .= "<th style='border: 1px solid black;padding: 8px;'><b>Grade</b></th>";
            $str .= "</tr>";
            $i = 1;
            foreach ($this->masterDet as $key => $value) {
                $str .= "<tr style='border: 1px solid black;padding: 8px;'>";
                $str .= "<td style='border: 1px solid black;padding: 8px;'>" . $i . "</td>";
                $str .= "<td style='border: 1px solid black;padding: 8px;'>" . $value['Ename2'] . "</td>";
                $str .= "<td style='border: 1px solid black;padding: 8px;'>" . $value['Description'] . "</td>";
                $str .= "<td style='border: 1px solid black;padding: 8px;'>" . $value['name'] . "</td>";
                $str .= "<td style='border: 1px solid black;padding: 8px;'>" . $value['countryName'] . "</td>";
                $str .= "<td style='border: 1px solid black;padding: 8px;'>" . $value['gradeDescription'] . "</td>";
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

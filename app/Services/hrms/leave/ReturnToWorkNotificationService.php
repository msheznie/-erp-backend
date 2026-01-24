<?php

namespace App\Services\hrms\leave;

use Carbon\Carbon;
use App\Models\HrmsEmployeeManager;
use App\Models\NotificationCompanyScenario;
use Illuminate\Support\Facades\DB;

use App\helper\email as Email;
class ReturnToWorkNotificationService
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

        $this->documentCode = $this->masterDet['documentCode'];

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
            ->where('scenarioID', 40)
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
            if ($val['applicableCategoryID'] == 7) { //Reporting manager
                $applicableCatDesc = 'Reporting manager';
                $manageInfo = $this->getReportingManagerInfo();


                if (empty($manageInfo)) {
                    $msg = 'Manager details not found for return to work notification';
                    $this->insertToLogTb(['Document' =>  $this->documentCode, 'Message' => $msg], 'error');
                }
                $mailTo = $manageInfo['EEmail'];
                $name = $manageInfo['Ename2'];
                
            } else { // Employee
                $applicableCatDesc = 'Employee';
                $mailTo = $val['employee']['empEmail'];
                $name = $val['employee']['empFullName'];
            }

            $mailBody = "Dear {$name},<br/>";
            $mailBody .= $this->email_body();
    
            $subject = 'Employee return to work notification';
    
            $emails = [
                'companySystemID' => $this->companyId,
                'alertMessage' => $subject,
                'empEmail' => $mailTo,
                'emailAlertMessage' => $mailBody
            ];
            $sendEmail = Email::sendEmailErp($emails);

            if (!$sendEmail["success"]) {
                $msg = "Return to work notification not sent for {$applicableCatDesc} {$name} "; 
                $logType = 'error';
                $this->insertToLogTb(['Document Code' => $this->documentCode, 'Message' => $msg], $logType); 
            } else {
                $msg = "Return to work notification sent for {$applicableCatDesc} {$name} ";
                $this->insertToLogTb(['Document Code' => $this->documentCode, 'Message' => $msg]); 
            }
        }
    }

    public function getReportingManagerInfo()
    {
        $manager = HrmsEmployeeManager::selectRaw('empID,managerID')
            ->where('active', 1)
            ->where('empID', $this->masterDet['empID'])
            ->whereHas('info')
            ->with('info:EIdNo,Ename2,EEmail')
            ->first(); 
        return $manager['info'];        
    }

    public function email_body()
    {

        $str = "<br/>";
        $str = "<br/>The following employee has returned to work:";
        $str .= ".<br/><b> Employee Name </b> : " . $this->masterDet['empName'];
        $str .= ".<br/><b> Leave Type </b> : " . $this->masterDet['leaveTypeDesc'];
        $str .= ".<br/><b> Leave From </b> : " . $this->masterDet['startDate'];
        $str .= ".<br/><b> Leave To </b> : " . $this->masterDet['endDate'];
        $str .= ".<br/><b> Return Back to work date </b> : " . $this->masterDet['returnToWorkDate'];
        $str .= ".<br/><b> Comment </b> : " . $this->masterDet['comments'];

        return $str;
    }

    public function insertToLogTb($logData, $logType = 'info')
    {
        $logData = json_encode($logData);
        $data = [
            'company_id' => $this->companyId,
            'module' => 'HRMS',
            'description' => 'Return to work notification scenario',
            'scenario_id' => 40,
            'processed_for' => $this->date,
            'logged_at' => $this->date,
            'log_type' => $logType,
            'log_data' => $logData,
        ];
        DB::table('job_logs')->insert($data);
    }
}

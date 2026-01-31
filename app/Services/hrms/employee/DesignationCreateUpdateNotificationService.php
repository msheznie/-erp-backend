<?php

namespace App\Services\hrms\employee;

use App\Models\Employee;
use Carbon\Carbon;
use App\Models\HrmsEmployeeManager;
use App\Models\NotificationCompanyScenario;
use Illuminate\Support\Facades\DB;

class DesignationCreateUpdateNotificationService
{
    private $companyId;
    private $id;
    private $date;
    private $isScenarioActive;
    private $notifyList;
    private $masterDet;

    public function __construct($companyId, $id, $masterDet)
    {
        $this->companyId = $companyId;
        $this->id = $id;
        $this->date = Carbon::now()->format('Y-m-d H:i:s');
        $this->isScenarioActive = false;
        $this->notifyList = [];
        $this->masterDet = $masterDet;
    }

    function execute()
    {
        if (empty($this->masterDet)) {
            $this->insertToLogTb(['Designation ID' => $this->id, 'Message' => 'Master details not found'], 'error');
            return false;
        }

        $this->validateNotificationScenarioActive();
        if (!$this->isScenarioActive) {
            return false;
        }

        $this->validateNotifyEmployeeExists();
        if (empty($this->notifyList)) {
            return false;
        }

        $this->sendEmail();
        $this->insertToLogTb(['Designation ID' => $this->getDesignationIdList(),
            'Message' => 'execution successfully completed']);
    }

    public function validateNotificationScenarioActive()
    {
        $notificationCompanyScenario = $this->getScenarioEmployees();
        $this->isScenarioActive = (!empty($notificationCompanyScenario)) ? true : false;
        if (!$this->isScenarioActive) {
            $this->insertToLogTb(['Designation ID' => $this->getDesignationIdList(),
                'Message' => 'Notification scenario does not exist or is not active'], 'error');
        }
    }

    public function validateNotifyEmployeeExists()
    {
        $getNotifyEmployees = $this->getScenarioEmployees(true);
        $this->notifyList = (!empty($getNotifyEmployees) ? $getNotifyEmployees['user'] : []);
        if (empty($this->notifyList)) {
            $this->insertToLogTb(['Designation ID' => $this->getDesignationIdList(),
                'Message' => 'Notification scenario employees do not exists'], 'error');
        }
    }

    public function getScenarioEmployees($getEmployees = false)
    {
        $getScenarioEmployees =  NotificationCompanyScenario::select('id')
            ->where('scenarioID', 54)
            ->where('companyID', $this->companyId)
            ->where('isActive', 1);

        if ($getEmployees) {
            $getScenarioEmployees = $getScenarioEmployees->with(['user' => function ($q) {
                $q->select('id', 'empID', 'companyScenarionID', 'isActive', 'applicableCategoryID')
                    ->where('isActive', '=', 1)
                    ->with(['employee' => function ($q3) {
                        $q3->select('employeeSystemID', 'empFullName', 'empEmail', 'empID', 'isEmailVerified');
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
        $this->insertToLogTb(['Designation ID' => $this->getDesignationIdList(),
            'Message' => 'Email Function Triggered']);
        $msg = '';
        $logType = 'info';
        $inValidEmails = [];

        foreach ($this->notifyList as $val) {

            $mailTo = '';
            $name = '';
            $applicableCatDesc = '';
            $isEmailVerified = '';
            $empCode = '';
         
            $applicableCatDesc = 'Employee';
            $mailTo = $val['employee']['empEmail'];
            $name = $val['employee']['empFullName'];
            $isEmailVerified = $val['employee']['isEmailVerified'];
            $empCode = $val['employee']['empID'];
            

            if ((!filter_var($mailTo, FILTER_VALIDATE_EMAIL)) || ($isEmailVerified == 0)) {
                $inValidEmails[] = $empCode;
            } else {
                $mailBody = "Dear {$name},<br/><br/>";
                $mailBody .= $this->emailBody();

                $createdUserName  = $this->masterDet[0]['CreatedUserName'] ?? '';
                $modifiedUserName = $this->masterDet[0]['ModifiedUserName'] ?? '';
                $isUpdate = (!empty($createdUserName) && !empty($modifiedUserName));
                $subject = $isUpdate ? 'Designation Updated in Designation Master' : 'Designation(s) Created in Designation Master';

                $emails = [
                    'companySystemID' => $this->companyId,
                    'alertMessage' => $subject,
                    'empEmail' => $mailTo,
                    'emailAlertMessage' => $mailBody
                ];
                $sendEmail = \Email::sendEmailErp($emails);

                if (!$sendEmail["success"]) {
                    $msg = "Designation create and update notification not sent for {$applicableCatDesc} {$name} ";
                    $logType = 'error';
                    $this->insertToLogTb(['Designation ID' => $this->getDesignationIdList(),
                        'Message' => $msg], $logType);
                } else {
                    $msg = "Designation create and update notification sent for {$applicableCatDesc} {$name} ";
                    $this->insertToLogTb(['Designation ID' => $this->getDesignationIdList(),
                        'Message' => $msg]);
                }
            }
        }

        if (!empty($inValidEmails)) {
            $this->insertToLogTb(
                [
                    'message' => 'Employees who have invalid/unverified email address',
                    'Employee Code' => $inValidEmails
                ],
                'data'
            );
        }
    }

    public function getReportingManagerInfo()
    {
        $manager = HrmsEmployeeManager::selectRaw('empID,managerID')
            ->where('active', 1)
            ->where('empID', $this->masterDet[0]['empId'])
            ->whereHas('info')
            ->with('info:EIdNo,Ename2,EEmail,ECode')
            ->first();
        return $manager['info'];
    }

    public function checkIsEmailVerified($empId)
    {
        return Employee::select('isEmailVerified')
                ->where('empID', $empId)
                ->first();
    }

    public function emailBody()
    {
        $createdUserName  = $this->masterDet[0]['CreatedUserName'] ?? '';
        $modifiedUserName = $this->masterDet[0]['ModifiedUserName'] ?? '';
        $createdDate = $this->masterDet[0]['CreatedDate'] ?? '';
        $isUpdate = (!empty($createdUserName) && !empty($modifiedUserName));
        $bodyLine = "";
        $newDesignation = $this->masterDet[0]['designation'] ?? '';

        if ($isUpdate) {
            $designationDesc = $this->masterDet[0]['oldDesc'] ?? '';
            $bodyLine = "The designation has been updated from  {$designationDesc} to {$newDesignation}.";
            $byLine = "Updated By: {$modifiedUserName}";
        } else {
            $designations = [];
            
            if (is_array($this->masterDet)) {
                foreach ($this->masterDet as $key => $value) {
                    if (is_numeric($key) && is_array($value) && isset($value['designation'])) {
                        $designations[] = $value['designation'];
                    }
                }
            }
            
            if (count($designations) > 1) {
                $designationsList = implode(', ', $designations);
                $bodyLine = "The following new designations have been created:<br>{$designationsList}";
            } else {
                $designationDesc = $this->masterDet[0]['designation'] ?? '';
                $bodyLine = "A new designation <b>{$designationDesc}</b> has been created.";
            }
            $byLine = "Created By: {$createdUserName}<br>Created On: {$createdDate}";
        }

        return "{$bodyLine}<br><br>{$byLine}";
    }

    private function getDesignationIdList()
    {
        $designationIds = array_column($this->masterDet, 'designationId');
        $designationIdList = implode(',', $designationIds);
        return $designationIdList;
    }

    public function insertToLogTb($logData, $logType = 'info')
    {
        $logData = json_encode($logData);
        $data = [
            'company_id' => $this->companyId,
            'module' => 'HRMS',
            'description' => 'Designation create and update notification scenario',
            'scenario_id' => 54,
            'processed_for' => $this->date,
            'logged_at' => $this->date,
            'log_type' => $logType,
            'log_data' => $logData,
        ];
        DB::table('job_logs')->insert($data);
    }
}

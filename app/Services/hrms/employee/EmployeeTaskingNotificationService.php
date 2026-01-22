<?php

namespace App\Services\hrms\employee;

use App\Models\Employee;
use Carbon\Carbon;
use App\Models\HrmsEmployeeManager;
use App\Models\NotificationCompanyScenario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmployeeTaskingNotificationService
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
            $this->insertToLogTb(['Employee Id' => '', 'Message' => 'Master details not found'], 'error');
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
        $this->insertToLogTb(['Employee Id' => $this->masterDet['empId'],
            'Message' => 'execution successfully completed']);
    }

    public function validateNotificationScenarioActive()
    {
        $notificationCompanyScenario = $this->getScenarioEmployees();
        $this->isScenarioActive = (!empty($notificationCompanyScenario)) ? true : false;
        if (!$this->isScenarioActive) {
            $this->insertToLogTb(['Employee Id' => $this->masterDet['empId'],
                'Message' => 'Notification scenario Does not exist or does not active'], 'error');
        }
    }

    public function validateNotifyEmployeeExists()
    {
        $getNotifyEmployees = $this->getScenarioEmployees(true);
        $this->notifyList = (!empty($getNotifyEmployees) ? $getNotifyEmployees['user'] : []);
        if (empty($this->notifyList)) {
            $this->insertToLogTb(['Employee Id' => $this->masterDet['empId'],
                'Message' => 'Notification scenario employees do not exists'], 'error');
        }
    }

    public function getScenarioEmployees($getEmployees = false)
    {
        $getScenarioEmployees =  NotificationCompanyScenario::select('id')
            ->where([
                'scenarioID' => 52,
                'companyID' => $this->companyId,
                'isActive' => 1
            ]);

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
        $this->insertToLogTb(['Employee Id' => $this->masterDet['empId'],
            'Message' => 'Email Function Triggered']);
        $msg = '';
        $logType = 'info';
        $inValidEmails = [];

        Log::info($this->masterDet);
        
        foreach ($this->notifyList as $val) {

            $mailTo = '';
            $name = '';
            $applicableCatDesc = '';
            $isEmailVerified = '';
            $empCode = '';
            if ($val['applicableCategoryID'] == 7) { //Requesting employees(hr_tasking_request_master.emp_id) reporting manager
                $applicableCatDesc = 'Reporting manager';
                $manageInfo = $this->getReportingManagerInfo();
                if (empty($manageInfo)) {
                    $msg = 'Manager details not found for employee tasking notification';
                    $this->insertToLogTb(['Employee' =>  $manageInfo['ECode'],
                        'Message' => $msg], 'error');
                }
                else
                {
                    $empCode = $manageInfo['ECode'];
                    $isEmailVerified = $this->checkIsEmailVerified($manageInfo['empID']);

                    $mailTo = $manageInfo['EEmail'];
                    $name = $manageInfo['Ename2'];
                }

            } else if ($val['applicableCategoryID'] == 9) {  //Employee request tasking 'hr_tasking_request_master.emp_id
                $applicableCatDesc = 'Applicable Employee';
                $mailTo = $this->masterDet['empEmail'];
                $name = $this->masterDet['empName'];
                $isEmailVerified = $this->checkIsEmailVerified($this->masterDet['empId']);
                $empCode = $this->masterDet['empCode'];

            } else { // Employee
                $applicableCatDesc = 'Employee';
                $mailTo = $val['employee']['empEmail'];
                $name = $val['employee']['empFullName'];
                $isEmailVerified = $val['employee']['isEmailVerified'];
                $empCode = $val['employee']['empID'];
            }

            if ((!filter_var($mailTo, FILTER_VALIDATE_EMAIL)) || ($isEmailVerified == 0)) {
                $inValidEmails[] = $empCode;
            } else {
                $mailBody = "Dear {$name},<br/><br/>";
                $mailBody .= $this->emailBody();

                $subject = 'Employee Tasking Created';

                $emails = [
                    'companySystemID' => $this->companyId,
                    'alertMessage' => $subject,
                    'empEmail' => $mailTo,
                    'emailAlertMessage' => $mailBody
                ];
                $sendEmail = \Email::sendEmailErp($emails);

                if (!$sendEmail["success"]) {
                    $msg = "Employee tasking notification not sent for {$applicableCatDesc} {$name} ";
                    $logType = 'error';
                    $this->insertToLogTb(['Employee Id' => $this->masterDet['empId'],
                        'Message' => $msg], $logType);
                } else {
                    $msg = "Employee tasking notification sent for {$applicableCatDesc} {$name} ";
                    $this->insertToLogTb(['Employee Id' => $this->masterDet['empId'],
                        'Message' => $msg]);
                }
            }
        }

        if (!empty($inValidEmails)) {
            $this->insertToLogTb(
                [
                    'message' => 'Employees who have invalid/unverified email address',
                    'Employee Id' => $inValidEmails
                ],
                'data'
            );
        }
    }

    public function getReportingManagerInfo()
    {
        $manager = HrmsEmployeeManager::selectRaw('empID,managerID')
            ->where([
                'active' => 1,
                'empID' => $this->masterDet['empId']
            ])
            ->whereHas('info')
            ->with('info:EIdNo,Ename2,EEmail,ECode')
            ->first();
        return !empty($manager) ? $manager['info'] : [];
    }

    public function checkIsEmailVerified($empId)
    {
        return Employee::where('empID', $empId)
                ->value('isEmailVerified');
    }

    public function emailBody()
    {
        $empName = $this->masterDet['empName'] ?? '';
        $assigneeName = $this->masterDet['assignedEmpName'] ?? '';
        $assigneeDepartment = $this->masterDet['DepartmentDes'] ?? '';
        $startDate = $this->masterDet['fromDate'] ?? '';
        $endDate = $this->masterDet['toDate'] ?? '';
        $assignee = $assigneeName . ' / ' . $assigneeDepartment;
        $period = $startDate . ' - ' . $endDate;
        
        return "Tasking has been created for <b>{$empName}</b> and assigned to <b>{$assignee}</b> for the period <b>{$period}</b>.";
    }

    public function insertToLogTb($logData, $logType = 'info')
    {
        $logData = json_encode($logData);
        $data = [
            'company_id' => $this->companyId,
            'module' => 'HRMS',
            'description' => 'Employee tasking notification scenario',
            'scenario_id' => 52,
            'processed_for' => $this->date,
            'logged_at' => $this->date,
            'log_type' => $logType,
            'log_data' => $logData,
        ];
        DB::table('job_logs')->insert($data);
    }
}


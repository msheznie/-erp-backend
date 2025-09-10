<?php

namespace App\Services\hrms\employee;

use App\Models\Employee;
use Carbon\Carbon;
use App\Models\HrmsEmployeeManager;
use App\Models\NotificationCompanyScenario;
use Illuminate\Support\Facades\DB;

class EmpDesignationUpdateNotificationService
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
            $this->insertToLogTb(['Employee Code' => '', 'Message' => 'Master details not found', 'error']);
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
        $this->insertToLogTb(['Employee Code' => $this->masterDet['employeeCode'],
            'Message' => 'execution successfully completed']);
    }

    public function validateNotificationScenarioActive()
    {
        $notificationCompanyScenario = $this->getScenarioEmployees();
        $this->isScenarioActive = (!empty($notificationCompanyScenario)) ? true : false;
        if (!$this->isScenarioActive) {
            $this->insertToLogTb(['Employee Code' => $this->masterDet['employeeCode'],
                'Message' => 'Notification scenario Does not exist or 
            does not active'], 'error');
        }
    }

    public function validateNotifyEmployeeExists()
    {
        $getNotifyEmployees = $this->getScenarioEmployees(true);
        $this->notifyList = (!empty($getNotifyEmployees) ? $getNotifyEmployees['user'] : []);
        if (empty($this->notifyList)) {
            $this->insertToLogTb(['Employee Code' => $this->masterDet['employeeCode'],
                'Message' => 'Employees Does not exists'], 'error');
        }
    }

    public function getScenarioEmployees($getEmployees = false)
    {
        $getScenarioEmployees =  NotificationCompanyScenario::select('id')
            ->where('scenarioID', 51)
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
        $this->insertToLogTb(['Employee Code' => $this->masterDet['employeeCode'],
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
            if ($val['applicableCategoryID'] == 7) { //Reporting manager
                $applicableCatDesc = 'Reporting manager';
                $manageInfo = $this->getReportingManagerInfo();
                $empCode = $manageInfo['ECode'];
                $isEmailVerified = $this->checkIsEmailVerified($manageInfo['empID']);

                if (empty($manageInfo)) {
                    $msg = 'Manager details not found for designation update notification';
                    $this->insertToLogTb(['Employee' =>  $this->masterDet['employeeCode'],
                        'Message' => $msg], 'error');
                }
                $mailTo = $manageInfo['EEmail'];
                $name = $manageInfo['Ename2'];

            } else if ($val['applicableCategoryID'] == 9) {  // Applicable Employee
                $applicableCatDesc = 'Applicable Employee';
                $mailTo = $this->masterDet['employeeMail'];
                $name = $this->masterDet['employeeName'];
                $isEmailVerified = $this->masterDet['isEmailVerified'];
                $empCode = $this->masterDet['employeeCode'];

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

                $subject = 'Employee designation update notification';

                $emails = [
                    'companySystemID' => $this->companyId,
                    'alertMessage' => $subject,
                    'empEmail' => $mailTo,
                    'emailAlertMessage' => $mailBody
                ];
                $sendEmail = \Email::sendEmailErp($emails);

                if (!$sendEmail["success"]) {
                    $msg = "Employee designation update notification not sent for {$applicableCatDesc} {$name} ";
                    $logType = 'error';
                    $this->insertToLogTb(['Employee Code' => $this->masterDet['employeeCode'],
                        'Message' => $msg], $logType);
                } else {
                    $msg = "Employee designation update notification sent for {$applicableCatDesc} {$name} ";
                    $this->insertToLogTb(['Employee Code' => $this->masterDet['employeeCode'],
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
            ->where('empID', $this->masterDet['empId'])
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
        $empName   = $this->masterDet['employeeName'] ?? '';
        $empCode   = $this->masterDet['employeeCode'] ?? '';
        $oldDes    = $this->masterDet['oldDesignation'] ?? '';
        $newDes    = $this->masterDet['newDesignation'] ?? '';
        $effDate   = $this->masterDet['effectiveDate'] ?? '';
        $updatedBy = $this->masterDet['updatedByUser'] ?? '';

        return "<b>Designation Update:</b> Employee {$empName} ({$empCode}) has been updated "
            . "from '{$oldDes}' to '{$newDes}' effective {$effDate}.<br>"
            . "Updated by: {$updatedBy}";
    }

    public function insertToLogTb($logData, $logType = 'info')
    {
        $logData = json_encode($logData);
        $data = [
            'company_id' => $this->companyId,
            'module' => 'HRMS',
            'description' => 'Employee designation update notification scenario',
            'scenario_id' => 51,
            'processed_for' => $this->date,
            'logged_at' => $this->date,
            'log_type' => $logType,
            'log_data' => $logData,
        ];
        DB::table('job_logs')->insert($data);
    }
}

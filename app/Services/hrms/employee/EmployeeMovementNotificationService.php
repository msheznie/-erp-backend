<?php

namespace App\Services\hrms\employee;

use App\Models\Employee;
use Carbon\Carbon;
use App\enums\hrms\JobProcedureType;
use App\enums\hrms\NotificationScenario;
use App\Models\HrmsEmployeeManager;
use App\Models\NotificationCompanyScenario;
use App\Models\SrpEmployeeDetails;
use Illuminate\Support\Facades\DB;
use App\helper\email as Email;
use Illuminate\Support\Facades\Log;

class EmployeeMovementNotificationService
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
        $this->insertToLogTb([
            'Employee Id' => $this->masterDet['empId'] ?? '',
            'Message' => 'execution successfully completed'
        ]);
    }

    public function validateNotificationScenarioActive()
    {
        $notificationCompanyScenario = $this->getScenarioEmployees();
        $this->isScenarioActive = (!empty($notificationCompanyScenario)) ? true : false;
        if (!$this->isScenarioActive) {
            $this->insertToLogTb(['Employee Id' => $this->masterDet['empId'] ?? '',
                'Message' => 'Notification scenario Does not exist or does not active'], 'error');
        }
    }

    public function validateNotifyEmployeeExists()
    {
        $getNotifyEmployees = $this->getScenarioEmployees(true);
        $this->notifyList = (!empty($getNotifyEmployees) ? $getNotifyEmployees['user'] : []);
        if (empty($this->notifyList)) {
            $this->insertToLogTb(['Employee Id' => $this->masterDet['empId'] ?? '',
                'Message' => 'Notification scenario employees do not exists'], 'error');
        }
    }

    public function getScenarioEmployees($getEmployees = false)
    {
        $jobProcedureType = $this->getJobProcedureType($this->masterDet['typeId'],$this->masterDet['movementTypeId']);
        $getScenarioEmployees = NotificationCompanyScenario::select('id')
            ->where([
                'scenarioID' => NotificationScenario::EMPLOYEE_MOVEMENT,
                'companyID' => $this->companyId,
                'isActive' => 1
            ]);

        if ($getEmployees) {
            $getScenarioEmployees = $getScenarioEmployees->with(['user' => function ($q) use ($jobProcedureType) {
                $q->select('id', 'empID', 'companyScenarionID', 'isActive', 'applicableCategoryID')
                    ->where('isActive', '=', 1)
                    ->where('emp_movement_type', $jobProcedureType)
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
        $this->insertToLogTb(['Employee Id' => $this->masterDet['empId'] ?? '',
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
            $isEmailSentAssignedReportingManager = false;
            if ($val['applicableCategoryID'] == 7) { // Reporting manager
                $applicableCatDesc = 'Reporting manager';
                $manageInfo = $this->getReportingManagerInfo();
                if (empty($manageInfo)) {
                    $msg = 'Manager details not found for employee movement notification';
                    $this->insertToLogTb(['Employee' => $this->masterDet['empId'] ?? '',
                        'Message' => $msg], 'error');
                } else {
                    $empCode = $manageInfo['ECode'];
                    $isEmailVerified = $this->checkIsEmailVerified($manageInfo['EIdNo']);
                    $mailTo = $manageInfo['EEmail'];
                    $name = $manageInfo['Ename2'];
                }
            } else if ($val['applicableCategoryID'] == 9) { // Applicable Employee (employee who moved)
                $applicableCatDesc = 'Applicable Employee';
                $mailTo = $this->masterDet['empEmail'] ?? '';
                $name = $this->masterDet['empName'] ?? '';
                $isEmailVerified = $this->checkIsEmailVerified($this->masterDet['empId'] ?? null);
                $empCode = $this->masterDet['empCode'] ?? '';
            } else if($this->masterDet['type'] == 'Internal' && !$isEmailSentAssignedReportingManager) {
                $isEmailSentAssignedReportingManager = true;
                $assignedManagerInfo = $this->getActiveReportingManagerInfo();
                if (!empty($assignedManagerInfo)) {
                    $applicableCatDesc = 'Existing Reporting Manager';
                    $mailTo = $assignedManagerInfo['EEmail'];
                    $name = $assignedManagerInfo['Ename2'];
                    $empCode = $assignedManagerInfo['ECode'];
                    $isEmailVerified = $this->checkIsEmailVerified($assignedManagerInfo['EIdNo']);
                }
            } else { // Employee
                $applicableCatDesc = 'Employee';
                $mailTo = $val['employee']['empEmail'];
                $name = $val['employee']['empFullName'];
                $isEmailVerified = $val['employee']['isEmailVerified'];
                $empCode = $val['employee']['empID'];
            }

            
            if (!filter_var($mailTo, FILTER_VALIDATE_EMAIL)) {
                $inValidEmails[] = $empCode . ' - ' . $mailTo . ' (Invalid email format)';
            } elseif ($isEmailVerified === null) {
                $this->insertToLogTb([
                    'Employee Id' => $this->masterDet['empId'] ?? '',
                    'Message' => "Email verification status unknown (null) for {$applicableCatDesc} {$name} ({$mailTo}) - Employee record may not exist"
                ], 'warning');
            } elseif ($isEmailVerified == 0) {
                $inValidEmails[] = $empCode . ' - ' . $mailTo . ' (Email not verified)';
            } else {
                $mailBody = "Dear {$name},<br/><br/>";
                $mailBody .= $this->emailBody();

                $subject = $this->emailSubject();

                $emails = [
                    'companySystemID' => $this->companyId,
                    'alertMessage' => $subject,
                    'empEmail' => $mailTo,
                    'emailAlertMessage' => $mailBody
                ];
                $sendEmail = Email::sendEmailErp($emails);

                if (!$sendEmail["success"]) {
                    $msg = "Employee movement notification not sent for {$applicableCatDesc} {$name} ";
                    $logType = 'error';
                    $this->insertToLogTb(['Employee Id' => $this->masterDet['empId'] ?? '',
                        'Message' => $msg], $logType);
                } else {
                    $msg = "Employee movement notification sent for {$applicableCatDesc} {$name} ";
                    $this->insertToLogTb(['Employee Id' => $this->masterDet['empId'] ?? '',
                        'Message' => $msg]);
                }
            }
        }

        if (!empty($inValidEmails)) {
            $this->insertToLogTb(
                [
                    'message' => 'Employees who have invalid/unverified email address',
                    'Employee code' => $inValidEmails
                ],
                'data'
            );
        }
    }

    public function getReportingManagerInfo()
    {
        $empId = $this->masterDet['empId'] ?? null;
        if (empty($empId)) {
            return [];
        }
        $manager = HrmsEmployeeManager::selectRaw('empID,managerID')
            ->where([
                'active' => 1,
                'empID' => $empId
            ])
            ->whereHas('info')
            ->with('info:EIdNo,Ename2,EEmail,ECode')
            ->first();
        return !empty($manager) ? $manager['info'] : [];
    }

    public function getActiveReportingManagerInfo()
    {
        $empId = $this->masterDet['assignedReportingManager'] ?? null;
        if (empty($empId)) {
            return [];
        }   
        $manager = SrpEmployeeDetails::select('EIdNo', 'Ename2', 'EEmail', 'ECode')
            ->where('EIdNo', $empId)
            ->first();
        return !empty($manager) ? $manager->toArray() : [];
    }

    public function checkIsEmailVerified($empId)
    {
        if (empty($empId)) {
            return null;
        }
        $employee = Employee::where('employeeSystemID', $empId)->first();
        return $employee ? $employee->isEmailVerified : null;
    }

    public function emailSubject()
    {
        $movementType = $this->masterDet['movementType'] ?? '';
        $classification = $this->masterDet['type'] ?? '';
        $empName = $this->masterDet['empName'] ?? '';

        return "Employee Movement Approved - {$movementType} ({$classification}) - {$empName}";
    }

    public function emailBody()
    {
        $empName = $this->masterDet['empName'] ?? '';
        $empCode = $this->masterDet['empCode'] ?? '';
        $movementType = $this->masterDet['movementType'] ?? '';
        $classification = $this->masterDet['type'] ?? '';
        $fromDate = $this->masterDet['fromDate'] ?? '';
        $toDate = $this->masterDet['toDate'] ?? '';
        $currentDepartmentDes = $this->masterDet['currentDepartmentDes'] ?? '';
        $assignedDepartmentDes = $this->masterDet['assignedDepartmentDes'] ?? '';
        $entityName = $this->masterDet['entityName'] ?? '';

        $isTransfer = (stripos((string) $movementType, 'Transfer') !== false);

        $body = 'This is to inform you that the following Employee Movement Request has been successfully approved in the system.<br/><br/>';
        $body .= '<b>Employee Movement Details:</b><br/>';
        $body .= 'Employee Name: ' . $empName . '<br/>';
        $body .= 'Employee ID: ' . $empCode . '<br/>';
        $body .= 'Movement Type: ' . $movementType . '<br/>';
        $body .= 'Classification: ' . $classification . '<br/>';

        if ($fromDate) {
            $body .= 'Start date: ' . $fromDate . '<br/>';
        }
        if (!$isTransfer && $toDate) {
            $body .= 'End date: ' . $toDate . '<br/>';
        }

        $body .= 'Current department: ' . $currentDepartmentDes . '<br/>';
        if (!$isTransfer && ($assignedDepartmentDes !== '')) {
            $body .= 'New department: ' . $assignedDepartmentDes . '<br/>';
        }

        $isExternal =  stripos((string) $classification, 'External') !== false;
        if ($isExternal && $entityName !== '') {
            $body .= 'Entity: ' . $entityName . '<br/>';
        }

        return $body;
    }

    public function insertToLogTb($logData, $logType = 'info')
    {
        $logData = json_encode($logData);
        $data = [
            'company_id' => $this->companyId,
            'module' => 'HRMS',
            'description' => 'Employee movement notification scenario',
            'scenario_id' => NotificationScenario::EMPLOYEE_MOVEMENT,
            'processed_for' => $this->date,
            'logged_at' => $this->date,
            'log_type' => $logType,
            'log_data' => $logData,
        ];
        DB::table('job_logs')->insert($data);
    }

    private function getJobProcedureType($typeId, $movementTypeId)
    {
        $jobProcedureType = null;

        if ($typeId == JobProcedureType::Internal) {
            switch ($movementTypeId) {
                case JobProcedureType::Transfer:
                    $jobProcedureType = JobProcedureType::Internal_Transfer;
                    break;
                case JobProcedureType::Secondment:
                    $jobProcedureType = JobProcedureType::Internal_Secondment;
                    break; 
                case JobProcedureType::Assignment:
                    $jobProcedureType = JobProcedureType::Internal_Assignment;
                    break;
            }
        }
        
        if ($typeId == JobProcedureType::External) {
            switch ($movementTypeId) {
                case JobProcedureType::Transfer:
                    $jobProcedureType = JobProcedureType::External_Transfer;
                    break;
                case JobProcedureType::Secondment:
                    $jobProcedureType = JobProcedureType::External_Secondment;
                    break;
                case JobProcedureType::Assignment:
                    $jobProcedureType = JobProcedureType::External_Assignment;
                    break;
            }
        }
        return $jobProcedureType;
    }
}

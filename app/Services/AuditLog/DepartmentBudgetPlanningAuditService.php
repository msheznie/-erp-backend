<?php

namespace App\Services\AuditLog;

use App\Models\CompanyFinanceYear;
use App\Models\WorkflowConfiguration;

class DepartmentBudgetPlanningAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];

        if ($auditData['crudType'] == "C") {
            // For creation, log all the new values
            if ($auditData['parentID'] == 0) {
                $modifiedData[] = ['amended_field' => "planning_code", 'previous_value' => '', 'new_value' => $auditData['newValue']['planningCode']];
                $modifiedData[] = ['amended_field' => "initiated_date", 'previous_value' => '', 'new_value' => $auditData['newValue']['initiatedDate']];
                $modifiedData[] = ['amended_field' => "submission_date", 'previous_value' => '', 'new_value' => $auditData['newValue']['submissionDate']];

                $workflow = WorkflowConfiguration::find($auditData['newValue']['workflowID']);
                $modifiedData[] = ['amended_field' => "workflow", 'previous_value' => '', 'new_value' => $workflow->workflowName];
                $modifiedData[] = ['amended_field' => "budget_type", 'previous_value' => '', 'new_value' => self::getType($auditData['newValue']['typeID'])];
                $year = CompanyFinanceYear::find($auditData['newValue']['yearID']);
                $modifiedData[] = ['amended_field' => "budget_year", 'previous_value' => '', 'new_value' => $year->bigginingDate . " | " . $year->endingDate];
                $modifiedData[] = ['amended_field' => "budget_period", 'previous_value' => '', 'new_value' => 'Yearly'];
            }
            else {
                $modifiedData[] = ['amended_field' => "request_code", 'previous_value' => '', 'new_value' => $auditData['newValue']['request_code']];
                $modifiedData[] = ['amended_field' => "current_submission_date", 'previous_value' => '', 'new_value' => $auditData['newValue']['current_submission_date']];
                $modifiedData[] = ['amended_field' => "date_of_request", 'previous_value' => '', 'new_value' => $auditData['newValue']['date_of_request']];
                $modifiedData[] = ['amended_field' => "reason_for_extension", 'previous_value' => '', 'new_value' => $auditData['newValue']['reason_for_extension']];
                $modifiedData[] = ['amended_field' => "status", 'previous_value' => '', 'new_value' => self::getTimeExtensionStatus($auditData['newValue']['status'])];
            }
        }
        else if ($auditData['crudType'] == "U") {
            // For updates, compare old and new values
            if ($auditData['parentID'] == 0) {
                if($auditData['previosValue']['workStatus'] != $auditData['newValue']['workStatus']) {
                    $modifiedData[] = ['amended_field' => "work_status", 'previous_value' => self::getWorkStatus($auditData['previosValue']['workStatus']), 'new_value' => self::getWorkStatus($auditData['newValue']['workStatus'])];
                }
            }
            else {
                if($auditData['previosValue']['status'] != $auditData['newValue']['status']) {
                    $modifiedData[] = ['amended_field' => "status", 'previous_value' => self::getTimeExtensionStatus($auditData['previosValue']['status']), 'new_value' => self::getTimeExtensionStatus($auditData['newValue']['status'])];
                }
            }

        }
        else if ($auditData['crudType'] == "D") {
            // For deletion, log all the previous values
        }

        return $modifiedData;
    }

    public static function getTimeExtensionStatus($status)
    {
        switch ($status) {
            case 1:
                return "Time Requested";
            case 2:
                return "Approved";
            case 3:
                return "Rejected";
            case 4:
                return "Cancelled";
            default:
                return "";
        }
    }

    public static function getWorkStatus($status)
    {
        switch ($status) {
            case 1 :
                return "Not Started";
            case 2 :
                return "In Progress";
            case 3 :
                return "Submitted to HOD";
            default :
                return "";
        }
    }

    public static function getType($type)
    {
        switch ($type) {
            case 1:
                return 'OPEX';
            case 2:
                return 'CAPEX';
            case 3:
                return 'Common';
            default:
                return '';
        }
    }
}

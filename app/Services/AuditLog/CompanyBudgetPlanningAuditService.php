<?php

namespace App\Services\AuditLog;

use App\Models\CompanyFinanceYear;
use App\Models\WorkflowConfiguration;

class CompanyBudgetPlanningAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];

        if ($auditData['crudType'] == 'C') {
            $nv = is_array($auditData['newValue'] ?? null) ? $auditData['newValue'] : [];
            if (empty($nv)) {
                return $modifiedData;
            }
            $modifiedData[] = ['amended_field' => 'planning_code', 'previous_value' => '', 'new_value' => (string) ($nv['planningCode'] ?? '')];
            $modifiedData[] = ['amended_field' => 'initiated_date', 'previous_value' => '', 'new_value' => (string) ($nv['initiatedDate'] ?? '')];
            $modifiedData[] = ['amended_field' => 'submission_date', 'previous_value' => '', 'new_value' => (string) ($nv['submissionDate'] ?? '')];
            $workflow = !empty($nv['workflowID']) ? WorkflowConfiguration::find($nv['workflowID']) : null;
            $modifiedData[] = ['amended_field' => 'workflow', 'previous_value' => '', 'new_value' => $workflow ? $workflow->workflowName : ''];
            $modifiedData[] = ['amended_field' => 'budget_type', 'previous_value' => '', 'new_value' => DepartmentBudgetPlanningAuditService::getType($nv['typeID'] ?? null)];
            $year = !empty($nv['yearID']) && !empty($nv['companySystemID'])
                ? CompanyFinanceYear::where('companySystemID', $nv['companySystemID'])->where('companyFinanceYearID', $nv['yearID'])->first()
                : null;
            $modifiedData[] = ['amended_field' => 'budget_year', 'previous_value' => '', 'new_value' => $year ? ($year->bigginingDate . ' | ' . $year->endingDate) : ''];
            $modifiedData[] = ['amended_field' => 'status', 'previous_value' => '', 'new_value' => (string) ($nv['status'] ?? '')];
        } elseif ($auditData['crudType'] == 'U') {
            $pv = is_array($auditData['previosValue'] ?? null) ? $auditData['previosValue'] : [];
            $nv = is_array($auditData['newValue'] ?? null) ? $auditData['newValue'] : [];
            if (($pv['confirmed_yn'] ?? null) != ($nv['confirmed_yn'] ?? null)) {
                $modifiedData[] = ['amended_field' => 'company_confirmed', 'previous_value' => DepartmentBudgetPlanningAuditService::yesNo($pv['confirmed_yn'] ?? null), 'new_value' => DepartmentBudgetPlanningAuditService::yesNo($nv['confirmed_yn'] ?? null)];
            }
            if (($pv['approved_yn'] ?? null) != ($nv['approved_yn'] ?? null)) {
                $modifiedData[] = ['amended_field' => 'approved_yn', 'previous_value' => (string) ($pv['approved_yn'] ?? ''), 'new_value' => (string) ($nv['approved_yn'] ?? '')];
            }
            if (($pv['rejected_yn'] ?? null) != ($nv['rejected_yn'] ?? null)) {
                $modifiedData[] = ['amended_field' => 'rejected_yn', 'previous_value' => (string) ($pv['rejected_yn'] ?? ''), 'new_value' => (string) ($nv['rejected_yn'] ?? '')];
            }
            if (($pv['status'] ?? null) != ($nv['status'] ?? null)) {
                $modifiedData[] = ['amended_field' => 'status', 'previous_value' => (string) ($pv['status'] ?? ''), 'new_value' => (string) ($nv['status'] ?? '')];
            }
            if (($pv['financeStatus'] ?? null) != ($nv['financeStatus'] ?? null)) {
                $modifiedData[] = ['amended_field' => 'finance_status', 'previous_value' => (string) ($pv['financeStatus'] ?? ''), 'new_value' => (string) ($nv['financeStatus'] ?? '')];
            }
            if (($pv['departmentStatus'] ?? null) != ($nv['departmentStatus'] ?? null)) {
                $modifiedData[] = ['amended_field' => 'department_status', 'previous_value' => (string) ($pv['departmentStatus'] ?? ''), 'new_value' => (string) ($nv['departmentStatus'] ?? '')];
            }
            if (($pv['submissionDate'] ?? '') != ($nv['submissionDate'] ?? '')) {
                $modifiedData[] = ['amended_field' => 'submission_date', 'previous_value' => (string) ($pv['submissionDate'] ?? ''), 'new_value' => (string) ($nv['submissionDate'] ?? '')];
            }
            if (($pv['timesReferred'] ?? null) != ($nv['timesReferred'] ?? null)) {
                $modifiedData[] = ['amended_field' => 'times_referred', 'previous_value' => (string) ($pv['timesReferred'] ?? ''), 'new_value' => (string) ($nv['timesReferred'] ?? '')];
            }
        } elseif ($auditData['crudType'] == 'D') {
            $pv = is_array($auditData['previosValue'] ?? null) ? $auditData['previosValue'] : [];
            if (!empty($pv)) {
                $modifiedData[] = ['amended_field' => 'planning_code', 'previous_value' => (string) ($pv['planningCode'] ?? ''), 'new_value' => ''];
                $modifiedData[] = ['amended_field' => 'company_system_id', 'previous_value' => (string) ($pv['companySystemID'] ?? ''), 'new_value' => ''];
            }
        }

        return $modifiedData;
    }
}

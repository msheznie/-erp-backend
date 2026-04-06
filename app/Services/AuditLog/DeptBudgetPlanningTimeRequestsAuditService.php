<?php

namespace App\Services\AuditLog;

/**
 * Audit payload builder for dept_budget_planning_time_requests (time extension requests).
 */
class DeptBudgetPlanningTimeRequestsAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];

        if ($auditData['crudType'] == 'C') {
            $nv = is_array($auditData['newValue'] ?? null) ? $auditData['newValue'] : [];
            if (empty($nv)) {
                return $modifiedData;
            }
            $modifiedData[] = ['amended_field' => 'request_code', 'previous_value' => '', 'new_value' => $nv['request_code'] ?? ''];
            $modifiedData[] = [
                'amended_field' => 'current_submission_date',
                'previous_value' => '',
                'new_value' => DepartmentBudgetPlanningAuditService::formatDateOnlyForAudit($nv['current_submission_date'] ?? null),
            ];
            $modifiedData[] = [
                'amended_field' => 'date_of_request',
                'previous_value' => '',
                'new_value' => DepartmentBudgetPlanningAuditService::formatDateOnlyForAudit($nv['date_of_request'] ?? null),
            ];
            $modifiedData[] = ['amended_field' => 'reason_for_extension', 'previous_value' => '', 'new_value' => $nv['reason_for_extension'] ?? ''];
            $modifiedData[] = ['amended_field' => 'status', 'previous_value' => '', 'new_value' => DepartmentBudgetPlanningAuditService::getTimeExtensionStatus($nv['status'] ?? null)];
        } elseif ($auditData['crudType'] == 'U') {
            $pv = is_array($auditData['previosValue'] ?? null) ? $auditData['previosValue'] : [];
            $nv = is_array($auditData['newValue'] ?? null) ? $auditData['newValue'] : [];
            if (($pv['status'] ?? null) != ($nv['status'] ?? null)) {
                $modifiedData[] = [
                    'amended_field' => 'status',
                    'previous_value' => DepartmentBudgetPlanningAuditService::getTimeExtensionStatus($pv['status'] ?? null),
                    'new_value' => DepartmentBudgetPlanningAuditService::getTimeExtensionStatus($nv['status'] ?? null),
                ];
            }
            if (($pv['new_time'] ?? null) != ($nv['new_time'] ?? null)) {
                $modifiedData[] = [
                    'amended_field' => 'new_submission_time',
                    'previous_value' => DepartmentBudgetPlanningAuditService::formatDateOnlyForAudit($pv['new_time'] ?? null),
                    'new_value' => DepartmentBudgetPlanningAuditService::formatDateOnlyForAudit($nv['new_time'] ?? null),
                ];
            }
        } elseif ($auditData['crudType'] == 'D') {
            $pv = is_array($auditData['previosValue'] ?? null) ? $auditData['previosValue'] : [];
            if (empty($pv)) {
                return $modifiedData;
            }
            $modifiedData[] = ['amended_field' => 'request_code', 'previous_value' => $pv['request_code'] ?? '', 'new_value' => ''];
            $modifiedData[] = [
                'amended_field' => 'current_submission_date',
                'previous_value' => DepartmentBudgetPlanningAuditService::formatDateOnlyForAudit($pv['current_submission_date'] ?? null),
                'new_value' => '',
            ];
            $modifiedData[] = [
                'amended_field' => 'date_of_request',
                'previous_value' => DepartmentBudgetPlanningAuditService::formatDateOnlyForAudit($pv['date_of_request'] ?? null),
                'new_value' => '',
            ];
            $modifiedData[] = ['amended_field' => 'reason_for_extension', 'previous_value' => $pv['reason_for_extension'] ?? '', 'new_value' => ''];
            $modifiedData[] = ['amended_field' => 'status', 'previous_value' => DepartmentBudgetPlanningAuditService::getTimeExtensionStatus($pv['status'] ?? null), 'new_value' => ''];
        }

        return $modifiedData;
    }
}

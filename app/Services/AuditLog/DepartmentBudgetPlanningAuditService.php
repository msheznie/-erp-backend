<?php

namespace App\Services\AuditLog;

use Illuminate\Support\Carbon;
use App\Models\CompanyFinanceYear;
use App\Models\WorkflowConfiguration;
use App\Models\Employee;
use App\Models\CompanyDepartmentEmployee;
class DepartmentBudgetPlanningAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];

        if ($auditData['crudType'] == "C") {
            // For creation, log all the new values
            if ($auditData['parentID'] == 0) {
                $modifiedData[] = ['amended_field' => "planning_code", 'previous_value' => '', 'new_value' => $auditData['newValue']['planningCode']];
                $modifiedData[] = ['amended_field' => "initiated_date", 'previous_value' => '', 'new_value' => self::formatDateOnlyForAudit($auditData['newValue']['initiatedDate'])];
                $modifiedData[] = ['amended_field' => "submission_date", 'previous_value' => '', 'new_value' => self::formatDateOnlyForAudit($auditData['newValue']['submissionDate'])];

                $workflow = WorkflowConfiguration::find($auditData['newValue']['workflowID']);
                $modifiedData[] = ['amended_field' => "workflow", 'previous_value' => '', 'new_value' => $workflow->workflowName];
                $modifiedData[] = ['amended_field' => "budget_type", 'previous_value' => '', 'new_value' => self::getType($auditData['newValue']['typeID'])];
                $year = CompanyFinanceYear::find($auditData['newValue']['yearID']);
                $modifiedData[] = ['amended_field' => "budget_year", 'previous_value' => '', 'new_value' => self::formatDateOnlyForAudit($year->bigginingDate) . " | " . self::formatDateOnlyForAudit($year->endingDate)];
                $modifiedData[] = ['amended_field' => "budget_period", 'previous_value' => '', 'new_value' => 'Yearly'];
            }
        }
        else if ($auditData['crudType'] == "U") {
            // For updates, compare old and new values
            if ($auditData['parentID'] == 0) {
                $pv = is_array($auditData['previosValue'] ?? null) ? $auditData['previosValue'] : [];
                $nv = is_array($auditData['newValue'] ?? null) ? $auditData['newValue'] : [];


                if (($pv['workStatus'] ?? null) != ($nv['workStatus'] ?? null)) {
                    $modifiedData[] = ['amended_field' => "department_status", 'previous_value' => self::getWorkStatus($pv['workStatus'] ?? null), 'new_value' => self::getWorkStatus($nv['workStatus'] ?? null)];
                }
                if (($pv['financeTeamStatus'] ?? null) != ($nv['financeTeamStatus'] ?? null)) {
                    $modifiedData[] = ['amended_field' => "finance_team_status", 'previous_value' => self::getFinanceTeamStatus($pv['financeTeamStatus'] ?? null), 'new_value' => self::getFinanceTeamStatus($nv['financeTeamStatus'] ?? null)];
                }
                if (($pv['confirmed_yn'] ?? null) != ($nv['confirmed_yn'] ?? null)) {
                    $modifiedData[] = ['amended_field' => "department_confirmed", 'previous_value' => self::yesNo($pv['confirmed_yn'] ?? null), 'new_value' => self::yesNo($nv['confirmed_yn'] ?? null)];
                }
                if (($pv['typeID'] ?? null) != ($nv['typeID'] ?? null)) {
                    $modifiedData[] = ['amended_field' => "budget_type", 'previous_value' => self::getType($pv['typeID'] ?? null), 'new_value' => self::getType($nv['typeID'] ?? null)];
                }
            } elseif ($auditData['parentID'] == 2) {
                // Delegate work status / context (synthetic payloads from controllers)
                $pv = is_array($auditData['previosValue'] ?? null) ? $auditData['previosValue'] : [];
                $nv = is_array($auditData['newValue'] ?? null) ? $auditData['newValue'] : [];
                if (($pv['delegate_work_status'] ?? null) != ($nv['delegate_work_status'] ?? null)) {
                    $modifiedData[] = [
                        'amended_field' => 'Delegate work status',
                        'previous_value' => self::formatDelegateWorkStatusForAudit($pv['delegate_work_status'] ?? null),
                        'new_value' => self::formatDelegateWorkStatusForAudit($nv['delegate_work_status'] ?? null),
                    ];
                }
                if (($pv['budget_planning_detail_id'] ?? null) != ($nv['budget_planning_detail_id'] ?? null)) {
                    $modifiedData[] = [
                        'amended_field' => 'Budget planning detail ID',
                        'previous_value' => (string) ($pv['budget_planning_detail_id'] ?? ''),
                        'new_value' => (string) ($nv['budget_planning_detail_id'] ?? ''),
                    ];
                }
                if (($pv['delegatee_employee_system_id'] ?? null) != ($nv['delegatee_employee_system_id'] ?? null)) {
                    $modifiedData[] = [
                        'amended_field' => 'Delegatee employee ID',
                        'previous_value' => (string) self::getEmployeeName($pv['delegatee_employee_system_id'] ?? ''),
                        'new_value' => (string) self::getEmployeeName($nv['delegatee_employee_system_id'] ?? ''),
                    ];
                }
            }

        }
        else if ($auditData['crudType'] == "D") {
            // Department planning row deleted (parentID 0)
            if ($auditData['parentID'] == 0 && is_array($auditData['previosValue']) && !empty($auditData['previosValue'])) {
                $pv = $auditData['previosValue'];
                $modifiedData[] = ['amended_field' => "planning_code", 'previous_value' => $pv['planningCode'] ?? '', 'new_value' => ''];
                $modifiedData[] = ['amended_field' => "department_id", 'previous_value' => (string) ($pv['departmentID'] ?? ''), 'new_value' => ''];
            }
        }

        return $modifiedData;
    }

    public static function getEmployeeName($employeeID)
    {

        $employee = Employee::find($employeeID);

        if(empty($employee))
        {
            return '';
        }

        return $employee->empFullName . ' (' . $employee->empID . ')';
    }
    public static function getFinanceTeamStatus($status)
    {
        switch ((int) $status) {
            case 1:
                return 'Open';
            case 2:
                return 'Under Review';
            case 3:
                return 'Sent Back for Revision';
            case 4:
                return 'Completed';
            default:
                return '';
        }
    }

    public static function yesNo($value)
    {
        return ((int) $value === 1 || $value === true || $value === '1') ? 'Yes' : 'No';
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
                return "Submitted to Finance";
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

    /**
     * Maps dep_budget_pl_delegate_details.work_status codes to UI labels (avoids raw "1" being shown as Yes/No in audit viewers).
     *
     * @see \App\Models\BudgetDelegateAccessRecord work status constants
     */
    public static function getDelegateWorkStatusLabel($status)
    {
        $key = is_numeric($status) ? (int) $status : $status;
        switch ($key) {
            case 1:
            case '1':
                return trans('custom.work_status_not_started');
            case 2:
            case '2':
                return trans('custom.work_status_in_progress');
            case 3:
            case '3':
                return 'Submitted to HOD';
            default:
                return $status === null || $status === '' ? '' : (string) $status;
        }
    }

    public static function formatDelegateWorkStatusForAudit($status)
    {
        if ($status === null || $status === '') {
            return '';
        }
        if (is_string($status) && !is_numeric($status) && $status !== 'batch_assigned') {
            return $status;
        }

        return self::getDelegateWorkStatusLabel($status);
    }

    /**
     * Normalize values for audit display as calendar date only (no time component).
     * Adds one day after parsing to align stored UTC datetimes with the intended calendar date.
     */
    public static function formatDateOnlyForAudit($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        try {
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->addDays(1)->format('Y-m-d');
            }

            return Carbon::parse($value)->addDays(1)->format('Y-m-d');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}

<?php

namespace App\Services\AuditLog;

use App\Models\Company;
use App\Models\Employee;
use App\Models\UserGroup;

class EmployeeNavigationAssignAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];

        if ($auditData['crudType'] == "C") {
            // For creation (assignment), log all the new values

            // Get employee name for employeeSystemID
            $employee = Employee::where('employeeSystemID', $auditData['newValue']['employeeSystemID'] ?? null)->first();
            $modifiedData[] = ['amended_field' => "employee", 'previous_value' => '', 'new_value' => ($employee) ? $employee->empName : ''];

            // Get user group name for userGroupID
            $userGroup = UserGroup::where('userGroupID', $auditData['newValue']['userGroupID'] ?? null)->first();
            $modifiedData[] = ['amended_field' => "user_group", 'previous_value' => '', 'new_value' => ($userGroup) ? $userGroup->description : ''];

            // Get company name for companyID
            $company = Company::where('companySystemID', $auditData['newValue']['companyID'] ?? null)->first();
            $modifiedData[] = ['amended_field' => "company", 'previous_value' => '', 'new_value' => ($company) ? $company->CompanyName : ''];

        } else if ($auditData['crudType'] == "D") {
            // For deletion (unassignment), log all the previous values

            // Get employee name for employeeSystemID
            $employee = Employee::where('employeeSystemID', $auditData['previosValue']['employeeSystemID'] ?? null)->first();
            $modifiedData[] = ['amended_field' => "employee", 'previous_value' => ($employee) ? $employee->empName : '', 'new_value' => ''];

            // Get user group name for userGroupID
            $userGroup = UserGroup::where('userGroupID', $auditData['previosValue']['userGroupID'] ?? null)->first();
            $modifiedData[] = ['amended_field' => "user_group", 'previous_value' => ($userGroup) ? $userGroup->description : '', 'new_value' => ''];

            // Get company name for companyID
            $company = Company::where('companySystemID', $auditData['previosValue']['companyID'] ?? null)->first();
            $modifiedData[] = ['amended_field' => "company", 'previous_value' => ($company) ? $company->CompanyName : '', 'new_value' => ''];
        }
        // Note: Update (U) is not typically used for employee navigation assignments
        // If needed in the future, it would handle changes to employee, user group, or company

        return $modifiedData;
    }
}

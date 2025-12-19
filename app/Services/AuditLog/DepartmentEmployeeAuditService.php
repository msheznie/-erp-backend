<?php

namespace App\Services\AuditLog;

use App\Models\CompanyDepartment;
use App\Models\Employee;

class DepartmentEmployeeAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];
        
        if ($auditData['crudType'] == "C") {
            // For creation, log all the new values
            
            // Get department name for departmentSystemID
            $department = CompanyDepartment::where('departmentSystemID', $auditData['newValue']['departmentSystemID'])->first();
            $modifiedData[] = ['amended_field' => "department", 'previous_value' => '', 'new_value' => ($department) ? $department->departmentDescription : ''];
            
            // Get employee name for employeeSystemID
            $employee = Employee::where('employeeSystemID', $auditData['newValue']['employeeSystemID'])->first();
            $modifiedData[] = ['amended_field' => "employee", 'previous_value' => '', 'new_value' => ($employee) ? $employee->empName : ''];
            
            $modifiedData[] = ['amended_field' => "is_hod", 'previous_value' => '', 'new_value' => ($auditData['newValue']['isHOD'] == 1) ? 'yes' : 'no'];
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => '', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            
        } else if ($auditData['crudType'] == "U") {
            // For updates, compare old and new values
            
            if($auditData['previosValue']['departmentSystemID'] != $auditData['newValue']['departmentSystemID']) {
                $oldDepartment = CompanyDepartment::where('departmentSystemID', $auditData['previosValue']['departmentSystemID'])->first();
                $newDepartment = CompanyDepartment::where('departmentSystemID', $auditData['newValue']['departmentSystemID'])->first();
                $modifiedData[] = ['amended_field' => "department", 'previous_value' => ($oldDepartment) ? $oldDepartment->departmentDescription : '', 'new_value' => ($newDepartment) ? $newDepartment->departmentDescription : ''];
            }
            
            if($auditData['previosValue']['employeeSystemID'] != $auditData['newValue']['employeeSystemID']) {
                $oldEmployee = Employee::where('employeeSystemID', $auditData['previosValue']['employeeSystemID'])->first();
                $newEmployee = Employee::where('employeeSystemID', $auditData['newValue']['employeeSystemID'])->first();
                $modifiedData[] = ['amended_field' => "employee", 'previous_value' => ($oldEmployee) ? $oldEmployee->empName : '', 'new_value' => ($newEmployee) ? $newEmployee->empName : ''];
            }
            
            if($auditData['previosValue']['isHOD'] != $auditData['newValue']['isHOD']) {
                $modifiedData[] = ['amended_field' => "is_hod", 'previous_value' => ($auditData['previosValue']['isHOD'] == 1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isHOD'] == 1) ? 'yes' : 'no'];
            }
            
            if($auditData['previosValue']['isActive'] != $auditData['newValue']['isActive']) {
                $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            }
            
        } else if ($auditData['crudType'] == "D") {
            // For deletion, log all the previous values
            
            $department = CompanyDepartment::where('departmentSystemID', $auditData['previosValue']['departmentSystemID'])->first();
            $modifiedData[] = ['amended_field' => "department", 'previous_value' => ($department) ? $department->departmentDescription : '', 'new_value' => ''];
            
            $employee = Employee::where('employeeSystemID', $auditData['previosValue']['employeeSystemID'])->first();
            $modifiedData[] = ['amended_field' => "employee", 'previous_value' => ($employee) ? $employee->empName : '', 'new_value' => ''];
            
            $modifiedData[] = ['amended_field' => "is_hod", 'previous_value' => ($auditData['previosValue']['isHOD'] == 1) ? 'yes' : 'no', 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'yes' : 'no', 'new_value' => ''];
        }

        return $modifiedData;
    }
} 
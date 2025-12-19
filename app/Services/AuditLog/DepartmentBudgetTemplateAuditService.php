<?php

namespace App\Services\AuditLog;

use App\Models\CompanyDepartment;
use App\Models\BudgetTemplate;

class DepartmentBudgetTemplateAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];
        
        if ($auditData['crudType'] == "C") {
            // For creation, log all the new values
            
            // Get department name for departmentSystemID
            $department = CompanyDepartment::where('departmentSystemID', $auditData['newValue']['departmentSystemID'])->first();
            $modifiedData[] = ['amended_field' => "department", 'previous_value' => '', 'new_value' => ($department) ? $department->departmentDescription : ''];
            
            // Get budget template name for budgetTemplateID
            $budgetTemplate = BudgetTemplate::where('budgetTemplateID', $auditData['newValue']['budgetTemplateID'])->first();
            $modifiedData[] = ['amended_field' => "budget_template", 'previous_value' => '', 'new_value' => ($budgetTemplate) ? $budgetTemplate->description : ''];
            
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => '', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            
        } elseif ($auditData['crudType'] == "U") {
            // For updates, compare old and new values
            
            if($auditData['previosValue']['departmentSystemID'] != $auditData['newValue']['departmentSystemID']) {
                $oldDepartment = CompanyDepartment::where('departmentSystemID', $auditData['previosValue']['departmentSystemID'])->first();
                $newDepartment = CompanyDepartment::where('departmentSystemID', $auditData['newValue']['departmentSystemID'])->first();
                $modifiedData[] = ['amended_field' => "department", 'previous_value' => ($oldDepartment) ? $oldDepartment->departmentDescription : '', 'new_value' => ($newDepartment) ? $newDepartment->departmentDescription : ''];
            }
            
            if($auditData['previosValue']['budgetTemplateID'] != $auditData['newValue']['budgetTemplateID']) {
                $oldBudgetTemplate = BudgetTemplate::where('budgetTemplateID', $auditData['previosValue']['budgetTemplateID'])->first();
                $newBudgetTemplate = BudgetTemplate::where('budgetTemplateID', $auditData['newValue']['budgetTemplateID'])->first();
                $modifiedData[] = ['amended_field' => "budget_template", 'previous_value' => ($oldBudgetTemplate) ? $oldBudgetTemplate->description : '', 'new_value' => ($newBudgetTemplate) ? $newBudgetTemplate->description : ''];
            }
            
            if($auditData['previosValue']['isActive'] != $auditData['newValue']['isActive']) {
                $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            }
            
        } elseif ($auditData['crudType'] == "D") {
            // For deletion, log all the previous values
            
            $department = CompanyDepartment::where('departmentSystemID', $auditData['previosValue']['departmentSystemID'])->first();
            $modifiedData[] = ['amended_field' => "department", 'previous_value' => ($department) ? $department->departmentDescription : '', 'new_value' => ''];
            
            $budgetTemplate = BudgetTemplate::where('budgetTemplateID', $auditData['previosValue']['budgetTemplateID'])->first();
            $modifiedData[] = ['amended_field' => "budget_template", 'previous_value' => ($budgetTemplate) ? $budgetTemplate->description : '', 'new_value' => ''];
            
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'yes' : 'no', 'new_value' => ''];
        }

        return $modifiedData;
    }
} 
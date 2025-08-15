<?php

namespace App\Services\AuditLog;

use App\Models\Company;
use App\Models\CompanyDepartment;

class DepartmentAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];
        
        if ($auditData['crudType'] == "C") {
            // For creation, log all the new values
            $modifiedData[] = ['amended_field' => "department_code", 'previous_value' => '', 'new_value' => $auditData['newValue']['departmentCode']];
            $modifiedData[] = ['amended_field' => "department_description", 'previous_value' => '', 'new_value' => $auditData['newValue']['departmentDescription']];
            
            // Get company name for companySystemID
            $company = Company::where('companySystemID', $auditData['newValue']['companySystemID'])->first();
            $modifiedData[] = ['amended_field' => "company", 'previous_value' => '', 'new_value' => ($company) ? $company->CompanyName : ''];

            // parent department logic as if parentdepartment id is null get the company name, else get the department name
            if(isset($auditData['newValue']['parentDepartmentID']) && !is_null($auditData['newValue']['parentDepartmentID'])) {
                $parentDepartment = CompanyDepartment::where('departmentSystemID', $auditData['newValue']['parentDepartmentID'])->first();
                $modifiedData[] = ['amended_field' => "parent_department", 'previous_value' => '', 'new_value' => ($parentDepartment) ? $parentDepartment->departmentDescription : ''];
            } else {
                $company = Company::where('companySystemID', $auditData['newValue']['companySystemID'])->first();
                $modifiedData[] = ['amended_field' => "parent_department", 'previous_value' => '', 'new_value' => ($company) ? $company->CompanyName : ''];
            }
            
            $modifiedData[] = ['amended_field' => "is_finance", 'previous_value' => '', 'new_value' => ($auditData['newValue']['isFinance'] == 1) ? 'yes' : 'no'];
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => '', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            
        } else if ($auditData['crudType'] == "U") {
            // For updates, compare old and new values
            
            if($auditData['previosValue']['departmentCode'] != $auditData['newValue']['departmentCode']) {
                $modifiedData[] = ['amended_field' => "department_code", 'previous_value' => $auditData['previosValue']['departmentCode'], 'new_value' => $auditData['newValue']['departmentCode']];
            }
            
            if($auditData['previosValue']['departmentDescription'] != $auditData['newValue']['departmentDescription']) {
                $modifiedData[] = ['amended_field' => "department_description", 'previous_value' => $auditData['previosValue']['departmentDescription'], 'new_value' => $auditData['newValue']['departmentDescription']];
            }
            
            if($auditData['previosValue']['companySystemID'] != $auditData['newValue']['companySystemID']) {
                $oldCompany = Company::where('companySystemID', $auditData['previosValue']['companySystemID'])->first();
                $newCompany = Company::where('companySystemID', $auditData['newValue']['companySystemID'])->first();
                $modifiedData[] = ['amended_field' => "company", 'previous_value' => ($oldCompany) ? $oldCompany->CompanyName : '', 'new_value' => ($newCompany) ? $newCompany->CompanyName : ''];
            }
            
            if($auditData['previosValue']['type'] != $auditData['newValue']['type']) {
                $oldType = ($auditData['previosValue']['type'] == 1) ? 'Parent' : 'Final';
                $newType = ($auditData['newValue']['type'] == 1) ? 'Parent' : 'Final';
                $modifiedData[] = ['amended_field' => "type", 'previous_value' => $oldType, 'new_value' => $newType];
            }
            
            //if parent department id is null get the company name, else get the department name, for previous value and new value
            if(is_null($auditData['previosValue']['parentDepartmentID'])) {
                $oldParentDepartment = Company::where('companySystemID', $auditData['previosValue']['companySystemID'])->first();
                $oldParentDepartment = ($oldParentDepartment) ? $oldParentDepartment->CompanyName : '';
            } else {
                $oldParentDepartment = CompanyDepartment::where('departmentSystemID', $auditData['previosValue']['parentDepartmentID'])->first();
                $oldParentDepartment = ($oldParentDepartment) ? $oldParentDepartment->departmentDescription : '';
            }

            if(is_null($auditData['newValue']['parentDepartmentID'])) { 
                $newParentDepartment = Company::where('companySystemID', $auditData['newValue']['companySystemID'])->first();
                $newParentDepartment = ($newParentDepartment) ? $newParentDepartment->CompanyName : '';
            } else {
                $newParentDepartment = CompanyDepartment::where('departmentSystemID', $auditData['newValue']['parentDepartmentID'])->first();
                $newParentDepartment = ($newParentDepartment) ? $newParentDepartment->departmentDescription : '';
            }

            $modifiedData[] = ['amended_field' => "parent_department", 'previous_value' => $oldParentDepartment, 'new_value' => $newParentDepartment];
            
            if($auditData['previosValue']['isFinance'] != $auditData['newValue']['isFinance']) {
                $modifiedData[] = ['amended_field' => "is_finance", 'previous_value' => ($auditData['previosValue']['isFinance'] == 1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isFinance'] == 1) ? 'yes' : 'no'];
            }
            
            if($auditData['previosValue']['isActive'] != $auditData['newValue']['isActive']) {
                $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            }
            
        } else if ($auditData['crudType'] == "D") {
            // For deletion, log all the previous values
            $modifiedData[] = ['amended_field' => "department_code", 'previous_value' => $auditData['previosValue']['departmentCode'], 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "department_description", 'previous_value' => $auditData['previosValue']['departmentDescription'], 'new_value' => ''];
            
            $company = Company::where('companySystemID', $auditData['previosValue']['companySystemID'])->first();
            $modifiedData[] = ['amended_field' => "company", 'previous_value' => ($company) ? $company->CompanyName : '', 'new_value' => ''];
            
            $typeValue = ($auditData['previosValue']['type'] == 1) ? 'Parent' : 'Final';
            $modifiedData[] = ['amended_field' => "type", 'previous_value' => $typeValue, 'new_value' => ''];
            
            //parent department logic as if parentdepartment id is null get the company name, else get the department name
            if(isset($auditData['previosValue']['parentDepartmentID']) && !is_null($auditData['previosValue']['parentDepartmentID'])) {
                $parentDepartment = CompanyDepartment::where('departmentSystemID', $auditData['previosValue']['parentDepartmentID'])->first();
                $modifiedData[] = ['amended_field' => "parent_department", 'previous_value' => ($parentDepartment) ? $parentDepartment->departmentDescription : '', 'new_value' => ''];
            } else {
                $company = Company::where('companySystemID', $auditData['previosValue']['companySystemID'])->first();
                $modifiedData[] = ['amended_field' => "parent_department", 'previous_value' => ($company) ? $company->CompanyName : '', 'new_value' => ''];
            }
            
            $modifiedData[] = ['amended_field' => "is_finance", 'previous_value' => ($auditData['previosValue']['isFinance'] == 1) ? 'yes' : 'no', 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'yes' : 'no', 'new_value' => ''];
        }

        return $modifiedData;
    }
} 
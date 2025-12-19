<?php

namespace App\Services\AuditLog;

use App\Models\Company;
use App\Models\WorkflowConfiguration;

class WorkflowConfigurationAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];
        
        if ($auditData['crudType'] == "C") {
            // For creation, log all the new values
            $modifiedData[] = ['amended_field' => "workflow_name", 'previous_value' => '', 'new_value' => $auditData['newValue']['workflowName']];

            $modifiedData[] = ['amended_field' => "initiate_budget", 'previous_value' => '', 'new_value' => self::getInitiateBudget($auditData['newValue']['initiateBudget'])];

            $modifiedData[] = ['amended_field' => "method", 'previous_value' => '', 'new_value' => self::getMethod($auditData['newValue']['method'])];

            $modifiedData[] = ['amended_field' => "allocation", 'previous_value' => '', 'new_value' => self::getAllocation($auditData['newValue']['allocation'])];
            $modifiedData[] = ['amended_field' => "final_approval", 'previous_value' => '', 'new_value' => $auditData['newValue']['finalApproval']];
            
            // Get company name for companySystemID
            $company = Company::where('companySystemID', $auditData['newValue']['companySystemID'])->first();
            $modifiedData[] = ['amended_field' => "company", 'previous_value' => '', 'new_value' => ($company) ? $company->CompanyName : ''];
            
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => '', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            
        } else if ($auditData['crudType'] == "U") {
            // For updates, compare old and new values
            
            if($auditData['previosValue']['workflowName'] != $auditData['newValue']['workflowName']) {
                $modifiedData[] = ['amended_field' => "workflow_name", 'previous_value' => $auditData['previosValue']['workflowName'], 'new_value' => $auditData['newValue']['workflowName']];
            }
            
            if($auditData['previosValue']['initiateBudget'] != $auditData['newValue']['initiateBudget']) {
                $modifiedData[] = ['amended_field' => "initiate_budget", 'previous_value' => self::getInitiateBudget($auditData['previosValue']['initiateBudget']), 'new_value' => self::getInitiateBudget($auditData['newValue']['initiateBudget'])];
            }
            
            if($auditData['previosValue']['method'] != $auditData['newValue']['method']) {
                $modifiedData[] = ['amended_field' => "method", 'previous_value' => self::getMethod($auditData['previosValue']['method']), 'new_value' => self::getMethod($auditData['newValue']['method'])];
            }
            
            if($auditData['previosValue']['allocation'] != $auditData['newValue']['allocation']) {
                $modifiedData[] = ['amended_field' => "allocation", 'previous_value' => self::getAllocation($auditData['previosValue']['allocation']), 'new_value' => self::getAllocation($auditData['newValue']['allocation'])];
            }
            
            if($auditData['previosValue']['finalApproval'] != $auditData['newValue']['finalApproval']) {
                $modifiedData[] = ['amended_field' => "final_approval", 'previous_value' => $auditData['previosValue']['finalApproval'], 'new_value' => $auditData['newValue']['finalApproval']];
            }
            
            if($auditData['previosValue']['companySystemID'] != $auditData['newValue']['companySystemID']) {
                $oldCompany = Company::where('companySystemID', $auditData['previosValue']['companySystemID'])->first();
                $newCompany = Company::where('companySystemID', $auditData['newValue']['companySystemID'])->first();
                $modifiedData[] = ['amended_field' => "company", 'previous_value' => ($oldCompany) ? $oldCompany->CompanyName : '', 'new_value' => ($newCompany) ? $newCompany->CompanyName : ''];
            }
            
            if($auditData['previosValue']['isActive'] != $auditData['newValue']['isActive']) {
                $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            }
            
        } else if ($auditData['crudType'] == "D") {
            // For deletion, log all the previous values
            $modifiedData[] = ['amended_field' => "workflow_name", 'previous_value' => $auditData['previosValue']['workflowName'], 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "initiate_budget", 'previous_value' => self::getInitiateBudget($auditData['previosValue']['initiateBudget']), 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "method", 'previous_value' => self::getMethod($auditData['previosValue']['method']), 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "allocation", 'previous_value' => self::getAllocation($auditData['previosValue']['allocation']), 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "final_approval", 'previous_value' => $auditData['previosValue']['finalApproval'], 'new_value' => ''];
            
            $company = Company::where('companySystemID', $auditData['previosValue']['companySystemID'])->first();
            $modifiedData[] = ['amended_field' => "company", 'previous_value' => ($company) ? $company->CompanyName : '', 'new_value' => ''];
            
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'yes' : 'no', 'new_value' => ''];
        }

        return $modifiedData;
    }

    public static function getInitiateBudget($data) {
        if($data == 1) {
            return 'Finance Team';
        }
        else {
            return '';
        }
    }

    public static function getMethod($data) {
        if($data == 1) {
            return 'Segment-Based';
        }
        else {
            return 'GL-Based';
        }
    }

    public static function getAllocation($data) {
        $allocation = '';
        switch ($data) {
            case 1:
                $allocation = 'Department HOD';
                break;
            case 2:
                $allocation = 'Parent HOD';
                break;
            case 3:
                $allocation = 'Finance Team';
                break;
            default:
                $allocation = '';
                break;
        }

        return $allocation;
    }
}

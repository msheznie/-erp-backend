<?php

namespace App\Services\AuditLog;

use App\Models\Company;
use App\Models\ErpAttributesFieldType;

class ItemFinanceCategorySubAssignedAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];
        if ($auditData['crudType'] == "C") {
            $company = Company::find($auditData['newValue']['companySystemID']);
            $companyID = $company->CompanyID;
            $companyName = $company->CompanyName;
            $modifiedData[] = ['amended_field' => "company_id", 'previous_value' => '', 'new_value' => $companyID];
            $modifiedData[] = ['amended_field' => "company_name", 'previous_value' => '', 'new_value' => $companyName];
            $modifiedData[] = ['amended_field' => "is_assigned", 'previous_value' => '', 'new_value' => ($auditData['newValue']['isAssigned']) ? 'True' : 'False'];
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => '', 'new_value' => ($auditData['newValue']['isActive']) ? 'True' : 'False'];
        } elseif ($auditData['crudType'] == "U"){
            if($auditData['previosValue']['isAssigned'] != $auditData['newValue']['isAssigned']) {
                $modifiedData[] = ['amended_field' => "is_assigned", 'previous_value' => ($auditData['previosValue']['isAssigned']) ? 'True' : 'False', 'new_value' => ($auditData['newValue']['isAssigned']) ? 'True' : 'False'];
            }
            if($auditData['previosValue']['isActive'] != $auditData['newValue']['isActive']) {
                $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive']) ? 'True' : 'False', 'new_value' => ($auditData['newValue']['isActive']) ? 'True' : 'False'];
            }
        } else if ($auditData['crudType'] == "D") {
            $company = Company::find($auditData['previosValue']['companySystemID']);
            $companyID = $company->CompanyID;
            $companyName = $company->CompanyName;
            $modifiedData[] = ['amended_field' => "company_id", 'previous_value' => $companyID, 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "company_name", 'previous_value' => $companyName, 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "is_assigned", 'previous_value' => ($auditData['previosValue']['isAssigned']) ? 'True' : 'False', 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' =>  ($auditData['previosValue']['isActive']) ? 'True' : 'False', 'new_value' => ''];
    }

        return $modifiedData;
    }
}

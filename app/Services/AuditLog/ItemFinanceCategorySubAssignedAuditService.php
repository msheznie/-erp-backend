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
            $modifiedData[] = ['amended_field' => "is_assigned", 'previous_value' => '', 'new_value' => ($auditData['newValue']['isAssigned']) ? 'yes' : 'no'];
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => '', 'new_value' => ($auditData['newValue']['isActive']) ? 'yes' : 'no'];
        } elseif ($auditData['crudType'] == "U"){
            if($auditData['previosValue']['isAssigned'] != $auditData['newValue']['isAssigned']) {
                $modifiedData[] = ['amended_field' => "is_assigned", 'previous_value' => ($auditData['previosValue']['isAssigned']) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isAssigned']) ? 'yes' : 'no'];
            }
            if($auditData['previosValue']['isActive'] != $auditData['newValue']['isActive']) {
                $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive']) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isActive']) ? 'yes' : 'no'];
            }
        } else if ($auditData['crudType'] == "D") {
            $company = Company::find($auditData['previosValue']['companySystemID']);
            $companyID = $company->CompanyID;
            $companyName = $company->CompanyName;
            $modifiedData[] = ['amended_field' => "company_id", 'previous_value' => $companyID, 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "company_name", 'previous_value' => $companyName, 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "is_assigned", 'previous_value' => ($auditData['previosValue']['isAssigned']) ? 'yes' : 'no', 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' =>  ($auditData['previosValue']['isActive']) ? 'yes' : 'no', 'new_value' => ''];
    }

        return $modifiedData;
    }
}

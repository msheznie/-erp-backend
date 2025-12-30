<?php

namespace App\Services\AuditLog;

use App\Models\Company;
use App\Models\UserGroup;

class UserGroupAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];

        if ($auditData['crudType'] == "C") {
            // For creation, log all the new values
            $modifiedData[] = ['amended_field' => "user_group_description", 'previous_value' => '', 'new_value' => $auditData['newValue']['description'] ?? ''];

            // Get company name for companyID
            $company = Company::where('companySystemID', $auditData['newValue']['companyID'] ?? null)->first();
            $modifiedData[] = ['amended_field' => "company", 'previous_value' => '', 'new_value' => ($company) ? $company->CompanyName : ''];

            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => '', 'new_value' => (isset($auditData['newValue']['isActive']) && $auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            $modifiedData[] = ['amended_field' => "is_default", 'previous_value' => '', 'new_value' => (isset($auditData['newValue']['defaultYN']) && $auditData['newValue']['defaultYN'] == 1) ? 'yes' : 'no'];

        } else if ($auditData['crudType'] == "U") {
            // For updates, compare old and new values

            if(isset($auditData['previosValue']['description']) && isset($auditData['newValue']['description']) &&
                $auditData['previosValue']['description'] != $auditData['newValue']['description']) {
                $modifiedData[] = ['amended_field' => "user_group_description", 'previous_value' => $auditData['previosValue']['description'], 'new_value' => $auditData['newValue']['description']];
            }

            if(isset($auditData['previosValue']['companyID']) && isset($auditData['newValue']['companyID']) &&
                $auditData['previosValue']['companyID'] != $auditData['newValue']['companyID']) {
                $oldCompany = Company::where('companySystemID', $auditData['previosValue']['companyID'])->first();
                $newCompany = Company::where('companySystemID', $auditData['newValue']['companyID'])->first();
                $modifiedData[] = ['amended_field' => "company", 'previous_value' => ($oldCompany) ? $oldCompany->CompanyName : '', 'new_value' => ($newCompany) ? $newCompany->CompanyName : ''];
            }

            if(isset($auditData['previosValue']['isActive']) && isset($auditData['newValue']['isActive']) &&
                $auditData['previosValue']['isActive'] != $auditData['newValue']['isActive']) {
                $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            }

            if(isset($auditData['previosValue']['defaultYN']) && isset($auditData['newValue']['defaultYN']) &&
                $auditData['previosValue']['defaultYN'] != $auditData['newValue']['defaultYN']) {
                $modifiedData[] = ['amended_field' => "is_default", 'previous_value' => ($auditData['previosValue']['defaultYN'] == 1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['defaultYN'] == 1) ? 'yes' : 'no'];
            }

        } else if ($auditData['crudType'] == "D") {
            // For deletion, log all the previous values
            $modifiedData[] = ['amended_field' => "user_group_description", 'previous_value' => $auditData['previosValue']['description'] ?? '', 'new_value' => ''];

            $company = Company::where('companySystemID', $auditData['previosValue']['companyID'] ?? null)->first();
            $modifiedData[] = ['amended_field' => "company", 'previous_value' => ($company) ? $company->CompanyName : '', 'new_value' => ''];

            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => (isset($auditData['previosValue']['isActive']) && $auditData['previosValue']['isActive'] == 1) ? 'yes' : 'no', 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "is_default", 'previous_value' => (isset($auditData['previosValue']['defaultYN']) && $auditData['previosValue']['defaultYN'] == 1) ? 'yes' : 'no', 'new_value' => ''];
        }

        return $modifiedData;
    }
}

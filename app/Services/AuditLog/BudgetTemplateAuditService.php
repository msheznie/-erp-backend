<?php

namespace App\Services\AuditLog;

use App\Models\Company;
use App\Models\BudgetTemplateColumn;

class BudgetTemplateAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];
        
        if ($auditData['crudType'] == "C") {
            // For creation, log all the new values
            $modifiedData[] = ['amended_field' => "description", 'previous_value' => '', 'new_value' => $auditData['newValue']['description']];
            
            // Get type label
            $typeLabel = self::getTypeLabel($auditData['newValue']['type']);
            $modifiedData[] = ['amended_field' => "type", 'previous_value' => '', 'new_value' => $typeLabel];
            
            // Get company name for companySystemID
            $company = Company::where('companySystemID', $auditData['newValue']['companySystemID'])->first();
            $modifiedData[] = ['amended_field' => "company", 'previous_value' => '', 'new_value' => ($company) ? $company->CompanyName : ''];
            
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => '', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            $modifiedData[] = ['amended_field' => "is_default", 'previous_value' => '', 'new_value' => ($auditData['newValue']['isDefault'] == 1) ? 'yes' : 'no'];
            
        } elseif ($auditData['crudType'] == "U") {
            // For updates, compare old and new values
            
            if($auditData['previosValue']['description'] != $auditData['newValue']['description']) {
                $modifiedData[] = ['amended_field' => "description", 'previous_value' => $auditData['previosValue']['description'], 'new_value' => $auditData['newValue']['description']];
            }
            
            if($auditData['previosValue']['type'] != $auditData['newValue']['type']) {
                $oldTypeLabel = self::getTypeLabel($auditData['previosValue']['type']);
                $newTypeLabel = self::getTypeLabel($auditData['newValue']['type']);
                $modifiedData[] = ['amended_field' => "type", 'previous_value' => $oldTypeLabel, 'new_value' => $newTypeLabel];
            }
            
            if($auditData['previosValue']['companySystemID'] != $auditData['newValue']['companySystemID']) {
                $oldCompany = Company::where('companySystemID', $auditData['previosValue']['companySystemID'])->first();
                $newCompany = Company::where('companySystemID', $auditData['newValue']['companySystemID'])->first();
                $modifiedData[] = ['amended_field' => "company", 'previous_value' => ($oldCompany) ? $oldCompany->CompanyName : '', 'new_value' => ($newCompany) ? $newCompany->CompanyName : ''];
            }
            
            if($auditData['previosValue']['isActive'] != $auditData['newValue']['isActive']) {
                $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            }
            
            if($auditData['previosValue']['isDefault'] != $auditData['newValue']['isDefault']) {
                $modifiedData[] = ['amended_field' => "is_default", 'previous_value' => ($auditData['previosValue']['isDefault'] == 1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isDefault'] == 1) ? 'yes' : 'no'];
            }

            if($auditData['previosValue']['linkRequestAmount'] != $auditData['newValue']['linkRequestAmount']) {
                //get the link request amount from the budget template columns

                $budgetTemplatePrevios = BudgetTemplateColumn::with('preColumn')->where('templateColumnID', $auditData['previosValue']['linkRequestAmount'])->get();
                $budgetTemplateNew = BudgetTemplateColumn::with('preColumn')->where('templateColumnID', $auditData['newValue']['linkRequestAmount'])->get();

                //get the pre column name from the budget template columns, pre column name is one value not array
                $preColumnPrevios = $budgetTemplatePrevios->pluck('preColumn.columnName')->first();
                $preColumnNew = $budgetTemplateNew->pluck('preColumn.columnName')->first();

                $modifiedData[] = ['amended_field' => "link_request_amount", 'previous_value' => $preColumnPrevios, 'new_value' => $preColumnNew];
            }
            
        } elseif ($auditData['crudType'] == "D") {
            // For deletion, log all the previous values
            
            $modifiedData[] = ['amended_field' => "description", 'previous_value' => $auditData['previosValue']['description'], 'new_value' => ''];
            
            $typeLabel = self::getTypeLabel($auditData['previosValue']['type']);
            $modifiedData[] = ['amended_field' => "type", 'previous_value' => $typeLabel, 'new_value' => ''];
            
            $company = Company::where('companySystemID', $auditData['previosValue']['companySystemID'])->first();
            $modifiedData[] = ['amended_field' => "company", 'previous_value' => ($company) ? $company->CompanyName : '', 'new_value' => ''];
            
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'yes' : 'no', 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "is_default", 'previous_value' => ($auditData['previosValue']['isDefault'] == 1) ? 'yes' : 'no', 'new_value' => ''];
        }

        return $modifiedData;
    }
    
    /**
     * Get type label based on type value
     */
    private static function getTypeLabel($type)
    {
        switch ($type) {
            case 1:
                return 'OPEX';
            case 2:
                return 'CAPEX';
            case 3:
                return 'Common';
            default:
                return 'Unknown';
        }
    }
} 
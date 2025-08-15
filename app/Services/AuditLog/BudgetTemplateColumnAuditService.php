<?php

namespace App\Services\AuditLog;

use App\Models\BudgetTemplate;
use App\Models\BudgetTemplatePreColumn;

class BudgetTemplateColumnAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];
        
        if ($auditData['crudType'] == "C") {
            // For creation, log all the new values
            
            // Get budget template name for budgetTemplateID
            $budgetTemplate = BudgetTemplate::where('budgetTemplateID', $auditData['newValue']['budgetTemplateID'])->first();
            $modifiedData[] = ['amended_field' => "budget_template", 'previous_value' => '', 'new_value' => ($budgetTemplate) ? $budgetTemplate->description : ''];
            
            // Get pre column name for preColumnID
            $preColumn = BudgetTemplatePreColumn::where('preColumnID', $auditData['newValue']['preColumnID'])->first();
            $modifiedData[] = ['amended_field' => "column_name", 'previous_value' => '', 'new_value' => ($preColumn) ? $preColumn->columnName : ''];
            
            $modifiedData[] = ['amended_field' => "is_mandatory", 'previous_value' => '', 'new_value' => ($auditData['newValue']['isMandatory'] == 1) ? 'yes' : 'no'];
            $modifiedData[] = ['amended_field' => "sort_order", 'previous_value' => '', 'new_value' => $auditData['newValue']['sortOrder']];
            
            if (isset($auditData['newValue']['fieldCode']) && !empty($auditData['newValue']['fieldCode'])) {
                $modifiedData[] = ['amended_field' => "field_code", 'previous_value' => '', 'new_value' => $auditData['newValue']['fieldCode']];
            }
            
            if (isset($auditData['newValue']['formulaExpression']) && !empty($auditData['newValue']['formulaExpression'])) {
                $modifiedData[] = ['amended_field' => "formula_expression", 'previous_value' => '', 'new_value' => $auditData['newValue']['formulaExpression']];
            }
            
            if (isset($auditData['newValue']['formulaColumnIDs']) && !empty($auditData['newValue']['formulaColumnIDs'])) {
                $modifiedData[] = ['amended_field' => "formula_column_ids", 'previous_value' => '', 'new_value' => $auditData['newValue']['formulaColumnIDs']];
            }
            
        } elseif ($auditData['crudType'] == "U") {
            // For updates, compare old and new values
            
            if($auditData['previosValue']['budgetTemplateID'] != $auditData['newValue']['budgetTemplateID']) {
                $oldBudgetTemplate = BudgetTemplate::where('budgetTemplateID', $auditData['previosValue']['budgetTemplateID'])->first();
                $newBudgetTemplate = BudgetTemplate::where('budgetTemplateID', $auditData['newValue']['budgetTemplateID'])->first();
                $modifiedData[] = ['amended_field' => "budget_template", 'previous_value' => ($oldBudgetTemplate) ? $oldBudgetTemplate->description : '', 'new_value' => ($newBudgetTemplate) ? $newBudgetTemplate->description : ''];
            }
            
            if($auditData['previosValue']['preColumnID'] != $auditData['newValue']['preColumnID']) {
                $oldPreColumn = BudgetTemplatePreColumn::where('preColumnID', $auditData['previosValue']['preColumnID'])->first();
                $newPreColumn = BudgetTemplatePreColumn::where('preColumnID', $auditData['newValue']['preColumnID'])->first();
                $modifiedData[] = ['amended_field' => "column_name", 'previous_value' => ($oldPreColumn) ? $oldPreColumn->columnName : '', 'new_value' => ($newPreColumn) ? $newPreColumn->columnName : ''];
            }
            
            if($auditData['previosValue']['isMandatory'] != $auditData['newValue']['isMandatory']) {
                $modifiedData[] = ['amended_field' => "is_mandatory", 'previous_value' => ($auditData['previosValue']['isMandatory'] == 1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isMandatory'] == 1) ? 'yes' : 'no'];
            }
            
            if($auditData['previosValue']['sortOrder'] != $auditData['newValue']['sortOrder']) {
                $modifiedData[] = ['amended_field' => "sort_order", 'previous_value' => $auditData['previosValue']['sortOrder'], 'new_value' => $auditData['newValue']['sortOrder']];
            }
            
            if(array_key_exists('fieldCode', $auditData['previosValue']) && array_key_exists('fieldCode', $auditData['newValue']) && $auditData['previosValue']['fieldCode'] != $auditData['newValue']['fieldCode']) {
                $modifiedData[] = ['amended_field' => "field_code", 'previous_value' => $auditData['previosValue']['fieldCode'], 'new_value' => $auditData['newValue']['fieldCode']];
            }
            
            if(array_key_exists('formulaExpression', $auditData['previosValue']) && array_key_exists('formulaExpression', $auditData['newValue']) && $auditData['previosValue']['formulaExpression'] != $auditData['newValue']['formulaExpression']) {
                $modifiedData[] = ['amended_field' => "formula_expression", 'previous_value' => $auditData['previosValue']['formulaExpression'], 'new_value' => $auditData['newValue']['formulaExpression']];
            }
            
            if(array_key_exists('formulaColumnIDs', $auditData['previosValue']) && array_key_exists('formulaColumnIDs', $auditData['newValue']) && $auditData['previosValue']['formulaColumnIDs'] != $auditData['newValue']['formulaColumnIDs']) {
                $modifiedData[] = ['amended_field' => "formula_column_ids", 'previous_value' => $auditData['previosValue']['formulaColumnIDs'], 'new_value' => $auditData['newValue']['formulaColumnIDs']];
            }
            
        } elseif ($auditData['crudType'] == "D") {
            // For deletion, log all the previous values
            
            $budgetTemplate = BudgetTemplate::where('budgetTemplateID', $auditData['previosValue']['budgetTemplateID'])->first();
            $modifiedData[] = ['amended_field' => "budget_template", 'previous_value' => ($budgetTemplate) ? $budgetTemplate->description : '', 'new_value' => ''];
            
            $preColumn = BudgetTemplatePreColumn::where('preColumnID', $auditData['previosValue']['preColumnID'])->first();
            $modifiedData[] = ['amended_field' => "column_name", 'previous_value' => ($preColumn) ? $preColumn->columnName : '', 'new_value' => ''];
            
            $modifiedData[] = ['amended_field' => "is_mandatory", 'previous_value' => ($auditData['previosValue']['isMandatory'] == 1) ? 'yes' : 'no', 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "sort_order", 'previous_value' => $auditData['previosValue']['sortOrder'], 'new_value' => ''];
            
            if (isset($auditData['previosValue']['fieldCode']) && !empty($auditData['previosValue']['fieldCode'])) {
                $modifiedData[] = ['amended_field' => "field_code", 'previous_value' => $auditData['previosValue']['fieldCode'], 'new_value' => ''];
            }
            
            if (isset($auditData['previosValue']['formulaExpression']) && !empty($auditData['previosValue']['formulaExpression'])) {
                $modifiedData[] = ['amended_field' => "formula_expression", 'previous_value' => $auditData['previosValue']['formulaExpression'], 'new_value' => ''];
            }
            
            if (isset($auditData['previosValue']['formulaColumnIDs']) && !empty($auditData['previosValue']['formulaColumnIDs'])) {
                $modifiedData[] = ['amended_field' => "formula_column_ids", 'previous_value' => $auditData['previosValue']['formulaColumnIDs'], 'new_value' => ''];
            }
        }

        return $modifiedData;
    }
} 
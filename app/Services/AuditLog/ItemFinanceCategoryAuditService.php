<?php

namespace App\Services\AuditLog;

use App\Models\ChartOfAccount;

class ItemFinanceCategoryAuditService
{
    public static function process($auditData) 
    {
        $modifiedData = [];
        if ($auditData['crudType'] == "U" || $auditData['crudType'] == "C") {
            $amendedFields = [];

            $masterData = $auditData['newValue'];
            $originalData = $auditData['previosValue'];
            foreach ($masterData as $key => $newValue) {
                $originalValue = $originalData[$key] ?? null;

                if ($newValue != $originalValue) {
                    $amendedFields[$key] = [
                        'old_value' => $originalValue,
                        'new_value' => $newValue,
                    ];
                }
            }

            if (!empty($amendedFields)) {
                unset($amendedFields['financeGLcodebBS'], $amendedFields['financeGLcodePL'], $amendedFields['financeCogsGLcodePL'], $amendedFields['financeGLcodeRevenue']);
                $fieldMappingsGL = [
                    'financeGLcodebBSSystemID' => 'balance_sheet_gl_code',
                    'financeGLcodePLSystemID' => 'consumption_gl_code',
                    'financeGLcodeRevenueSystemID' => 'revenue_gl_code',
                    'financeCogsGLcodePLSystemID' => 'cogs_gl_code',
                ];
                $fieldMappings = [
                    'categoryDescription' => 'category_description',
                    'enableSpecification' => 'enable_specification',
                    'includePLForGRVYN' => 'include_pl_for_grv_yn',
                    'expiryYN' => 'expiry',
                    'isActive' => 'is_active',
                    'attributesYN' => 'attributes',
                    'trackingType' => 'tracking'
                ];
                $fieldMappingsTracking = [
                    0 => 'no_tracking',
                    1 => 'batch/lot_no',
                    2 => 'unique_serial_no',
                ];
                foreach ($amendedFields as $field => $value) {

                    $fieldName = $fieldMappingsGL[$field] ?? $field;
                    $oldValue = $value['old_value'];
                    $newValue = $value['new_value'];
                    if($fieldName != $field){
                        $oldAccount = ChartOfAccount::find($value['old_value']);
                        $newAccount = ChartOfAccount::find($value['new_value']);
                            $oldValue = isset($oldAccount->AccountCode) ? $oldAccount->AccountCode. ' - '. $oldAccount->AccountDescription: '-';
                            $newValue = isset($newAccount->AccountCode) ? $newAccount->AccountCode. ' - '. $newAccount->AccountDescription: '-';
                    } else {
                        $fieldName = $fieldMappings[$field] ?? $field;
                        if ($field == "enableSpecification" || $field == "includePLForGRVYN" || $field == "expiryYN" || $field == "attributesYN" || $field == "isActive") {
                            $newValue = ($value['new_value'] == true || $value['new_value'] == 1) ? 'True' : 'False';
                            $oldValue = is_null($value['old_value'])  ? null : (($value['new_value'] == true || $value['new_value'] == 1) ? 'False' : 'True');
                        } else if ($field == "trackingType") {
                            $newValue = $fieldMappingsTracking[$value['new_value']] ?? $value['new_value'];
                            $oldValue = $fieldMappingsTracking[$value['old_value']] ?? $value['old_value'];
                        }
                    }

                    if($fieldName != $field) {
                        $modifiedData[] = ['amended_field' => $fieldName, 'previous_value' => $oldValue, 'new_value' => $newValue];
                    }
                }
            }
        }

        return $modifiedData;
    }
}

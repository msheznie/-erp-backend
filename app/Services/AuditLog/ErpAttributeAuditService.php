<?php

namespace App\Services\AuditLog;

use App\Models\ErpAttributesFieldType;

class ErpAttributeAuditService
{
    public static function process($auditData) 
    {
        $modifiedData = [];
        if ($auditData['crudType'] == "C") {
            $modifiedData[] = ['amended_field' => "Description", 'previous_value' => '', 'new_value' => $auditData['newValue']['description']];
            $modifiedData[] = ['amended_field' => "Is Mandatory", 'previous_value' => '', 'new_value' => ($auditData['newValue']['is_mendatory']) ? 'True' : 'False'];
            $modifiedData[] = ['amended_field' => "Field Type", 'previous_value' => '', 'new_value' => ErpAttributesFieldType::fieldName($auditData['newValue']['field_type_id'])];
        }
        else if ($auditData['crudType'] == "U") {
            $modifiedData[] = ['amended_field' => "Is Mandatory", 'new_value' => ($auditData['newValue']['is_mendatory']) ? 'True' : 'False', 'previous_value' => ($auditData['previosValue']['is_mendatory']) ? 'True' : 'False'];
        }
        else if ($auditData['crudType'] == "D") {
            $modifiedData[] = ['amended_field' => "Description", 'new_value' => '', 'previous_value' => $auditData['previosValue']['description']];
            $modifiedData[] = ['amended_field' => "Is Mandatory", 'new_value' => '', 'previous_value' => ($auditData['previosValue']['is_mendatory']) ? 'True' : 'False'];
            $modifiedData[] = ['amended_field' => "Field Type", 'new_value' => '', 'previous_value' => ErpAttributesFieldType::fieldName($auditData['previosValue']['field_type_id'])];
        }

        return $modifiedData;
    }
}

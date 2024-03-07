<?php

namespace App\Services\AuditLog;

use App\Models\ErpAttributesFieldType;

class ErpAttributeAuditService
{
    public static function process($auditData) 
    {
        $modifiedData = [];
        if ($auditData['crudType'] == "C") {
            $modifiedData[] = ['amended_field' => "description", 'previous_value' => '', 'new_value' => $auditData['newValue']['description']];
            $modifiedData[] = ['amended_field' => "is_mandatory", 'previous_value' => '', 'new_value' => ($auditData['newValue']['is_mendatory']) ? 'yes' : 'no'];
            $modifiedData[] = ['amended_field' => "field_type", 'previous_value' => '', 'new_value' => ErpAttributesFieldType::fieldName($auditData['newValue']['field_type_id'])];
        }
        else if ($auditData['crudType'] == "U") {
            $modifiedData[] = ['amended_field' => "is_mandatory", 'new_value' => ($auditData['newValue']['is_mendatory']) ? 'yes' : 'no', 'previous_value' => ($auditData['previosValue']['is_mendatory']) ? 'yes' : 'no'];
        }
        else if ($auditData['crudType'] == "D") {
            $modifiedData[] = ['amended_field' => "description", 'new_value' => '', 'previous_value' => $auditData['previosValue']['description']];
            $modifiedData[] = ['amended_field' => "is_mandatory", 'new_value' => '', 'previous_value' => ($auditData['previosValue']['is_mendatory']) ? 'yes' : 'no'];
            $modifiedData[] = ['amended_field' => "field_type", 'new_value' => '', 'previous_value' => ErpAttributesFieldType::fieldName($auditData['previosValue']['field_type_id'])];
        }

        return $modifiedData;
    }
}

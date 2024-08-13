<?php

namespace App\Services\AuditLog;

use App\Models\ErpAttributesFieldType;

class ErpAttributeAuditService
{
    public static function process($auditData) 
    {
        $modifiedData = [];
        if ($auditData['crudType'] == "C") {
            if(isset($auditData['newValue']['attributes_id'])){
                $modifiedData[] = ['amended_field' => "description", 'previous_value' => '', 'new_value' => $auditData['newValue']['description']];
                $modifiedData[] = ['amended_field' => "color", 'previous_value' => '', 'new_value' => $auditData['newValue']['color']];
            } else {
                $modifiedData[] = ['amended_field' => "description", 'previous_value' => '', 'new_value' => $auditData['newValue']['description']];
                $modifiedData[] = ['amended_field' => "is_mandatory", 'previous_value' => '', 'new_value' => ($auditData['newValue']['is_mendatory']) ? 'yes' : 'no'];
                $modifiedData[] = ['amended_field' => "field_type", 'previous_value' => '', 'new_value' => ErpAttributesFieldType::fieldName($auditData['newValue']['field_type_id'])];
            }
            
        }
        else if ($auditData['crudType'] == "U") {

            if( isset($auditData['previosValue']['attributes_id']) && isset($auditData['newValue']['attributes_id'])){

                if($auditData['previosValue']['description'] != $auditData['newValue']['description']) {  
                    $modifiedData[] = ['amended_field' => "description", 'previous_value' => ($auditData['previosValue']['description']) ? $auditData['previosValue']['description']: '', 'new_value' => ($auditData['newValue']['description']) ? $auditData['newValue']['description'] : ''];
                }

                if($auditData['previosValue']['color'] != $auditData['newValue']['color']) {  
                    $modifiedData[] = ['amended_field' => "color", 'previous_value' => ($auditData['previosValue']['color']) ? $auditData['previosValue']['color']: '', 'new_value' => ($auditData['newValue']['color']) ? $auditData['newValue']['color'] : ''];
                }

            } else {
                if($auditData['previosValue']['description'] != $auditData['newValue']['description']) {  
                    $modifiedData[] = ['amended_field' => "description", 'previous_value' => ($auditData['previosValue']['description']) ? $auditData['previosValue']['description']: '', 'new_value' => ($auditData['newValue']['description']) ? $auditData['newValue']['description'] : ''];
                }
                
                if($auditData['previosValue']['field_type_id'] != $auditData['newValue']['field_type_id']) {  
                    $modifiedData[] = ['amended_field' => "field_type", 'previous_value' => ErpAttributesFieldType::fieldName($auditData['previosValue']['field_type_id']), 'new_value' => ErpAttributesFieldType::fieldName($auditData['newValue']['field_type_id'])];
                }
    
                if($auditData['previosValue']['is_mendatory'] != $auditData['newValue']['is_mendatory']) {  
                    $modifiedData[] = ['amended_field' => "is_mandatory", 'previous_value' => ($auditData['previosValue']['is_mendatory'] == 1) ? 'Yes': 'No', 'new_value' => ($auditData['newValue']['is_mendatory'] == 1) ? 'Yes': 'No'];
                }
    
                if($auditData['previosValue']['is_active'] != $auditData['newValue']['is_active']) {  
                    $modifiedData[] = ['amended_field' => "active", 'previous_value' => ($auditData['previosValue']['is_active'] == 1) ? 'Yes': 'No', 'new_value' => ($auditData['newValue']['is_active'] == 1) ? 'Yes': 'No'];
                }
            }
           

        }
        else if ($auditData['crudType'] == "D") {
            if( isset($auditData['previosValue']['attributes_id'])){
                $modifiedData[] = ['amended_field' => "description", 'new_value' => '', 'previous_value' => $auditData['previosValue']['description']];
                $modifiedData[] = ['amended_field' => "color", 'new_value' => '', 'previous_value' => $auditData['previosValue']['color']];
                
            } else {
                $modifiedData[] = ['amended_field' => "description", 'new_value' => '', 'previous_value' => $auditData['previosValue']['description']];
                $modifiedData[] = ['amended_field' => "field_type", 'new_value' => '', 'previous_value' => ErpAttributesFieldType::fieldName($auditData['previosValue']['field_type_id'])];
                $modifiedData[] = ['amended_field' => "is_mandatory", 'new_value' => '', 'previous_value' => ($auditData['previosValue']['is_mendatory']) ? 'yes' : 'no'];
                $modifiedData[] = ['amended_field' => "active", 'new_value' => '', 'previous_value' => ($auditData['previosValue']['is_active']) ? 'yes' : 'no'];

            }
            
        }

        return $modifiedData;
    }
}

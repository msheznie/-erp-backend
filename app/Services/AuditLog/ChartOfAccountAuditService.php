<?php

namespace App\Services\AuditLog;

use App\helper\Helper;
use Illuminate\Support\Arr;
class ChartOfAccountAuditService
{

    public static function process($auditData)
    {



        $modifiedData = [];
        if ($auditData['crudType'] == "U"){

        
                if($auditData['previosValue']['AccountDescription'] != $auditData['newValue']['AccountDescription']) {  
                    $modifiedData[] = ['amended_field' => "account_description", 'previous_value' => ($auditData['previosValue']['AccountDescription']) ? $auditData['previosValue']['AccountDescription']: '', 'new_value' => ($auditData['newValue']['AccountDescription']) ? $auditData['newValue']['AccountDescription'] : ''];
                }

                if($auditData['previosValue']['controllAccountYN'] != $auditData['newValue']['controllAccountYN']) {                
                    $modifiedData[] = ['amended_field' => "control_account_yn", 'previous_value' => ($auditData['previosValue']['controllAccountYN'] == 1) ? 'Yes': 'No', 'new_value' => ($auditData['newValue']['controllAccountYN'] == 1) ? 'Yes': 'No'];
                }

                if($auditData['previosValue']['isBank'] != $auditData['newValue']['isBank']) {                
                    $modifiedData[] = ['amended_field' => "is_bank", 'previous_value' => ($auditData['previosValue']['isBank'] == 1) ? 'Yes': 'No', 'new_value' => ($auditData['newValue']['isBank'] == 1) ? 'Yes': 'No'];
                }
       
                if($auditData['previosValue']['isActive'] != $auditData['newValue']['isActive']) {                
                    $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'Yes': 'No', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'Yes': 'No'];
                }

        }

        return $modifiedData;
    }

    public static function validateFieldsByPolicy($newValue, $previosValue, $policy ,$policyCAc)
    {
        if ($policy) {
            $allowednewValues['AccountDescription'] = $newValue['AccountDescription'];
            $allowedpreviousValues['AccountDescription'] = $previosValue['AccountDescription'];
        }

        if ($policyCAc) {
            $allowednewValues['controllAccountYN'] = $newValue['controllAccountYN'];
            $allowedpreviousValues['controllAccountYN'] = $previosValue['controllAccountYN'];

            $allowednewValues['isBank'] = $newValue['isBank'];
            $allowedpreviousValues['isBank'] = $previosValue['isBank'];
        }

        if ($policy || $policyCAc) {
            $allowednewValues['isActive'] = $newValue['isActive'];
            $allowedpreviousValues['isActive'] = $previosValue['isActive'];
        }

        $data = ['allowednewValues'=>$allowednewValues,'allowedpreviousValues'=>$allowedpreviousValues];

        return $data;
    }
}

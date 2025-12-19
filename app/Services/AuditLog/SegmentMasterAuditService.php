<?php

namespace App\Services\AuditLog;

use App\Models\Company;

class SegmentMasterAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];
        if ($auditData['crudType'] == "U"){
            if($auditData['previosValue']['ServiceLineCode'] != $auditData['newValue']['ServiceLineCode']) {
                $modifiedData[] = ['amended_field' => "segment_code", 'previous_value' => $auditData['previosValue']['ServiceLineCode'], 'new_value' => $auditData['newValue']['ServiceLineCode']];
            }
            if($auditData['previosValue']['ServiceLineDes'] != $auditData['newValue']['ServiceLineDes']) {
                $modifiedData[] = ['amended_field' => "description", 'previous_value' => $auditData['previosValue']['ServiceLineDes'], 'new_value' => $auditData['newValue']['ServiceLineDes']];
            }
            if($auditData['previosValue']['isActive'] != $auditData['newValue']['isActive']) {
                $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive']) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isActive']) ? 'yes' : 'no'];
            }
            if($auditData['previosValue']['isMaster'] != $auditData['newValue']['isMaster']) {
                $modifiedData[] = ['amended_field' => "is_master", 'previous_value' => ($auditData['previosValue']['isMaster']) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isMaster']) ? 'yes' : 'no'];
            }
            if($auditData['previosValue']['isFinalLevel'] != $auditData['newValue']['isFinalLevel']) {
                $modifiedData[] = ['amended_field' => "is_final", 'previous_value' => ($auditData['previosValue']['isFinalLevel']) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isFinalLevel']) ? 'yes' : 'no'];
            }
            if($auditData['previosValue']['isPublic'] != $auditData['newValue']['isPublic']) {
                $modifiedData[] = ['amended_field' => "is_public", 'previous_value' => ($auditData['previosValue']['isPublic']) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isPublic']) ? 'yes' : 'no'];
            }
        } else if ($auditData['crudType'] == "D") {
            $modifiedData[] = ['amended_field' => "segment_code", 'previous_value' => $auditData['previosValue']['ServiceLineCode'], 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "description", 'previous_value' => $auditData['previosValue']['ServiceLineDes'], 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive']) ? 'yes' : 'no', 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "is_master", 'previous_value' =>  ($auditData['previosValue']['isMaster']) ? 'yes' : 'no', 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "is_final", 'previous_value' =>  ($auditData['previosValue']['isFinalLevel']) ? 'yes' : 'no', 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "is_public", 'previous_value' =>  ($auditData['previosValue']['isPublic']) ? 'yes' : 'no', 'new_value' => ''];
    }

        return $modifiedData;
    }
}

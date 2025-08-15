<?php

namespace App\Services\AuditLog;

use App\Models\Company;
use App\Models\WorkflowConfigurationHodAction;
use App\Models\HodAction;
use App\Models\WorkflowConfiguration;

class WorkflowConfigurationHodActionAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];
        
        if ($auditData['crudType'] == "C") {
            // For creation, log all the new values
            $hodAction = HodAction::find($auditData['newValue']['hodActionID']);
            $modifiedData[] = ['amended_field' => "hod_action", 'previous_value' => '', 'new_value' => ($hodAction) ? $hodAction->description : ''];

            $modifiedData[] = ['amended_field' => "parent", 'previous_value' => '', 'new_value' => $auditData['newValue']['parent'] ? 'Yes' : 'No'];
            $modifiedData[] = ['amended_field' => "child", 'previous_value' => '', 'new_value' => $auditData['newValue']['child'] ? 'Yes' : 'No'];
            
        } else if ($auditData['crudType'] == "U") {
            // For updates, compare old and new values
            if($auditData['previosValue']['hodActionID'] != $auditData['newValue']['hodActionID']) {
                $oldHodAction = HodAction::find($auditData['previosValue']['hodActionID']);
                $newHodAction = HodAction::find($auditData['newValue']['hodActionID']);
                $modifiedData[] = ['amended_field' => "hod_action", 'previous_value' => ($oldHodAction) ? $oldHodAction->description : '', 'new_value' => ($newHodAction) ? $newHodAction->description : ''];
            }
            
            if($auditData['previosValue']['parent'] != $auditData['newValue']['parent']) {
                $modifiedData[] = ['amended_field' => "parent", 'previous_value' => $auditData['previosValue']['parent'] ? 'Yes' : 'No', 'new_value' => $auditData['newValue']['parent'] ? 'Yes' : 'No'];
            }
            
            if($auditData['previosValue']['child'] != $auditData['newValue']['child']) {
                $modifiedData[] = ['amended_field' => "child", 'previous_value' => $auditData['previosValue']['child'] ? 'Yes' : 'No', 'new_value' => $auditData['newValue']['child'] ? 'Yes' : 'No'];
            }
            
        } else if ($auditData['crudType'] == "D") {
            // For deletion, log all the previous values
            $hodAction = HodAction::find($auditData['previosValue']['hodActionID']);
            $modifiedData[] = ['amended_field' => "hod_action", 'previous_value' => ($hodAction) ? $hodAction->description : '', 'new_value' => ''];
            
            $modifiedData[] = ['amended_field' => "parent", 'previous_value' => $auditData['previosValue']['parent'] ? 'Yes' : 'No', 'new_value' => ''];
            $modifiedData[] = ['amended_field' => "child", 'previous_value' => $auditData['previosValue']['child'] ? 'Yes' : 'No', 'new_value' => ''];
        }

        return $modifiedData;
    }
} 
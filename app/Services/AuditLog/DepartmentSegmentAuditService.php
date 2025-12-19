<?php

namespace App\Services\AuditLog;

use App\Models\CompanyDepartment;
use App\Models\SegmentMaster;

class DepartmentSegmentAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];
        
        if ($auditData['crudType'] == "C") {
            // For creation, log all the new values
            
            // Get department name for departmentSystemID
            $department = CompanyDepartment::where('departmentSystemID', $auditData['newValue']['departmentSystemID'])->first();
            $modifiedData[] = ['amended_field' => "department", 'previous_value' => '', 'new_value' => ($department) ? $department->departmentDescription : ''];
            
            // Get segment name for serviceLineSystemID
            $segment = SegmentMaster::where('serviceLineSystemID', $auditData['newValue']['serviceLineSystemID'])->first();
            $modifiedData[] = ['amended_field' => "segment", 'previous_value' => '', 'new_value' => ($segment) ? $segment->ServiceLineDes : ''];
            
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => '', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            
        } else if ($auditData['crudType'] == "U") {
            // For updates, compare old and new values
            
            if($auditData['previosValue']['departmentSystemID'] != $auditData['newValue']['departmentSystemID']) {
                $oldDepartment = CompanyDepartment::where('departmentSystemID', $auditData['previosValue']['departmentSystemID'])->first();
                $newDepartment = CompanyDepartment::where('departmentSystemID', $auditData['newValue']['departmentSystemID'])->first();
                $modifiedData[] = ['amended_field' => "department", 'previous_value' => ($oldDepartment) ? $oldDepartment->departmentDescription : '', 'new_value' => ($newDepartment) ? $newDepartment->departmentDescription : ''];
            }
            
            if($auditData['previosValue']['serviceLineSystemID'] != $auditData['newValue']['serviceLineSystemID']) {
                $oldSegment = SegmentMaster::where('serviceLineSystemID', $auditData['previosValue']['serviceLineSystemID'])->first();
                $newSegment = SegmentMaster::where('serviceLineSystemID', $auditData['newValue']['serviceLineSystemID'])->first();
                $modifiedData[] = ['amended_field' => "segment", 'previous_value' => ($oldSegment) ? $oldSegment->ServiceLineDes : '', 'new_value' => ($newSegment) ? $newSegment->ServiceLineDes : ''];
            }
            
            if($auditData['previosValue']['isActive'] != $auditData['newValue']['isActive']) {
                $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            }
            
        } else if ($auditData['crudType'] == "D") {
            // For deletion, log all the previous values
            
            $department = CompanyDepartment::where('departmentSystemID', $auditData['previosValue']['departmentSystemID'])->first();
            $modifiedData[] = ['amended_field' => "department", 'previous_value' => ($department) ? $department->departmentDescription : '', 'new_value' => ''];
            
            $segment = SegmentMaster::where('serviceLineSystemID', $auditData['previosValue']['serviceLineSystemID'])->first();
            $modifiedData[] = ['amended_field' => "segment", 'previous_value' => ($segment) ? $segment->ServiceLineDes : '', 'new_value' => ''];
            
            $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive'] == 1) ? 'yes' : 'no', 'new_value' => ''];
        }

        return $modifiedData;
    }
} 
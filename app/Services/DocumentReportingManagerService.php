<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\HrmsEmployeeManager;

class DocumentReportingManagerService
{
    public static function getReportingManagerDocumentApprovedData($empInfo, $approvalRole, $params, $sorceDocument, $docInforArr, $email_in = null)
    {
        $reportingManagerForApproval = HrmsEmployeeManager::where('empID', $empInfo->employeeSystemID)->where('active', 1)->first();
        
        if (!$reportingManagerForApproval) {
            return ['success' => false, 'message' => trans('custom.reporting_manager_not_assigned_for_selected_employee')];
        }

        $reportingManagerID = $reportingManagerForApproval->managerID;
        $manager_details = Employee::where('employeeSystemID', $reportingManagerID)->first();
        
        if (!$manager_details) {
            return ['success' => false, 'message' => trans('custom.reporting_manager_not_assigned_for_selected_employee')];
        }

        $documentApprovedData = array(
            'companySystemID' => $approvalRole->companySystemID,
            'companyID' => $approvalRole->companyID,
            'departmentSystemID' => $approvalRole->departmentSystemID,
            'departmentID' => $approvalRole->departmentID,
            'serviceLineSystemID' => $approvalRole->serviceLineSystemID,
            'serviceLineCode' => $approvalRole->serviceLineID,
            'documentSystemID' => $params['document'],
            'documentID' => $approvalRole->documentID,
            'documentSystemCode' => $params["autoID"],
            'documentCode' => $sorceDocument[$docInforArr["documentCodeColumnName"]],
            'approvalLevelID' => $approvalRole->approvalLevelID,
            'rollID' => $approvalRole->rollMasterID,
            'approvalGroupID' => $approvalRole->approvalGroupID,
            'rollLevelOrder' => $approvalRole->rollLevel,
            'docConfirmedDate' => now(),
            'docConfirmedByEmpSystemID' => $manager_details->employeeSystemID,
            'docConfirmedByEmpID' => $manager_details->empID,
            'timeStamp' => NOW(),
            'reference_email' => $email_in
        );

        return ['success' => true, 'data' => $documentApprovedData];
    }
}

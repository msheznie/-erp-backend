<?php

namespace App\Services;

use App\Models\Company;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\HrmsEmployeeManager;

class DocumentAutoApproveService
{
	public static function setDocumentApprovedData($params, $sorceDocument, $docInforArr, $empInfo)
	{
        $documentApprovedAuto = [];

        $companyData = Company::find($params['company']);

        $documentMaster = DocumentMaster::find($params['document']);

        $departmentSystemID = $documentMaster ? $documentMaster->departmentSystemID : null;
        $departmentID = $documentMaster ? $documentMaster->departmentID : null;
        $documentID = $documentMaster ? $documentMaster->documentID : null;

        if (isset($params['email'])) {
            $email_in = $params['email'];
        } else {
            $email_in = null;
        }

        $documentApprovedAuto[] = array('companySystemID' => $params['company'], 'companyID' => ($companyData ? $companyData->CompanyID : ""), 'departmentSystemID' => $departmentSystemID, 'departmentID' => $departmentID, 'serviceLineSystemID' => null, 'serviceLineCode' => null, 'documentSystemID' => $params['document'], 'documentID' => $documentID, 'documentSystemCode' => $params["autoID"], 'documentCode' => $sorceDocument[$docInforArr["documentCodeColumnName"]], 'approvalLevelID' => null, 'rollID' => null, 'approvalGroupID' => null, 'rollLevelOrder' => 1, 'docConfirmedDate' => now(), 'docConfirmedByEmpSystemID' => $empInfo->employeeSystemID, 'docConfirmedByEmpID' => $empInfo->empID, 'timeStamp' => NOW(), 'reference_email' => $email_in, 'isApprovedFromPC' => 1);

        return $documentApprovedAuto;
	}

    public static function getAutoApproveParams($documentSystemID, $documentSystemCode)
    {
        $data['isAutoCreateDocument'] = true;
        $data['rollLevelOrder'] = 1;
        $data['approvedComments'] = 'Approved by system';
        $data['documentSystemCode'] = $documentSystemCode;
        $data['documentSystemID'] = $documentSystemID;

        $documentApprovedData = DocumentApproved::where('documentSystemCode', $documentSystemCode)
                                                ->where('documentSystemID', $documentSystemID)
                                                ->where('isApprovedFromPC', 1)
                                                ->first();

        $data['documentApprovedID'] = $documentApprovedData ? $documentApprovedData->documentApprovedID : null;
        $data['companySystemID'] = $documentApprovedData ? $documentApprovedData->companySystemID : null;

        return $data;
    }

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


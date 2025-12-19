<?php

namespace App\Services;

use App\Models\Company;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;

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
}


<?php

namespace App\helper;
use App\Models\DocumentApproved;
use App\Models\EmployeesDepartment;
use App\Models\CompanyPolicyMaster;
use App\Models\CompanyDocumentAttachment;
use App\Models\ApprovalLevel;
use App\Models\DocumentMaster;
use App\helper\Helper;
use App\helper\email;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReopenDocument
{
	 public static function reopenDocument($input)
    {
    	$docInforArr = self::setDocumentArray($input);
        if (empty($docInforArr)) {
            return ['success' => false, 'message' => 'Document ID not found'];
        }


        DB::beginTransaction();
        try {
            // get current employee detail
            $employeeSystemID = Helper::getEmployeeSystemID();

            // Model name
            $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; 
            $sourceModel = $namespacedModel::find($input["documentAutoID"]);

            $emails = array();
            if (empty($sourceModel)) {
                return ['success' => false, 'message' => "Document not found"];
            }

            if ($sourceModel['RollLevForApp_curr'] > 1) {
                return ['success' => false, 'message' => "You cannot reopen this document, it is already partially approved"];
            }

            if ($sourceModel[$docInforArr['approvedColumnName']] == -1) {
                return ['success' => false, 'message' => "You cannot reopen this document it is already fully approved"];
            }

            if ($sourceModel[$docInforArr['confirmColumnName']] == 0) {
                return ['success' => false, 'message' => "You cannot reopen this document, it is not confirmed"];
            }

            // updating fields
            $sourceModel[$docInforArr['confirmColumnName']] = 0;
            $sourceModel[$docInforArr['confirmedBySystemID']] = null;
            $sourceModel[$docInforArr['confirmedByID']] = null;
            $sourceModel[$docInforArr['confirmedDate']] = null;
            $sourceModel[$docInforArr['confirmedByName']] = null;
            $sourceModel->RollLevForApp_curr = 1;
            $sourceModel->save();

            $document = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();

            if($input["documentSystemID"] == 56 )
            {
                $cancelDocNameBody = $document->documentDescription . ' <b>' . $sourceModel->supplierName . '</b>';
                $cancelDocNameSubject = $document->documentDescription . ' ' . $sourceModel->supplierName;
            }
            else if($input["documentSystemID"] == 58 )
            {
                $cancelDocNameBody = $document->documentDescription . ' <b>' . $sourceModel->CustomerName . '</b>';
                $cancelDocNameSubject = $document->documentDescription . ' ' . $sourceModel->CustomerName;
            }
            else
            {
                $cancelDocNameBody = $document->documentDescription . ' <b>' . $sourceModel[$docInforArr['documentCodeColumnName']] . '</b>';
                $cancelDocNameSubject = $document->documentDescription . ' ' . $sourceModel[$docInforArr['documentCodeColumnName']];
            }




            $subject = $cancelDocNameSubject . ' is reopened';

           
            $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . Helper::getEmployeeCode($employeeSystemID) . ' - ' . Helper::getEmployeeName() . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';


            $documentApproval = DocumentApproved::levelWiseDocumentApprover($input['documentSystemID'], $sourceModel[$docInforArr['primarykey']], 1, $sourceModel[$docInforArr['companyColumnName']]);

            if ($documentApproval) {
                if ($documentApproval->approvedYN == 0) {
                    $isServiceLineApproval = null;
                    if (isset($docInforArr['companyColumnName']) && !is_null($docInforArr['companyColumnName'])) {
                        $companyDocument = CompanyDocumentAttachment::companyDocumentAttachemnt($sourceModel[$docInforArr['companyColumnName']], $input['documentSystemID']);
                        if (empty($companyDocument)) {
                            return ['success' => false, 'message' => 'Policy not found for this document'];
                        }

                        $isServiceLineApproval = $companyDocument['isServiceLineApproval'];
                    }

                    $approvalList = self::userApprovalAccess($documentApproval->approvalGroupID, $documentApproval->companySystemID, null, $documentApproval->documentSystemID, $documentApproval->serviceLineSystemID, $isServiceLineApproval, "get");

                    foreach ($approvalList as $da) {
                        if ($da->employee) {
                            $emails[] = array('empSystemID' => $da->employee->employeeSystemID,
                                'companySystemID' => $documentApproval->companySystemID,
                                'docSystemID' => $documentApproval->documentSystemID,
                                'alertMessage' => $subject,
                                'emailAlertMessage' => $body,
                                'docSystemCode' => $documentApproval->documentSystemCode);
                        }
                    }

                    $sendEmail = email::sendEmail($emails);
                    if (!$sendEmail["success"]) {
                        return ['success' => false, 'message' => $sendEmail["message"]];
                    }
                }
            }

            $deleteApproval = DocumentApproved::deleteApproval($sourceModel[$docInforArr['primarykey']], $sourceModel[$docInforArr['companyColumnName']], $input['documentSystemID']);
            

            DB::commit();
            return ['success' => true, 'message' => 'Document reopened successfully'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => 'Error Occurred'];
            // return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public static function setDocumentArray($input)
    {
    	$docInforArr = array('documentCodeColumnName' => '', 'confirmColumnName' => '', 'confirmedBy' => '', 'confirmedBySystemID' => '', 'confirmedDate' => '', 'tableName' => '', 'modelName' => '', 'primarykey' => '');
        switch ($input["documentSystemID"]) { // check the document id and set relavant parameters
            case 56:
                $docInforArr["documentCodeColumnName"] = 'primarySupplierCode';
                $docInforArr["confirmColumnName"] = 'supplierConfirmedYN';
                $docInforArr["confirmedBySystemID"] = 'supplierConfirmedEmpSystemID';
                $docInforArr["confirmedByID"] = 'supplierConfirmedEmpID';
                $docInforArr["confirmedDate"] = 'supplierConfirmedDate';
                $docInforArr["tableName"] = 'suppliermaster';
                $docInforArr["modelName"] = 'SupplierMaster';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["primarykey"] = 'supplierCodeSystem';
                $docInforArr["companyColumnName"] = 'primaryCompanySystemID';
                $docInforArr["confirmedByName"] = 'supplierConfirmedEmpName';
                break;
            case 57:
                $docInforArr["documentCodeColumnName"] = 'primaryCode';
                $docInforArr["confirmColumnName"] = 'itemConfirmedYN';
                $docInforArr["confirmedBySystemID"] = 'itemConfirmedByEMPSystemID';
                $docInforArr["confirmedByID"] = 'itemConfirmedByEMPID';
                $docInforArr["confirmedDate"] = 'itemConfirmedDate';
                $docInforArr["tableName"] = 'itemmaster';
                $docInforArr["modelName"] = 'ItemMaster';
                $docInforArr["approvedColumnName"] = 'itemApprovedYN';
                $docInforArr["primarykey"] = 'itemCodeSystem';
                $docInforArr["companyColumnName"] = 'primaryCompanySystemID';
                $docInforArr["confirmedByName"] = 'itemConfirmedByEMPName';
                break;
            case 58:
                $docInforArr["documentCodeColumnName"] = 'CutomerCode';
                $docInforArr["confirmColumnName"] = 'confirmedYN';
                $docInforArr["confirmedBySystemID"] = 'confirmedEmpSystemID';
                $docInforArr["confirmedByID"] = 'confirmedEmpID';
                $docInforArr["confirmedByName"] = 'confirmedEmpName';
                $docInforArr["confirmedDate"] = 'confirmedDate';
                $docInforArr["tableName"] = 'customermaster';
                $docInforArr["modelName"] = 'CustomerMaster';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["primarykey"] = 'customerCodeSystem';
                $docInforArr["companyColumnName"] = 'primaryCompanySystemID';
                break;
            case 59:
                $docInforArr["documentCodeColumnName"] = 'AccountCode';
                $docInforArr["confirmColumnName"] = 'confirmedYN';
                $docInforArr["confirmedBySystemID"] = 'confirmedEmpSystemID';
                $docInforArr["confirmedByID"] = 'confirmedEmpID';
                $docInforArr["confirmedByName"] = 'confirmedEmpName';
                $docInforArr["confirmedDate"] = 'confirmedEmpDate';
                $docInforArr["tableName"] = 'chartofaccounts';
                $docInforArr["modelName"] = 'ChartOfAccount';
                $docInforArr["approvedColumnName"] = 'isApproved';
                $docInforArr["primarykey"] = 'chartOfAccountSystemID';
                $docInforArr["companyColumnName"] = 'primaryCompanySystemID';
                break;
            case 86:
                $docInforArr["documentCodeColumnName"] = 'supplierName';
                $docInforArr["confirmColumnName"] = 'supplierConfirmedYN';
                $docInforArr["confirmedBySystemID"] = 'supplierConfirmedEmpSystemID';
                $docInforArr["confirmedByID"] = 'supplierConfirmedEmpID';
                $docInforArr["confirmedDate"] = 'supplierConfirmedDate';
                $docInforArr["tableName"] = 'registeredsupplier';
                $docInforArr["modelName"] = 'RegisteredSupplier';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["primarykey"] = 'id';
                $docInforArr["companyColumnName"] = 'companySystemID';
                $docInforArr["confirmedByName"] = 'supplierConfirmedEmpName';
                break;
            case 96:
                $docInforArr["documentCodeColumnName"] = 'conversionCode';
                $docInforArr["confirmColumnName"] = 'confirmedYN';
                $docInforArr["confirmedBySystemID"] = 'ConfirmedBySystemID';
                $docInforArr["confirmedByID"] = 'ConfirmedBy';
                $docInforArr["confirmedDate"] = 'confirmedDate';
                $docInforArr["tableName"] = 'currency_conversion_master';
                $docInforArr["modelName"] = 'CurrencyConversionMaster';
                $docInforArr["approvedColumnName"] = 'approvedYN';
                $docInforArr["primarykey"] = 'id';
                $docInforArr["companyColumnName"] = null;
                $docInforArr["confirmedByName"] = 'confirmedEmpName';
                break;
            case 132:
                $docInforArr["documentCodeColumnName"] = 'ServiceLineCode';
                $docInforArr["confirmColumnName"] = 'confirmed_yn';
                $docInforArr["confirmedBySystemID"] = 'confirmed_by_emp_system_id';
                $docInforArr["confirmedByID"] = 'confirmed_by_emp_id';
                $docInforArr["confirmedDate"] = 'confirmed_date';
                $docInforArr["tableName"] = 'serviceline';
                $docInforArr["modelName"] = 'SegmentMaster';
                $docInforArr["approvedColumnName"] = 'approved_yn';
                $docInforArr["primarykey"] = 'serviceLineSystemID';
                $docInforArr["companyColumnName"] = 'companySystemID';
                $docInforArr["confirmedByName"] = 'confirmed_by_name';
                break;
            default:
                return [];
        }

        return $docInforArr;
    }


     public static function userApprovalAccess($approvalGroupID, $companySystemID, $employeeSystemID, $documentSystemID, $serviceLineSystemID, $isServiceLineApproval, $type)
    {
        $checkUserHasApprovalAccess = EmployeesDepartment::where('employeeGroupID', $approvalGroupID)
                                                            ->where('companySystemID', $companySystemID)
                                                            ->where('documentSystemID', $documentSystemID)
                                                            ->where('isActive', 1)
                                                            ->where('removedYN', 0);

        if ($isServiceLineApproval == -1) {
            $checkUserHasApprovalAccess = $checkUserHasApprovalAccess->where('ServiceLineSystemID', $serviceLineSystemID);
        }

        $checkUserHasApprovalAccess = $checkUserHasApprovalAccess->whereHas('employee', function($q) {
                                                                    $q->where('discharegedYN',0);
                                                                });

        if ($type == "check") {
            $checkUserHasApprovalAccess = $checkUserHasApprovalAccess->where('employeeSystemID', $employeeSystemID)
                                                                     ->groupBy('employeeSystemID')
                                                                     ->exists();

            if (!$checkUserHasApprovalAccess) {
                return false;
            } 
            return true;
        } else {
            return $checkUserHasApprovalAccess->with(['employee'])
                                              ->groupBy('employeeSystemID')
                                              ->get();
        }
    }
}

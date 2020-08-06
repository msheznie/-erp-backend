<?php

namespace App\Traits;
use App\helper\Helper;
use App\Models\AuditTrail;
use Carbon\Carbon;

trait AuditTrial
{

    /**
     * @param int $documentSystemID
     * @param int $documentSystemCode
     * @param int $rollLevelOrder
     * @param string $companySystemID
     * @return array
     */
    public static function createAuditTrial($documentSystemID, $documentSystemCode, $comment, $process = 'returned back')
    {
        $docInforArr = array('modelName' => '', 'primarykey' => '', 'documentCodeColumnName' =>'','companySystemID' => '', 'companyID' => '', 'serviceLineSystemID' =>'','serviceLineCode' => '', 'documentID' => '', 'documentSystemCode' =>'' );

        switch ($documentSystemID) {
            case 3: //GRV
                $docInforArr["modelName"] = 'GRVMaster';
                $docInforArr["primarykey"] = 'grvAutoID';
                $docInforArr["documentCodeColumnName"] = 'grvPrimaryCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLineCode';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 1:
            case 50:
            case 51:
                $docInforArr["modelName"] = 'PurchaseRequest';
                $docInforArr["primarykey"] = 'purchaseRequestID';
                $docInforArr["documentCodeColumnName"] = 'purchaseRequestCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLineCode';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 2:
            case 5:
            case 52:
                $docInforArr["modelName"] = 'ProcumentOrder';
                $docInforArr["primarykey"] = 'purchaseOrderID';
                $docInforArr["documentCodeColumnName"] = 'purchaseOrderCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLine';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 11: // supplier invoice
                $docInforArr["modelName"] = 'BookInvSuppMaster';
                $docInforArr["primarykey"] = 'bookingSuppMasInvAutoID';
                $docInforArr["documentCodeColumnName"] = 'bookingInvCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 15: // debit note
                $docInforArr["modelName"] = 'DebitNote';
                $docInforArr["primarykey"] = 'debitNoteAutoID';
                $docInforArr["documentCodeColumnName"] = 'debitNoteCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;
            case 19: // credit note
                $docInforArr["modelName"] = 'CreditNote';
                $docInforArr["primarykey"] = 'creditNoteAutoID';
                $docInforArr["documentCodeColumnName"] = 'creditNoteCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemiD';
                $docInforArr["documentID"] = 'documentID';
                break;
            default:
                return ['success' => false, 'message' => 'Document ID not found'];
        }

        $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
        $masterRec = $namespacedModel::find($documentSystemCode);

        if(!empty($masterRec)){
            $employee = Helper::getEmployeeInfo();
            $description = $masterRec[$docInforArr["documentID"]]." ".$masterRec[$docInforArr["documentCodeColumnName"]]." is ".$process." by ".$employee->empName;
            if($comment != ''){
                $description .= " due to below reason. ".$comment;
            }
            $insertArray = [
                'companySystemID' => $masterRec[$docInforArr["companySystemID"]],
                'companyID' => $masterRec[$docInforArr["companyID"]],
                'serviceLineSystemID' => isset($masterRec[$docInforArr["serviceLineSystemID"]])?$masterRec[$docInforArr["serviceLineSystemID"]]:null,
                'serviceLineCode' => isset($masterRec[$docInforArr["serviceLineCode"]])?$masterRec[$docInforArr["serviceLineCode"]]:null,
                'documentSystemID' => $masterRec[$docInforArr["documentSystemID"]],
                'documentID' => $masterRec[$docInforArr["documentID"]],
                'documentSystemCode' => $masterRec[$docInforArr["primarykey"]],
                'valueFrom' => 0,
                'valueTo' => 0,
                'valueFromSystemID' => null,
                'valueFromText' => null,
                'valueToSystemID' => null,
                'valueToText' => null,
                'description' => $description,
                'modifiedUserSystemID' => $employee->employeeSystemID,
                'modifiedUserID' => $employee->empID,
                'modifiedDate' => Carbon::now()
            ];
            AuditTrail::create($insertArray);
        }


    }

}

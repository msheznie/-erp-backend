<?php

namespace App\Traits;
use App\helper\Helper;
use App\Models\AuditTrail;
use App\Services\UserTypeService;
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
    public static function createAuditTrial($documentSystemID, $documentSystemCode, $comment, $process = '', $oldValue = null, $isFromAPI = false)
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
            case 9: //MR
                $docInforArr["modelName"] = 'MaterielRequest';
                $docInforArr["primarykey"] = 'RequestID';
                $docInforArr["documentCodeColumnName"] = 'RequestCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLineCode';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;
            case 8: //MI
                $docInforArr["modelName"] = 'ItemIssueMaster';
                $docInforArr["primarykey"] = 'itemIssueAutoID';
                $docInforArr["documentCodeColumnName"] = 'itemIssueCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLineCode';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;
            case 12: //SR
                $docInforArr["modelName"] = 'ItemReturnMaster';
                $docInforArr["primarykey"] = 'itemReturnAutoID';
                $docInforArr["documentCodeColumnName"] = 'itemReturnCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLineCode';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 13: //ST
                $docInforArr["modelName"] = 'StockTransfer';
                $docInforArr["primarykey"] = 'stockTransferAutoID';
                $docInforArr["documentCodeColumnName"] = 'stockTransferCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLineCode';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 10: //RS
                $docInforArr["modelName"] = 'StockReceive';
                $docInforArr["primarykey"] = 'stockReceiveAutoID';
                $docInforArr["documentCodeColumnName"] = 'stockReceiveCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLineCode';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 7: //SA
                $docInforArr["modelName"] = 'StockAdjustment';
                $docInforArr["primarykey"] = 'stockAdjustmentAutoID';
                $docInforArr["documentCodeColumnName"] = 'stockAdjustmentCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLineCode';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 24: //PRN
                $docInforArr["modelName"] = 'PurchaseReturn';
                $docInforArr["primarykey"] = 'purhaseReturnAutoID';
                $docInforArr["documentCodeColumnName"] = 'purchaseReturnCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLineCode';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 61: //INRC
                $docInforArr["modelName"] = 'InventoryReclassification';
                $docInforArr["primarykey"] = 'inventoryreclassificationID';
                $docInforArr["documentCodeColumnName"] = 'documentCode';
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

            case 4: // PaySupplierInvoiceMaster
                $docInforArr["modelName"] = 'PaySupplierInvoiceMaster';
                $docInforArr["primarykey"] = 'PayMasterAutoId';
                $docInforArr["documentCodeColumnName"] = 'BPVcode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 6: //Expense Claim
                $docInforArr["modelName"] = 'ExpenseClaim';
                $docInforArr["primarykey"] = 'expenseClaimMasterAutoID';
                $docInforArr["documentCodeColumnName"] = 'expenseClaimCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                $docInforArr["serviceLineSystemID"] = 'departmentSystemID';
                $docInforArr["serviceLineCode"] = 'departmentID';
                break;

            case 28: //Monthly Addition
                $docInforArr["modelName"] = 'MonthlyAdditionsMaster';
                $docInforArr["primarykey"] = 'monthlyAdditionsMasterID';
                $docInforArr["documentCodeColumnName"] = 'monthlyAdditionsCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'CompanyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 20: // CI
                $docInforArr["modelName"] = 'CustomerInvoiceDirect';
                $docInforArr["primarykey"] = 'custInvoiceDirectAutoID';
                $docInforArr["documentCodeColumnName"] = 'bookingInvCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemiD';
                $docInforArr["documentID"] = 'documentID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLineCode';
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

            case 67:
            case 68: // Sales / quotation
                $docInforArr["modelName"] = 'QuotationMaster';
                $docInforArr["primarykey"] = 'quotationMasterID';
                $docInforArr["documentCodeColumnName"] = 'quotationCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                break;
            case 71: // DEO
                $docInforArr["modelName"] = 'DeliveryOrder';
                $docInforArr["primarykey"] = 'deliveryOrderID';
                $docInforArr["documentCodeColumnName"] = 'deliveryOrderCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLineCode';
                break;

            case 17: // JV
                $docInforArr["modelName"] = 'JvMaster';
                $docInforArr["primarykey"] = 'jvMasterAutoId';
                $docInforArr["documentCodeColumnName"] = 'JVcode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 46: // Budget Transfer Note - BTN
                $docInforArr["modelName"] = 'BudgetTransferForm';
                $docInforArr["primarykey"] = 'budgetTransferFormAutoID';
                $docInforArr["documentCodeColumnName"] = 'transferVoucherNo';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 65: // Budget
                $docInforArr["modelName"] = 'BudgetMaster';
                $docInforArr["primarykey"] = 'budgetmasterID';
                $docInforArr["documentCodeColumnName"] = 'budgetmasterID';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLineCode';
                break;

            case 62: // BRC
                $docInforArr["modelName"] = 'BankReconciliation';
                $docInforArr["primarykey"] = 'bankRecAutoID';
                $docInforArr["documentCodeColumnName"] = 'bankRecPrimaryCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 64: // PBT
                $docInforArr["modelName"] = 'PaymentBankTransfer';
                $docInforArr["primarykey"] = 'paymentBankTransferID';
                $docInforArr["documentCodeColumnName"] = 'bankTransferDocumentCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 22: // FA
                $docInforArr["modelName"] = 'FixedAssetMaster';
                $docInforArr["primarykey"] = 'faID';
                $docInforArr["documentCodeColumnName"] = 'faCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLineCode';
                break;

            case 23: // FAD
                $docInforArr["modelName"] = 'FixedAssetDepreciationMaster';
                $docInforArr["primarykey"] = 'depMasterAutoID';
                $docInforArr["documentCodeColumnName"] = 'depCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 41: // FADS
                $docInforArr["modelName"] = 'AssetDisposalMaster';
                $docInforArr["primarykey"] = 'assetdisposalMasterAutoID';
                $docInforArr["documentCodeColumnName"] = 'disposalDocumentCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 63: // ACA
                $docInforArr["modelName"] = 'AssetCapitalization';
                $docInforArr["primarykey"] = 'capitalizationID';
                $docInforArr["documentCodeColumnName"] = 'capitalizationCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 87: // SLR
                $docInforArr["modelName"] = 'SalesReturn';
                $docInforArr["primarykey"] = 'id';
                $docInforArr["documentCodeColumnName"] = 'salesReturnCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                $docInforArr["serviceLineCode"] = 'serviceLineCode';
                break;
                case 103: // Asset Transfer
                    $docInforArr["modelName"] = 'ERPAssetTransfer';
                    $docInforArr["primarykey"] = 'id';
                    $docInforArr["documentCodeColumnName"] = 'document_code';
                    $docInforArr["companySystemID"] = 'company_id';
                    $docInforArr["companyID"] = 'company_code';
                    $docInforArr["documentSystemID"] = 'documentSystemID';
                    $docInforArr["documentID"] = 'document_id';
                    $docInforArr["serviceLineSystemID"] = 'serviceLineSystemID';
                    $docInforArr["serviceLineCode"] = 'serviceLineCode';
                break;
            default:
                return ['success' => false, 'message' => 'Document ID not found'];
        }

        $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
        $masterRec = $namespacedModel::find($documentSystemCode);

        if(!empty($masterRec)){
            if($isFromAPI){
                $employee = UserTypeService::getSystemEmployee();
            } else {
                $employee = Helper::getEmployeeInfo();
            }
            $description = $masterRec[$docInforArr["documentID"]]." ".$masterRec[$docInforArr["documentCodeColumnName"]]." is ".$process;
            if($comment != ''){
                $description .= ". due to below reason: ".$comment;
            }
            $insertArray = [
                'companySystemID' => $masterRec[$docInforArr["companySystemID"]],
                'companyID' => isset($masterRec[$docInforArr["companyID"]])?$masterRec[$docInforArr["companyID"]]:null,
                'serviceLineSystemID' => isset($masterRec[$docInforArr["serviceLineSystemID"]])?$masterRec[$docInforArr["serviceLineSystemID"]]:null,
                'serviceLineCode' => isset($masterRec[$docInforArr["serviceLineCode"]])?$masterRec[$docInforArr["serviceLineCode"]]:null,
                'documentSystemID' => $masterRec[$docInforArr["documentSystemID"]],
                'documentID' => $masterRec[$docInforArr["documentID"]],
                'documentSystemCode' => $masterRec[$docInforArr["primarykey"]],
                'valueFrom' => 0,
                'valueTo' => 0,
                'valueFromSystemID' => null,
                'valueFromText' => $oldValue,
                'valueToSystemID' => null,
                'valueToText' => ucfirst($process),
                'description' => $description,
                'modifiedUserSystemID' => $employee->employeeSystemID,
                'modifiedUserID' => $employee->empID,
                'modifiedDate' => Carbon::now()
            ];
            AuditTrail::create($insertArray);

            if($masterRec->offsetExists('modifiedPc') && $masterRec->offsetExists('modifiedUser') && $masterRec->offsetExists('modifiedUserSystemID'))
            {
                $masterRec->modifiedPc = gethostname();
                $masterRec->modifiedUser =\Helper::getEmployeeID();
                $masterRec->modifiedUserSystemID = \Helper::getEmployeeSystemID();
                $masterRec->save();
            }
   

        }


    }


    public static function insertAuditTrial($modelName, $documentSystemCode, $comment, $process = '')
    {
        $docInforArr = array('modelName' => '', 'primarykey' => '', 'documentCodeColumnName' =>'','companySystemID' => '', 'companyID' => '', 'serviceLineSystemID' =>'','serviceLineCode' => '', 'documentID' => '', 'documentSystemCode' =>'' );

        switch ($modelName) {
            case 'MatchDocumentMaster':
                $docInforArr["modelName"] = 'MatchDocumentMaster';
                $docInforArr["primarykey"] = 'matchDocumentMasterAutoID';
                $docInforArr["documentCodeColumnName"] = 'matchingDocCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;

            case 'CustomerReceivePayment':
                $docInforArr["modelName"] = 'CustomerReceivePayment';
                $docInforArr["primarykey"] = 'custReceivePaymentAutoID';
                $docInforArr["documentCodeColumnName"] = 'custPaymentReceiveCode';
                $docInforArr["companySystemID"] = 'companySystemID';
                $docInforArr["companyID"] = 'companyID';
                $docInforArr["documentSystemID"] = 'documentSystemID';
                $docInforArr["documentID"] = 'documentID';
                break;


            default:
                return ['success' => false, 'message' => 'Document ID not found'];
        }

        $namespacedModel = 'App\Models\\' . $docInforArr["modelName"]; // Model name
        $masterRec = $namespacedModel::find($documentSystemCode);

        if(!empty($masterRec)){
            $employee = Helper::getEmployeeInfo();
            $documentName = $masterRec[$docInforArr["documentID"]];
            if($modelName == 'MatchDocumentMaster'){
                if($masterRec->documentSystemID == 19  || $masterRec->documentSystemID == 21){
                    $documentName = 'Receipt Matching';
                }else if($masterRec->documentSystemID == 4  || $masterRec->documentSystemID == 15){
                    $documentName = 'Payment Voucher Matching';
                }
            }elseif ($modelName == 'CustomerReceivePayment'){
                $documentName = 'Receipt Voucher';
            }

            $description = $documentName." ".$masterRec[$docInforArr["documentCodeColumnName"]]." is ".$process;
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
                'valueToText' => ucfirst($process),
                'description' => $description,
                'modifiedUserSystemID' => $employee->employeeSystemID,
                'modifiedUserID' => $employee->empID,
                'modifiedDate' => Carbon::now()
            ];
            AuditTrail::create($insertArray);
        }

    }

}

<?php

namespace App\Services\GeneralLedger;

use App\Models\DocumentMaster;
use App\Models\CompanyFinancePeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GlPostedDateService
{
	public static function validatePostedDate($documentSystemCode, $documentSystemID, $fromJob = false)
	{
        $docInforArr = array('masterModelName' => '', 'detailModelName' => '', 'primarykeyColumnInDetail' => '', 'documentDate' => '', 'financeCategoryColumn' => '', 'financePeriod' => '');
        switch ($documentSystemID) {
            case 3: // GRV
                $docInforArr["masterModelName"] = 'GRVMaster';
                $docInforArr["detailModelName"] = 'GRVDetails';
                $docInforArr["primarykeyColumnInDetail"] = "grvAutoID";
                $docInforArr["documentDate"] = "grvDate";
                $docInforArr["financeCategoryColumn"] = "itemFinanceCategoryID";
                $docInforArr["financePeriod"] = "financeperiod_by";
                break;
            case 8: // MI - Material issue
                $docInforArr["masterModelName"] = 'ItemIssueMaster';
                $docInforArr["detailModelName"] = 'ItemIssueDetails';
                $docInforArr["primarykeyColumnInDetail"] = "itemIssueAutoID";
                $docInforArr["documentDate"] = "issueDate";
                $docInforArr["financeCategoryColumn"] = "itemFinanceCategoryID";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 12: // SR - Material Return
                $docInforArr["masterModelName"] = 'ItemReturnMaster';
                $docInforArr["detailModelName"] = 'ItemReturnDetails';
                $docInforArr["primarykeyColumnInDetail"] = "itemReturnAutoID";
                $docInforArr["documentDate"] = "ReturnDate";
                $docInforArr["financeCategoryColumn"] = "itemFinanceCategoryID";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 13: // ST - Stock Transfer
                $docInforArr["masterModelName"] = 'StockTransfer';
                $docInforArr["detailModelName"] = 'StockTransferDetails';
                $docInforArr["primarykeyColumnInDetail"] = "stockTransferAutoID";
                $docInforArr["documentDate"] = "tranferDate";
                $docInforArr["financeCategoryColumn"] = "itemFinanceCategoryID";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 10: // RS - Stock Receive
                $docInforArr["masterModelName"] = 'StockReceive';
                $docInforArr["detailModelName"] = 'StockReceiveDetails';
                $docInforArr["primarykeyColumnInDetail"] = "stockReceiveAutoID";
                $docInforArr["documentDate"] = "receivedDate";
                $docInforArr["financeCategoryColumn"] = "itemFinanceCategoryID";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 61: // INRC - Inventory Reclassififcation
                $docInforArr["masterModelName"] = 'InventoryReclassification';
                $docInforArr["detailModelName"] = 'InventoryReclassificationDetail';
                $docInforArr["primarykeyColumnInDetail"] = "inventoryreclassificationID";
                $docInforArr["documentDate"] = "inventoryReclassificationDate";
                $docInforArr["financeCategoryColumn"] = "itemFinanceCategoryID";
                $docInforArr["financePeriod"] = "financeperiod_by";
                break;
            case 24: // PRN - Purchase Return
                $docInforArr["masterModelName"] = 'PurchaseReturn';
                $docInforArr["detailModelName"] = 'PurchaseReturnDetails';
                $docInforArr["primarykeyColumnInDetail"] = "purhaseReturnAutoID";
                $docInforArr["documentDate"] = "purchaseReturnDate";
                $docInforArr["financeCategoryColumn"] = "itemFinanceCategoryID";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 20:
                $docInforArr["masterModelName"] = 'CustomerInvoiceDirect';
                $docInforArr["detailModelName"] = 'CustomerInvoiceItemDetails';
                $docInforArr["primarykeyColumnInDetail"] = "custInvoiceDirectAutoID";
                $docInforArr["documentDate"] = "bookingDate";
                $docInforArr["financeCategoryColumn"] = "itemFinanceCategoryID";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 7: // SA - Stock Adjustment
                $docInforArr["masterModelName"] = 'StockAdjustment';
                $docInforArr["detailModelName"] = 'StockAdjustmentDetails';
                $docInforArr["primarykeyColumnInDetail"] = "stockAdjustmentAutoID";
                $docInforArr["documentDate"] = "stockAdjustmentDate";
                $docInforArr["financeCategoryColumn"] = "itemFinanceCategoryID";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 11: // SI - Supplier Invoice
                $docInforArr["masterModelName"] = 'BookInvSuppMaster';
                $docInforArr["detailModelName"] = 'SupplierInvoiceDirectItem';
                $docInforArr["primarykeyColumnInDetail"] = "bookingSuppMasInvAutoID";
                $docInforArr["documentDate"] = "bookingDate";
                $docInforArr["financeCategoryColumn"] = "itemFinanceCategoryID";
                $docInforArr["financePeriod"] = "financeperiod_by";
                break;
             case 15: // DN - Debit Note
                $docInforArr["masterModelName"] = 'DebitNote';
                $docInforArr["detailModelName"] = 'DebitNoteDetails';
                $docInforArr["primarykeyColumnInDetail"] = "debitNoteAutoID";
                $docInforArr["documentDate"] = "debitNoteDate";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 19: // CN - Credit Note
                $docInforArr["masterModelName"] = 'CreditNote';
                $docInforArr["detailModelName"] = 'CreditNoteDetails';
                $docInforArr["primarykeyColumnInDetail"] = "creditNoteAutoID";
                $docInforArr["documentDate"] = "creditNoteDate";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 4: // PV - Payment Voucher
                $docInforArr["masterModelName"] = 'PaySupplierInvoiceMaster';
                $docInforArr["documentDate"] = "BPVdate";
                $docInforArr["financePeriod"] = "financeperiod_by";
                break;
            case 21: // BRV - Customer Receive Payment
                $docInforArr["masterModelName"] = 'CustomerReceivePayment';
                $docInforArr["documentDate"] = "custPaymentReceiveDate";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 17: // JV - Journal Voucher
                $docInforArr["masterModelName"] = 'JvMaster';
                $docInforArr["documentDate"] = "JVdate";
                $docInforArr["financePeriod"] = "financeperiod_by";
                break;
            case 22: // FA - Fixed Asset Master
                $docInforArr["masterModelName"] = 'FixedAssetMaster';
                $docInforArr["documentDate"] = "documentDate";
                break;
            case 23: // FAD - Fixed Asset Depreciation
                $docInforArr["masterModelName"] = 'FixedAssetDepreciationMaster';
                $docInforArr["documentDate"] = "depDate";
                $docInforArr["financePeriod"] = "financeperiod_by";
                break;
            case 41: // FADS - Fixed Asset Disposal
                $docInforArr["masterModelName"] = 'AssetDisposalMaster';
                $docInforArr["documentDate"] = "disposalDocumentDate";
                $docInforArr["financePeriod"] = "financeperiod_by";
                break;
            case 71:
                $docInforArr["masterModelName"] = 'DeliveryOrder';
                $docInforArr["detailModelName"] = 'DeliveryOrderDetail';
                $docInforArr["primarykeyColumnInDetail"] = "deliveryOrderID";
                $docInforArr["documentDate"] = "deliveryOrderDate";
                $docInforArr["financeCategoryColumn"] = "itemFinanceCategoryID";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 87: // sales return
                $docInforArr["masterModelName"] = 'SalesReturn';
                $docInforArr["detailModelName"] = 'SalesReturnDetail';
                $docInforArr["primarykeyColumnInDetail"] = "salesReturnID";
                $docInforArr["documentDate"] = "salesReturnDate";
                $docInforArr["financeCategoryColumn"] = "itemFinanceCategoryID";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            case 97: // SA - Stock Count
                $docInforArr["masterModelName"] = 'StockCount';
                $docInforArr["detailModelName"] = 'StockCountDetail';
                $docInforArr["primarykeyColumnInDetail"] = "stockCountAutoID";
                $docInforArr["documentDate"] = "stockCountDate";
                $docInforArr["financeCategoryColumn"] = "itemFinanceCategoryID";
                $docInforArr["financePeriod"] = "finance_period_by";
                break;
            default:
                return  ['status' => true, 'message' => "Document ID not found"];
        }

        $postedDate = null;
        $masterModel = 'App\Models\\' . $docInforArr["masterModelName"]; // Model name
        $detailModel = 'App\Models\\' . $docInforArr["detailModelName"]; // Model name


        $financePeriodRelation = $docInforArr["financePeriod"];
        $documentDate = $docInforArr["documentDate"];

        if (in_array($documentSystemID, [15, 19, 21, 17, 22, 23, 41, 4])) {
            if (in_array($documentSystemID, [22])) {
                $masterRec = $masterModel::find($documentSystemCode);
                $postedDate = $masterRec->$documentDate;
            } else {
                $masterRec = $masterModel::with([$docInforArr["financePeriod"]])->find($documentSystemCode);
                if ($masterRec->$financePeriodRelation && $masterRec->$financePeriodRelation->isActive == -1) {
                    $postedDate = $masterRec->$documentDate;
                } 
            }
        } else {
            $masterRec = $masterModel::with([$docInforArr["financePeriod"]])->find($documentSystemCode);

            if ($masterRec) {
                if ($documentSystemID == 20) {
                    if ($masterRec->isPerforma == 2 || $masterRec->isPerforma == 3 || $masterRec->isPerforma == 4 || $masterRec->isPerforma == 5) {
                        $detailInventoryCheck = $detailModel::where($docInforArr['primarykeyColumnInDetail'], $documentSystemCode)
                                                    ->where($docInforArr['financeCategoryColumn'], 1)
                                                    ->first();

                        if (!$detailInventoryCheck) {
                            if ($masterRec->$financePeriodRelation && $masterRec->$financePeriodRelation->isActive == -1) {
                                $postedDate = $masterRec->$documentDate;
                            } 
                        } 
                    } else {
                        if ($masterRec->$financePeriodRelation && $masterRec->$financePeriodRelation->isActive == -1) {
                            $postedDate = $masterRec->$documentDate;
                        } 
                    }                             
                } else if ($documentSystemID == 11) {
                    if ($masterRec->documentType == 3) {
                        $detailInventoryCheck = $detailModel::where($docInforArr['primarykeyColumnInDetail'], $documentSystemCode)
                                                    ->where($docInforArr['financeCategoryColumn'], 1)
                                                    ->first();

                        if (!$detailInventoryCheck) {
                            if ($masterRec->$financePeriodRelation && $masterRec->$financePeriodRelation->isActive == -1) {
                                $postedDate = $masterRec->$documentDate;
                            } 
                        } 
                    }  else {
                        if ($masterRec->$financePeriodRelation && $masterRec->$financePeriodRelation->isActive == -1) {
                            $postedDate = $masterRec->$documentDate;
                        } 
                    }      
                } else {
                    $detailInventoryCheck = $detailModel::where($docInforArr['primarykeyColumnInDetail'], $documentSystemCode)
                                                    ->where($docInforArr['financeCategoryColumn'], 1)
                                                    ->first();

                    if (!$detailInventoryCheck) {
                        if ($masterRec->$financePeriodRelation && $masterRec->$financePeriodRelation->isActive == -1) {
                            $postedDate = $masterRec->$documentDate;
                        } 
                    } 
                }
            }
        }

        if (is_null($postedDate)) {
            $postedDate = date('Y-m-d H:i:s');
            $postedDateOnly = date('Y-m-d');

            $documentMaster = DocumentMaster::find($documentSystemID);

            $companySystemID = isset($masterRec->companySystemID) ? $masterRec->companySystemID : 0;

            if ($documentMaster) {
                $financePeriod = CompanyFinancePeriod::where('departmentSystemID', $documentMaster->departmentSystemID)
                                                     ->where('isActive', -1)
                                                     ->where('companySystemID', $companySystemID)
                                                     ->whereDate('dateFrom', '<=', $postedDateOnly)
                                                     ->whereDate('dateTo', '>=', $postedDateOnly)
                                                     ->first();

                if (!$financePeriod) {
                    return ['status' => false, 'message' => trans('custom.financial_posting_error_financial_period_is_not_active')];
                } 
            }            
        }

        return ['status' => true, 'postedDate' => $postedDate];
    }
}

<?php

namespace App\Services\GeneralLedger;

use App\helper\TaxService;
use App\Models\AdvancePaymentDetails;
use App\Models\AdvanceReceiptDetails;
use App\Models\AssetCapitalization;
use App\Models\AssetDisposalDetail;
use App\Models\AssetDisposalMaster;
use App\Models\BookInvSuppDet;
use App\Models\BookInvSuppMaster;
use App\Models\CreditNote;
use App\Models\CreditNoteDetails;
use App\Models\CurrencyConversion;
use App\Models\StockCount;
use App\Models\StockCountDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerMaster;
use App\Models\CustomerReceivePayment;
use App\Models\CustomerReceivePaymentDetail;
use App\Models\DebitNote;
use App\Models\DebitNoteDetails;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderDetail;
use App\Models\DirectInvoiceDetails;
use App\Models\DirectPaymentDetails;
use App\Models\DirectReceiptDetail;
use App\Models\Employee;
use App\Models\FixedAssetDepreciationMaster;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
use App\Models\PurchaseReturnLogistic;
use App\Models\GeneralLedger;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\InventoryReclassification;
use App\Models\InventoryReclassificationDetail;
use App\Models\ItemIssueDetails;
use App\Models\ItemIssueMaster;
use App\Models\ItemReturnDetails;
use App\Models\ItemReturnMaster;
use App\Models\JvDetail;
use App\Models\JvMaster;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PoAdvancePayment;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetails;
use App\Models\SegmentMaster;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetails;
use App\Models\StockReceive;
use App\Models\StockReceiveDetails;
use App\Models\StockTransfer;
use App\Models\StockTransferDetails;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\Taxdetail;
use App\Models\SupplierInvoiceDirectItem;
use App\Models\Company;
use App\Models\SupplierAssigned;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ChartOfAccount;
use App\Models\SalesReturn;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\SalesReturnDetail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\UnbilledGRVInsert;
use App\Jobs\TaxLedgerInsert;
use App\Services\GeneralLedger\GlPostedDateService;

class SalesReturnGlService
{
	public static function processEntry($masterModel)
	{
         $data = [];
        $taxLedgerData = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);

        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

        $masterDocumentDate = isset($masterModel['documentDateOveride']) ? $masterModel['documentDateOveride'] : $validatePostedDate['postedDate'];
        
        $masterData = SalesReturn::with(['detail' => function ($query) {
            $query->selectRaw('SUM(companyLocalAmount) as localAmount, SUM(companyReportingAmount) as rptAmount,SUM(transactionAmount) as transAmount,salesReturnID');
        }], 'finance_period_by')->find($masterModel["autoID"]);

        //all acoount
        $allAc = SalesReturnDetail::selectRaw("SUM(wacValueLocal*qtyReturned) as localAmount, SUM(wacValueReporting*qtyReturned) as rptAmount,SUM(transactionAmount) as transAmount, reasonCode, isPostItemLedger, reasonGLCode, financeGLcodebBSSystemID as financeGLcodebBSSystemID,financeGLcodebBS as financeGLcodebBS,companyLocalCurrencyID as localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,transactionCurrencyID as transCurrencyID,companyReportingCurrencyER as reportingCurrencyER,companyLocalCurrencyER as localCurrencyER,transactionCurrencyER as transCurrencyER, financeGLcodeRevenueSystemID")
            ->WHERE('salesReturnID', $masterModel["autoID"])
            ->groupBy('financeGLcodebBSSystemID')
            ->get();

        //all acoount
        $COSGAc = SalesReturnDetail::selectRaw("SUM(wacValueLocal*qtyReturned) as localAmount, SUM(wacValueReporting*qtyReturned) as rptAmount,SUM(transactionAmount) as transAmount,financeGLcodebBSSystemID as financeGLcodebBSSystemID,financeGLcodebBS as financeGLcodebBS,companyLocalCurrencyID as localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,transactionCurrencyID as transCurrencyID,companyReportingCurrencyER as reportingCurrencyER,companyLocalCurrencyER as localCurrencyER,transactionCurrencyER as transCurrencyER, financeGLcodeRevenueSystemID, financeCogsGLcodePLSystemID, financeCogsGLcodePL")
            ->WHERE('salesReturnID', $masterModel["autoID"])
            ->groupBy('financeCogsGLcodePLSystemID')
            ->get();

        //all acoount
        $revenueAc = SalesReturnDetail::selectRaw("SUM(companyLocalAmount) as localAmount, SUM(companyReportingAmount) as rptAmount,SUM(transactionAmount) as transAmount,financeGLcodebBSSystemID as financeGLcodebBSSystemID,financeGLcodebBS as financeGLcodebBS,companyLocalCurrencyID as localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,transactionCurrencyID as transCurrencyID,companyReportingCurrencyER as reportingCurrencyER,companyLocalCurrencyER as localCurrencyER,transactionCurrencyER as transCurrencyER, financeGLcodeRevenueSystemID, financeGLcodeRevenue")
            ->WHERE('salesReturnID', $masterModel["autoID"])
            ->groupBy('financeGLcodeRevenueSystemID')
            ->get();

        if ($masterData) {
            $data['companySystemID'] = $masterData->companySystemID;
            $data['companyID'] = $masterData->companyID;
            $data['masterCompanyID'] = null;
            $data['documentSystemID'] = $masterData->documentSystemID;
            $data['documentID'] = $masterData->documentID;
            $data['documentSystemCode'] = $masterModel["autoID"];
            $data['documentCode'] = $masterData->salesReturnCode;
            $data['documentDate'] = $masterDocumentDate;
            $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
            $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
            $data['documentConfirmedDate'] = $masterData->confirmedDate;
            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
            $data['documentFinalApprovedBy'] = $masterData->approvedbyEmpID;
            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedEmpSystemID;
            $data['documentNarration'] = $masterData->narration;

            if ($masterData->returnType == 2) {
                $data['chartOfAccountSystemID'] = $masterData->custGLAccountSystemID;
                $data['glCode'] = $masterData->custGLAccountCode;
                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
            } else {
                $checkFromInvoice = SalesReturnDetail::where('salesReturnID', $masterModel["autoID"])
                    ->whereHas('delivery_order', function($query) {
                        $query->where('selectedForCustomerInvoice', -1);
                    })
                    ->first();

                if ($checkFromInvoice) {
                    $data['chartOfAccountSystemID'] = $masterData->custGLAccountSystemID;
                    $data['glCode'] = $masterData->custGLAccountCode;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                } else {
                    $data['chartOfAccountSystemID'] = $masterData->custUnbilledAccountSystemID;
                    $data['glCode'] = $masterData->custUnbilledAccountCode;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                }
            }

            $data['documentTransCurrencyID'] = $masterData->transactionCurrencyID;
            $data['documentTransCurrencyER'] = $masterData->transactionCurrencyER;
            $data['documentTransAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->transAmount) + ((!is_null($masterData->VATAmount)) ? $masterData->VATAmount : 0)) * -1;
            $data['documentLocalCurrencyID'] = $masterData->companyLocalCurrencyID;
            $data['documentLocalCurrencyER'] = $masterData->companyLocalCurrencyER;
            $data['documentLocalAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->localAmount) + ((!is_null($masterData->VATAmountLocal)) ? $masterData->VATAmountLocal : 0)) * -1;
            $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
            $data['documentRptCurrencyER'] = $masterData->companyReportingCurrencyER;
            $data['documentRptAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->rptAmount) + ((!is_null($masterData->VATAmountRpt)) ? $masterData->VATAmountRpt : 0)) * -1;
            $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
            $data['serviceLineCode'] = $masterData->serviceLineCode;
            $data['clientContractID'] = 'X';
            $data['contractUID'] = 159;
            $data['supplierCodeSystem'] = $masterData->customerID;
            $data['holdingShareholder'] = null;
            $data['holdingPercentage'] = 0;
            $data['nonHoldingPercentage'] = 0;
            $data['chequeNumber'] = 0;
            $data['invoiceNumber'] = 0;
            $data['documentType'] = null;
            $data['createdDateTime'] = \Helper::currentDateTime();
            $data['createdUserID'] = $empID->empID;
            $data['createdUserSystemID'] = $empID->employeeSystemID;
            $data['createdUserPC'] = gethostname();
            $data['timestamp'] = \Helper::currentDateTime();

            if (!($masterData->returnType != 2 && !$checkFromInvoice)) {
                array_push($finalData, $data);
            } 

            if ($allAc) {
                foreach ($allAc as $val) {
                    $currencyConversionInv = \Helper::currencyConversion($masterData->companySystemID, $val->localCurrencyID, $val->transCurrencyID, $val->localAmount);
                    if($val->isPostItemLedger == 0 && $val->reasonCode != null){
                        $data['chartOfAccountSystemID'] = $val->reasonGLCode;
                        $chartOfAccountAssigned = ChartOfAccountsAssigned::where('chartOfAccountSystemID',$val->reasonGLCode)->first();
                        if($chartOfAccountAssigned){
                            $data['glCode'] = $chartOfAccountAssigned->AccountCode;
                        }
                    }else{
                        $data['chartOfAccountSystemID'] = $val->financeGLcodebBSSystemID;
                        $data['glCode'] = $val->financeGLcodebBS;
                    }

                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransCurrencyID'] = $val->transCurrencyID;
                    $data['documentTransCurrencyER'] = $val->transCurrencyER;
                    $data['documentTransAmount'] = \Helper::roundValue(ABS($currencyConversionInv['documentAmount']));
                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount));
                    $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                    $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                    $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount));
                    $data['timestamp'] = \Helper::currentDateTime();
                    array_push($finalData, $data);
                }
            }

            if ($COSGAc) {
                foreach ($COSGAc as $val) {
                    $currencyConversionCog = \Helper::currencyConversion($masterData->companySystemID, $val->localCurrencyID, $val->transCurrencyID, $val->localAmount);

                    $data['chartOfAccountSystemID'] = $val->financeCogsGLcodePLSystemID;
                    $data['glCode'] = $val->financeCogsGLcodePL;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransCurrencyID'] = $val->transCurrencyID;
                    $data['documentTransCurrencyER'] = $val->transCurrencyER;
                    $data['documentTransAmount'] = (\Helper::roundValue(ABS($currencyConversionCog['documentAmount']))) * -1;
                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                    $data['documentLocalAmount'] = (\Helper::roundValue(ABS($val->localAmount))) * -1;
                    $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                    $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                    $data['documentRptAmount'] = (\Helper::roundValue(ABS($val->rptAmount))) * -1;
                    $data['timestamp'] = \Helper::currentDateTime();
                    array_push($finalData, $data);
                }
            }

            if ($revenueAc) {
                foreach ($revenueAc as $val) {
                    $data['chartOfAccountSystemID'] = $val->financeGLcodeRevenueSystemID;
                    $data['glCode'] = $val->financeGLcodeRevenue;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransCurrencyID'] = $val->transCurrencyID;
                    $data['documentTransCurrencyER'] = $val->transCurrencyER;
                    $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount));
                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount));
                    $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                    $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                    $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount));
                    $data['timestamp'] = \Helper::currentDateTime();
                    if (!($masterData->returnType != 2 && !$checkFromInvoice)) {
                        array_push($finalData, $data);
                    } 
                }
            }

            $erp_taxdetail = Taxdetail::where('companySystemID', $masterData->companySystemID)
                ->where('documentSystemCode', $masterData->id)
                ->where('documentSystemID', 87)
                ->get();

            if (!empty($erp_taxdetail)) {
                if ($masterData->returnType == 2) {
                    $taxConfigData = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);
                    $chartofaccountTaxID = $taxConfigData->outputVatGLAccountAutoID;
                    $taxLedgerData['outputVatGLAccountID'] = $chartofaccountTaxID;
                } else {
                    $checkFromInvoice = SalesReturnDetail::where('salesReturnID', $masterModel["autoID"])
                        ->whereHas('delivery_order', function($query) {
                            $query->where('selectedForCustomerInvoice', -1);
                        })
                        ->first();

                    if ($checkFromInvoice) {
                        $taxConfigData = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);
                        $chartofaccountTaxID = $taxConfigData->outputVatGLAccountAutoID;
                        $taxLedgerData['outputVatGLAccountID'] = $chartofaccountTaxID;
                    } else {
                        $taxConfigData = TaxService::getOutputVATTransferGLAccount($masterModel["companySystemID"]);
                        $chartofaccountTaxID = $taxConfigData->outputVatTransferGLAccountAutoID;
                        $taxLedgerData['outputVatTransferGLAccountID'] = $chartofaccountTaxID;
                    }
                }
                if (!empty($taxConfigData) && isset($chartofaccountTaxID) && $chartofaccountTaxID > 0) {
                    $taxGL = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')
                        ->where('chartOfAccountSystemID', $chartofaccountTaxID)
                        ->first();
                    if (!empty($taxGL)) {
                        foreach ($erp_taxdetail as $tax) {
                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';
                            // from customer invoice master table
                            $data['chartOfAccountSystemID'] = $taxGL['chartOfAccountSystemID'];
                            $data['glCode'] = $taxGL->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                            $data['documentNarration'] = $tax->taxDescription;
                            $data['clientContractID'] = 'X';
                            $data['supplierCodeSystem'] = $masterData->customerID;

                            $data['documentTransCurrencyID'] = $tax->currency;
                            $data['documentTransCurrencyER'] = $tax->currencyER;
                            $data['documentTransAmount'] = $tax->amount;
                            $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                            $data['documentLocalAmount'] = $tax->localAmount;
                            $data['documentRptCurrencyID'] = $tax->rptCurrencyID;
                            $data['documentRptCurrencyER'] = $tax->rptCurrencyER;
                            $data['documentRptAmount'] = $tax->rptAmount;
                            if (!($masterData->returnType != 2 && !$checkFromInvoice)) {
                                array_push($finalData, $data);
                            } 
                        }
                    } else {
                        Log::info('Customer Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                        Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                    }
                } else {
                    Log::info('Customer Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                    Log::info('Output Vat GL Account not configured' . date('H:i:s'));
                }
            }
        }
        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
    }
}

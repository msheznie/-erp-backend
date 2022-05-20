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

class CustomerReceivePaymentGlService
{
	public static function processEntry($masterModel)
	{
         $data = [];
        $taxLedgerData = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
        $masterData = CustomerReceivePayment::with(['bank', 'finance_period_by'])->find($masterModel["autoID"]);

        //get balancesheet account
        $cpd = CustomerReceivePaymentDetail::selectRaw("SUM(receiveAmountLocal) as localAmount, SUM(receiveAmountRpt) as rptAmount,SUM(receiveAmountTrans) as transAmount,localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,custTransactionCurrencyID as transCurrencyID,companyReportingER as reportingCurrencyER,localCurrencyER as localCurrencyER,custTransactionCurrencyER as transCurrencyER")
            ->WHERE('custReceivePaymentAutoID', $masterModel["autoID"])
            ->first();

        $totaldd = DirectReceiptDetail::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DRAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DRAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,DDRAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode")
            ->WHERE('directReceiptAutoID', $masterModel["autoID"])
            ->first();

        // advance receipt details

        $totalAdv = AdvanceReceiptDetails::selectRaw("SUM(localAmount) as localAmount, 
                                                        SUM(comRptAmount) as rptAmount,
                                                        SUM(paymentAmount) as transAmount,
                                                        localCurrencyID as localCurrencyID,
                                                        comRptCurrencyID as reportingCurrencyID,
                                                        customerTransCurrencyID as transCurrencyID,
                                                        comRptER as reportingCurrencyER,
                                                        localER,
                                                        customerTransER as transCurrencyER")
                                            ->WHERE('custReceivePaymentAutoID', $masterModel["autoID"])
                                            ->first();


        //get p&l account
        $dd = DirectReceiptDetail::with(['chartofaccount'])
            ->selectRaw("SUM(netAmountLocal) as localAmount, SUM(netAmountRpt) as rptAmount,SUM(netAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DRAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,DDRAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode,comments,chartOfAccountSystemID")
            ->WHERE('directReceiptAutoID', $masterModel["autoID"])
            ->whereNotNull('serviceLineSystemID')
            ->whereNotNull('chartOfAccountSystemID')
            ->groupBy('serviceLineSystemID', 'chartOfAccountSystemID', 'comments')
            ->get();

        $masterDocumentDate = date('Y-m-d H:i:s');
        if ($masterData->finance_period_by->isActive == -1) {
            $masterDocumentDate = $masterData->custPaymentReceiveDate;
        }

        if ($masterData) {
            $data['companySystemID'] = $masterData->companySystemID;
            $data['companyID'] = $masterData->companyID;
            $data['serviceLineSystemID'] = null;
            $data['serviceLineCode'] = null;
            $data['masterCompanyID'] = null;
            $data['documentSystemID'] = $masterData->documentSystemID;
            $data['documentID'] = $masterData->documentID;
            $data['documentSystemCode'] = $masterModel["autoID"];
            $data['documentCode'] = $masterData->custPaymentReceiveCode;
            $data['documentDate'] = $masterDocumentDate;
            $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
            $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
            $data['documentConfirmedDate'] = $masterData->confirmedDate;
            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
            $data['documentNarration'] = $masterData->narration;
            $data['clientContractID'] = 'X';
            $data['contractUID'] = 159;
            $data['supplierCodeSystem'] = $masterData->customerID;
            $data['holdingShareholder'] = null;
            $data['holdingPercentage'] = 0;
            $data['nonHoldingPercentage'] = 0;
            $data['chequeNumber'] = $masterData->custChequeNo;
            $data['documentType'] = $masterData->documentType;
            $data['createdDateTime'] = \Helper::currentDateTime();
            $data['createdUserID'] = $empID->empID;
            $data['createdUserSystemID'] = $empID->employeeSystemID;
            $data['createdUserPC'] = gethostname();
            $data['timestamp'] = \Helper::currentDateTime();


            if ($masterData->documentType == 13) { //Customer Receive Payment
                if ($cpd) {
                    $data['serviceLineSystemID'] = 24;
                    $data['serviceLineCode'] = 'X';
                    $data['chartOfAccountSystemID'] = $masterData->customerGLCodeSystemID;
                    $data['glCode'] = $masterData->customerGLCode;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                    $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                    $data['documentTransAmount'] = \Helper::roundValue($cpd->transAmount) * -1;
                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                    $data['documentLocalAmount'] = \Helper::roundValue($cpd->localAmount) * -1;
                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                    $data['documentRptAmount'] = \Helper::roundValue($cpd->rptAmount) * -1;
                    $data['timestamp'] = \Helper::currentDateTime();
                    array_push($finalData, $data);

                    $data['serviceLineSystemID'] = 24;
                    $data['serviceLineCode'] = 'X';
                    $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 6) :$masterData->bank->chartOfAccountSystemID;
                    $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 6) : $masterData->bank->glCodeLinked;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransCurrencyID'] = $masterData->bankCurrency;
                    $data['documentTransCurrencyER'] = $masterData->bankCurrencyER;
                    $data['documentTransAmount'] = abs(\Helper::roundValue($cpd->transAmount + $totaldd->transAmount));
                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                    $data['documentLocalAmount'] = abs(\Helper::roundValue($cpd->localAmount + $totaldd->localAmount));
                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                    $data['documentRptAmount'] = abs(\Helper::roundValue($cpd->rptAmount + $totaldd->rptAmount));
                    $data['timestamp'] = \Helper::currentDateTime();
                    array_push($finalData, $data);

                    // Bank Charges
                    if ($dd) {
                        foreach ($dd as $val) {
                            $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                            $data['serviceLineCode'] = $val->serviceLineCode;
                            $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                            $data['glCode'] = $val->financeGLcodePL;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentNarration'] = $val->comments;
                            $data['documentTransCurrencyID'] = $val->transCurrencyID;
                            $data['documentTransCurrencyER'] = $val->transCurrencyER;

                            $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $val->localCurrencyER;

                            $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                            $data['documentRptCurrencyER'] = $val->reportingCurrencyER;

                            if ($val->transAmount < 0) {
                                $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount));
                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount));
                                $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount));
                            } else {
                                $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount)) * -1;
                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount)) * -1;
                                $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount)) * -1;
                            }

                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);
                        }
                    }

                }
            }

            if ($masterData->documentType == 14 || $masterData->documentType == 15) { //Direct Receipt & advance receipt
                if ($totaldd) {

                    if($totaldd->transAmount == 0){
                        $totaldd = $totalAdv;
                        $data['serviceLineSystemID'] = 24;
                        $data['serviceLineCode'] = 'X';
                    }else{
                        $data['serviceLineSystemID'] = $totaldd->serviceLineSystemID;
                        $data['serviceLineCode'] = $totaldd->serviceLineCode;
                    }


                    $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 6) :$masterData->bank->chartOfAccountSystemID;
                    $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 6) : $masterData->bank->glCodeLinked;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                    $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                    $data['documentTransAmount'] = \Helper::roundValue($totaldd->transAmount);
                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                    $data['documentLocalAmount'] = \Helper::roundValue($totaldd->localAmount);
                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                    $data['documentRptAmount'] = \Helper::roundValue($totaldd->rptAmount);
                    $data['timestamp'] = \Helper::currentDateTime();
                    array_push($finalData, $data);

                    if ($masterData->documentType == 15) {
                        $data['serviceLineSystemID'] = 24;
                        $data['serviceLineCode'] = 'X';
                        $data['chartOfAccountSystemID'] = $masterData->customerGLCodeSystemID;
                        $data['glCode'] = $masterData->customerGLCode;
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                        $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                        $data['documentTransAmount'] = \Helper::roundValue($totaldd->transAmount) * -1;
                        $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                        $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                        $data['documentLocalAmount'] = \Helper::roundValue($totaldd->localAmount) * -1;
                        $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                        $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                        $data['documentRptAmount'] = \Helper::roundValue($totaldd->rptAmount) * -1;
                        $data['timestamp'] = \Helper::currentDateTime();
                        array_push($finalData, $data);
                    }

                    if ($dd) {
                        foreach ($dd as $val) {
                            $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                            $data['serviceLineCode'] = $val->serviceLineCode;
                            $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                            $data['glCode'] = $val->financeGLcodePL;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] =ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentNarration'] = $val->comments;
                            $data['documentTransCurrencyID'] = $val->transCurrencyID;
                            $data['documentTransCurrencyER'] = $val->transCurrencyER;
                            $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount)) * -1;
                            $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount)) * -1;
                            $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                            $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                            $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount)) * -1;
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);
                        }
                    }
                }
            }

            $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, 
                                        SUM(rptAmount) as rptAmount,
                                        SUM(amount) as transAmount,
                                        localCurrencyID,
                                        rptCurrencyID as reportingCurrencyID,
                                        currency as supplierTransactionCurrencyID,
                                        currencyER as supplierTransactionER,
                                        rptCurrencyER as companyReportingER,
                                        localCurrencyER")
                ->WHERE('documentSystemCode', $masterModel["autoID"])
                ->WHERE('documentSystemID', $masterModel["documentSystemID"])
                ->groupBy('documentSystemCode')
                ->first();

            $taxLocal = 0;
            $taxRpt = 0;
            $taxTrans = 0;

            if ($tax) {
                $taxLocal = $tax->localAmount;
                $taxRpt = $tax->rptAmount;
                $taxTrans = $tax->transAmount;
                $taxConfigData = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);

                if (!empty($taxConfigData)) {  // out put vat entries
                    $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->outputVatGLAccountAutoID)
                        ->where('companySystemID', $masterData->companySystemID)
                        ->first();

                    if (!empty($chartOfAccountData)) {
                        $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                        $data['glCode'] = $chartOfAccountData->AccountCode;
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    } else {
                        return ['status' => false, 'error' => ['message' => "Output Vat GL Account not assigned to company"]];
                    }
                } else {
                    return ['status' => false, 'error' => ['message' => "Output Vat GL Account not configured"]];
                }

                $data['serviceLineSystemID'] = 24;
                $data['serviceLineCode'] = 'X';
                $data['clientContractID'] = 'X';
                $data['contractUID'] = 159;

                $data['documentTransCurrencyID'] = $tax->supplierTransactionCurrencyID;
                $data['documentTransCurrencyER'] = $tax->supplierTransactionER;
                $data['documentTransAmount'] = \Helper::roundValue(ABS($taxTrans)) * -1;
                $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                $data['documentLocalAmount'] = \Helper::roundValue(ABS($taxLocal)) * -1;
                $data['documentRptCurrencyID'] = $tax->reportingCurrencyID;
                $data['documentRptCurrencyER'] = $tax->companyReportingER;
                $data['documentRptAmount'] = \Helper::roundValue(ABS($taxRpt)) * -1;
                array_push($finalData, $data);

                if($masterData->documentType == 15) { // out put vat transfer entries

                    $taxConfigData = TaxService::getOutputVATTransferGLAccount($masterModel["companySystemID"]);

                    if (!empty($taxConfigData)) {
                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->outputVatTransferGLAccountAutoID)
                            ->where('companySystemID', $masterData->companySystemID)
                            ->first();

                        if (!empty($chartOfAccountData)) {
                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                            $data['glCode'] = $chartOfAccountData->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        } else {
                            return ['status' => false, 'error' => ['message' => "Output Vat Transfer GL Account not assigned to company"]];
                        }
                    } else {
                        return ['status' => false, 'error' => ['message' => "Output Vat Transfer GL Account not configured"]];
                    }

                    $data['documentTransAmount'] = \Helper::roundValue(ABS($taxTrans));
                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($taxLocal));
                    $data['documentRptAmount'] = \Helper::roundValue(ABS($taxRpt));
                    array_push($finalData, $data);
                }
            }
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
    }
}
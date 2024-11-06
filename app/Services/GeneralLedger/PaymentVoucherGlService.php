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
use App\Models\PaymentVoucherBankChargeDetails;
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
use App\Models\TaxVatCategories;
use App\Services\ExchangeSetup\ExchangeSetupGlService;
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
use ExchangeSetupConfig;

class PaymentVoucherGlService
{
	public static function processEntry($masterModel)
	{
        $data = [];
        $taxLedgerData = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
        $masterData = PaySupplierInvoiceMaster::with(['bank', 'financeperiod_by', 'transactioncurrency', 'localcurrency', 'rptcurrency'])->find($masterModel["autoID"]);
        $linkDocument = null;
        //get balancesheet account
        $si = PaySupplierInvoiceDetail::selectRaw("SUM(paymentLocalAmount) as localAmount, SUM(paymentComRptAmount) as rptAmount,SUM(supplierPaymentAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierPaymentCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierPaymentER as transCurrencyER")->WHERE('PayMasterAutoId', $masterModel["autoID"])->WHERE('matchingDocID', 0)->first();

        $siApData = PaySupplierInvoiceDetail::selectRaw("SUM(paymentLocalAmount) as localAmount, SUM(paymentComRptAmount) as rptAmount,SUM(supplierPaymentAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierPaymentCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierPaymentER as transCurrencyER")->WHERE('PayMasterAutoId', $masterModel["autoID"])->WHERE('matchingDocID', 0)->WHERE('isRetention', 0)->first();

        $retentionData = PaySupplierInvoiceDetail::selectRaw("SUM(paymentLocalAmount) as localAmount, SUM(paymentComRptAmount) as rptAmount,SUM(supplierPaymentAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierPaymentCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierPaymentER as transCurrencyER")->WHERE('PayMasterAutoId', $masterModel["autoID"])->WHERE('matchingDocID', 0)->WHERE('isRetention', 1)->first();

        $dp = DirectPaymentDetails::with(['chartofaccount'])->selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DPAmount) as transAmount,SUM(bankAmount) as bankAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DPAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,DPAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode,chartOfAccountSystemID,comments,bankCurrencyID,vatSubCategoryID,vatAmount,VATAmountLocal,VATAmountRpt")->WHERE('directPaymentAutoID', $masterModel["autoID"])->whereNotNull('serviceLineSystemID')->whereNotNull('chartOfAccountSystemID')->groupBy('serviceLineSystemID', 'chartOfAccountSystemID', 'comments')->get();

        $dpTotal = DirectPaymentDetails::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DPAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DPAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,DPAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode")->WHERE('directPaymentAutoID', $masterModel["autoID"])->first();

        $exemptVatTotal = DirectPaymentDetails::selectRaw("SUM(vatAmount) as vatAmount, SUM(VATAmountLocal) as VATAmountLocal, SUM(VATAmountRpt) as VATAmountRpt")->whereHas('vatSubCategories', function ($q) {
            $q->where('subCatgeoryType', 3);
        })->WHERE('directPaymentAutoID', $masterModel["autoID"])->first();

        $bankChargeDetails = PaymentVoucherBankChargeDetails::where('payMasterAutoID',$masterData['PayMasterAutoId'])->get();

        $bankChargeDetailsSum = PaymentVoucherBankChargeDetails::selectRaw("SUM(dpAmount) as dpAmount, SUM(localAmount) as localAmount,SUM(comRptAmount) as comRptAmount")->WHERE('payMasterAutoID', $masterData['PayMasterAutoId'])->first();


        $ap = AdvancePaymentDetails::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(supplierTransAmount) as transAmount, SUM(VATAmountLocal) as VATAmountLocalTotal, SUM(VATAmountRpt) as VATAmountRptTotal,SUM(VATAmount) as VATAmountTotal,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierTransCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierTransER as transCurrencyER")->WHERE('PayMasterAutoId', $masterModel["autoID"])->first();

        $isBankCheck = DirectPaymentDetails::WHERE('directPaymentAutoID', $masterModel["autoID"])->WHERE('glCodeIsBank', 1)->first();

        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

        $masterDocumentDate = isset($masterModel['documentDateOveride']) ? $masterModel['documentDateOveride'] : $validatePostedDate['postedDate'];

        $localCurrDP = $masterData->localcurrency ? $masterData->localcurrency->DecimalPlaces : 3;
        $rptCurrDP = $masterData->rptcurrency ? $masterData->rptcurrency->DecimalPlaces : 2;
        $isMasterExchangeRateChanged = ExchangeSetupConfig::isMasterDocumentExchageRateChanged($masterData) &&  ExchangeSetupConfig::checkExchangeRateChangedOnDocumnentLevel($masterData);
        if ($masterData) {
            $data['companySystemID'] = $masterData->companySystemID;
            $data['companyID'] = $masterData->companyID;
            $data['serviceLineSystemID'] = null;
            $data['serviceLineCode'] = null;
            $data['masterCompanyID'] = null;
            $data['documentSystemID'] = $masterData->documentSystemID;
            $data['documentID'] = $masterData->documentID;
            $data['documentSystemCode'] = $masterModel["autoID"];
            $data['documentCode'] = $masterData->BPVcode;
            $data['documentDate'] = $masterDocumentDate;
            $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
            $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
            $data['documentConfirmedDate'] = $masterData->confirmedDate;
            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
            $data['documentNarration'] = $masterData->BPVNarration;
            $data['clientContractID'] = 'X';
            $data['contractUID'] = 159;
            $data['supplierCodeSystem'] = $masterData->BPVsupplierID;
            $data['employeeSystemID'] = $masterData->directPaymentPayeeEmpID;
            $data['holdingShareholder'] = null;
            $data['holdingPercentage'] = 0;
            $data['nonHoldingPercentage'] = 0;
            $data['chequeNumber'] = $masterData->BPVchequeNo;
            $data['documentType'] = $masterData->invoiceType;
            $data['createdDateTime'] = \Helper::currentDateTime();
            $data['createdUserID'] = $empID->empID;
            $data['createdUserSystemID'] = $empID->employeeSystemID;
            $data['createdUserPC'] = gethostname();
            $data['timestamp'] = \Helper::currentDateTime();

            if ($masterData->invoiceType == 2 || $masterData->invoiceType == 6) { //Supplier Payment
                if ($si) {
                    $transAmountTotal = 0;
                    $localAmountTotal = 0;
                    $rptAmountTotal = 0;
                    $linkDocument = $si;
                    $masterTransAmountTotal = $si->transAmount + $bankChargeDetailsSum->dpAmount;
                    $masterLocalAmountTotal = $masterData->payAmountCompLocal;
                    $masterRptAmountTotal = $masterData->payAmountCompRpt;

                    $data['serviceLineSystemID'] = 24;
                    $data['serviceLineCode'] = 'X';
                    $data['chartOfAccountSystemID'] = $masterData->supplierGLCodeSystemID;
                    $data['glCode'] = $masterData->supplierGLCode;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                    $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                    $data['documentTransAmount'] = \Helper::roundValue($siApData->transAmount);
                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $si->transAmount/$si->localAmount;
                    $data['documentLocalAmount'] = \Helper::roundValue($siApData->localAmount);
                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                    $data['documentRptCurrencyER'] = $si->transAmount/$si->rptAmount;
                    $data['documentRptAmount'] = \Helper::roundValue($siApData->rptAmount);
                    if($isMasterExchangeRateChanged)
                    {
                        $data['documentLocalCurrencyER'] = $si->transAmount/$si->localAmount;
                        $data['documentRptCurrencyER'] = $si->transAmount/$si->rptAmount;
                    }

                    $data['timestamp'] = \Helper::currentDateTime();
                    if ($siApData && $siApData->transAmount > 0) {
                        array_push($finalData, $data);
                    }

                    if ($retentionData && $retentionData->transAmount > 0) {


                        $retentionTransAmount = $retentionData->transAmount;
                        $retentionLocalAmount = $retentionData->localAmount;
                        $retentionRptAmount = $retentionData->rptAmount;


                        $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "retention-control-account");
                        $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "retention-control-account");
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        $data['documentTransAmount'] = \Helper::roundValue($retentionTransAmount);
                        $data['documentLocalAmount'] = \Helper::roundValue($retentionLocalAmount);
                        $data['documentRptAmount'] = \Helper::roundValue($retentionRptAmount);
                        array_push($finalData, $data);
                    }

                    if ($isMasterExchangeRateChanged)
                   {
                       $transAmountTotal = $si->transAmount + $bankChargeDetailsSum->dpAmount;
                       $masterLocalAmountTotal = $si->localAmount + $bankChargeDetailsSum->localAmount;
                       $masterRptAmountTotal = $si->rptAmount + $bankChargeDetailsSum->comRptAmount;

                       //convert amount in currency conversion
                       $convertAmount = \Helper::convertAmountToLocalRpt(203, $masterModel["autoID"], $transAmountTotal);

                       $localAmountTotal = $convertAmount["localAmount"] + $bankChargeDetailsSum->localAmount;
                       $rptAmountTotal = $convertAmount["reportingAmount"];



                       $retationVATAmount = 0;
                        $retentionLocalVatAmount = 0;
                        $retentionRptVatAmount = 0;
                        $retationVATAmount = TaxService::calculateRetentionVatAmount($masterModel["autoID"]);

                        if ($retationVATAmount > 0) {
                            $currencyConvertionRetention = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransCurrencyID, $masterData->supplierTransCurrencyID, $retationVATAmount);

                            $retentionLocalVatAmount = $currencyConvertionRetention['localAmount'];
                            $retentionRptVatAmount = $currencyConvertionRetention['reportingAmount'];
                        }


                        $data['serviceLineSystemID'] = 24;
                        $data['serviceLineCode'] = 'X';
                        $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account") :$masterData->bank->chartOfAccountSystemID;
                        $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account") : $masterData->bank->glCodeLinked;
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                        $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                        $data['documentTransAmount'] = \Helper::roundValue($transAmountTotal + $retationVATAmount) * -1;
                        $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                        $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                        $data['documentLocalAmount'] = ($convertAmount["localAmount"] + $retentionLocalVatAmount) * -1;
                        $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                        $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                        $data['documentRptAmount'] = ($convertAmount["reportingAmount"] + $retentionRptVatAmount) * -1;
                        $retationRcmVATAmount = TaxService::calculateRCMRetentionVatAmount($masterModel["autoID"]);

                        if ($retationRcmVATAmount > 0) {
                            $data['documentTransAmount'] = \Helper::roundValue($transAmountTotal) * -1;
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = ($convertAmount["localAmount"]) * -1;
                            $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                            $data['documentRptAmount'] = ($convertAmount["reportingAmount"]) * -1;
                        }
                        $data['timestamp'] = \Helper::currentDateTime();
                        array_push($finalData, $data);
                    }else {
                        $transAmountTotal = $si->transAmount + $bankChargeDetailsSum->dpAmount;
                        $localAmountTotal = $si->localAmount + $bankChargeDetailsSum->localAmount;
                        $rptAmountTotal = $si->rptAmount + $bankChargeDetailsSum->comRptAmount;

                        $retationVATAmount = 0;
                        $retentionLocalVatAmount = 0;
                        $retentionRptVatAmount = 0;
                        $retationVATAmount = TaxService::calculateRetentionVatAmount($masterModel["autoID"]);

                        if ($retationVATAmount > 0) {
                            $currencyConvertionRetention = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransCurrencyID, $masterData->supplierTransCurrencyID, $retationVATAmount);

                            $retentionLocalVatAmount = $currencyConvertionRetention['localAmount'];
                            $retentionRptVatAmount = $currencyConvertionRetention['reportingAmount'];
                        }



                        $data['serviceLineSystemID'] = 24;
                        $data['serviceLineCode'] = 'X';
                        $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account") :$masterData->bank->chartOfAccountSystemID;
                        $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account") : $masterData->bank->glCodeLinked;
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                        $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;

                        $convertAmount = \Helper::convertAmountToLocalRpt(203, $masterModel["autoID"], ($transAmountTotal + $retationVATAmount));

                        $data['documentTransAmount'] = \Helper::roundValue($transAmountTotal + $retationVATAmount) * -1;
                        $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                        $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                        $data['documentLocalAmount'] = \Helper::roundValue($convertAmount["localAmount"]) * -1;
                        $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                        $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                        $data['documentRptAmount'] = \Helper::roundValue($convertAmount["reportingAmount"]) * -1;

                        $retationRcmVATAmount = TaxService::calculateRCMRetentionVatAmount($masterModel["autoID"]);
                        if ($retationRcmVATAmount > 0) {
                            $data['documentTransAmount'] = \Helper::roundValue($transAmountTotal) * -1;
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $localAmountTotal != 0 ? ($transAmountTotal / $localAmountTotal) : 0;
                            $data['documentLocalAmount'] = \Helper::roundValue($localAmountTotal) * -1;
                            $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                            $data['documentRptCurrencyER'] = $rptAmountTotal != 0 ? ($transAmountTotal / $rptAmountTotal) : 0;
                            $data['documentRptAmount'] = \Helper::roundValue($rptAmountTotal) * -1;
                        }
                        $data['timestamp'] = \Helper::currentDateTime();
                        array_push($finalData, $data);
                    }

                    foreach ($bankChargeDetails as $bankChargeDetail) {
                        $data['serviceLineSystemID'] = $bankChargeDetail->serviceLineSystemID;
                        $data['serviceLineCode'] = $bankChargeDetail->serviceLineCode;
                        $data['chartOfAccountSystemID'] = $bankChargeDetail->chartOfAccountSystemID;
                        $data['glCode'] = $bankChargeDetail->glCode;
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                        $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                        $data['documentTransAmount'] = \Helper::roundValue($bankChargeDetail->dpAmount);
                        $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                        $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                        $data['documentLocalAmount'] = $bankChargeDetail->localAmount;
                        $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                        $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                        $data['documentRptAmount'] = $bankChargeDetail->comRptAmount;
                        $data['timestamp'] = \Helper::currentDateTime();
                        array_push($finalData, $data);
                    }

                    $diffTrans = $transAmountTotal - $masterTransAmountTotal;
                    $diffLocal = $localAmountTotal - $masterLocalAmountTotal;
                    $diffRpt = $rptAmountTotal - $masterRptAmountTotal;
                    Log::info('Payment Voucher xxxx' . date('H:i:s'));
                    Log::info('Tras' . $diffTrans);
                    Log::info('Local' . $diffLocal);
                    Log::info('Rpt' . $diffRpt);

                    if (ABS(round($diffTrans)) != 0 || ABS(round($diffLocal, $masterData->localcurrency->DecimalPlaces)) != 0 || ABS(round($diffRpt, $masterData->rptcurrency->DecimalPlaces)) != 0) {
                        $company = Company::find($masterData->companySystemID);

                        $exchangeGainServiceLine = SegmentMaster::where('companySystemID', $masterData->companySystemID)
                            ->where('isPublic', 1)
                            ->where('isActive', 1)
                            ->first();
                        Log::info('Payment Voucher ---- GL -----' . date('H:i:s'));
                        Log::info($exchangeGainServiceLine);

                        if (!empty($exchangeGainServiceLine)) {
                            Log::info('Payment Voucher ---- GL ----- Exist' . date('H:i:s'));
                            $data['serviceLineSystemID'] = $exchangeGainServiceLine->serviceLineSystemID;
                            $data['serviceLineCode'] = $exchangeGainServiceLine->ServiceLineCode;
                        } else {
                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';
                        }

                        Log::info('Payment Voucher ---- GL -----' . date('H:i:s'));

                        $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "exchange-gainloss-gl");
                        $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "exchange-gainloss-gl");
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                        $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;


                        $data['documentTransAmount'] = \Helper::roundValue($diffTrans);
                        $data['documentLocalAmount'] = \Helper::roundValue($diffLocal);
                        $data['documentRptAmount'] = \Helper::roundValue($diffRpt);
                        $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                        $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                        $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                        $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                        if($isMasterExchangeRateChanged)
                        {
                            $data['documentTransAmount'] = \Helper::roundValue(ABS($diffTrans)) * ($diffTrans > 0 ? 1 : -1);
                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($diffLocal)) * ($diffLocal > 0 ? 1 : -1);
                            $data['documentRptAmount'] = \Helper::roundValue(ABS($diffRpt)) * ($diffRpt > 0 ? 1 : -1);
                            $data['documentLocalCurrencyER'] = $si->transAmount/$si->localAmount;
                            $data['documentRptCurrencyER'] = $si->transAmount/$si->rptAmount;



                        }

                        $data['timestamp'] = \Helper::currentDateTime();
                        array_push($finalData, $data);
                    }

                    if ($retentionData) {
                        $retationVATAmount = TaxService::calculateRetentionVatAmount($masterModel["autoID"]);

                        if ($retationVATAmount > 0) {
                            $currencyConvertionRetention = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransCurrencyID, $masterData->supplierTransCurrencyID, $retationVATAmount);

                            $taxConfigData = TaxService::getInputVATGLAccount($masterModel["companySystemID"]);
                            if (!empty($taxConfigData)) {
                                $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->inputVatGLAccountAutoID)
                                    ->where('companySystemID', $masterData->companySystemID)
                                    ->first();

                                if (!empty($chartOfAccountData)) {
                                    $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                    $data['glCode'] = $chartOfAccountData->AccountCode;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransAmount'] = \Helper::roundValue(ABS($retationVATAmount));
                                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($currencyConvertionRetention['localAmount']));
                                    $data['documentRptAmount'] = \Helper::roundValue(ABS($currencyConvertionRetention['reportingAmount']));

                                    array_push($finalData, $data);

                                    $taxLedgerData['inputVATGlAccountID'] = $chartOfAccountData->chartOfAccountSystemID;

                                } else {
                                    Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                    Log::info('Input Vat GL Account not assigned to company' . date('H:i:s'));
                                }
                            } else {
                                Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                Log::info('Input Vat Transfer GL Account not configured' . date('H:i:s'));
                            }
                        }


                        $retationRcmVATAmount = TaxService::calculateRCMRetentionVatAmount($masterModel["autoID"]);

                        if ($retationRcmVATAmount > 0) {
                            $currencyConvertionRetention = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransCurrencyID, $masterData->supplierTransCurrencyID, $retationRcmVATAmount);

                            $taxConfigData2 = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);
                            if (!empty($taxConfigData2)) {
                                $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData2->outputVatGLAccountAutoID)
                                    ->where('companySystemID', $masterData->companySystemID)
                                    ->first();

                                if (!empty($chartOfAccountData)) {
                                    $retationVATAmount = 0;
                                    $retentionLocalVatAmount = 0;
                                    $retentionRptVatAmount = 0;
                                    $retationVATAmount = TaxService::calculateRetentionVatAmount($masterModel["autoID"]);

                                    if ($retationVATAmount > 0) {
                                        $currencyConvertionRetention = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransCurrencyID, $masterData->supplierTransCurrencyID, $retationVATAmount);

                                        $retentionLocalVatAmount = $currencyConvertionRetention['localAmount'];
                                        $retentionRptVatAmount = $currencyConvertionRetention['reportingAmount'];
                                    }

                                    $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                    $data['glCode'] = $chartOfAccountData->AccountCode;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransAmount'] = \Helper::roundValue(ABS($retationVATAmount) * -1);
                                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($retentionLocalVatAmount) * -1);
                                    $data['documentRptAmount'] = \Helper::roundValue(ABS($retentionRptVatAmount) * -1);

                                    array_push($finalData, $data);

                                    $taxLedgerData['outputVatGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;

                                } else {
                                    Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                    Log::info('Input Vat GL Account not assigned to company' . date('H:i:s'));
                                }
                            } else {
                                Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                Log::info('Input Vat Transfer GL Account not configured' . date('H:i:s'));
                            }
                        }
                    }
                }
            }

            if ($masterData->invoiceType == 5 || $masterData->invoiceType == 7) { //Advance Payment
                if ($ap) {
                    $data['serviceLineSystemID'] = 24;
                    $data['serviceLineCode'] = 'X';
                    if($masterData->invoiceType == 7) {
                        $data['chartOfAccountSystemID'] = $masterData->employeeAdvanceAccountSystemID;
                        $data['glCode'] = $masterData->employeeAdvanceAccount;
                    } else {
                        $data['chartOfAccountSystemID'] = $masterData->advanceAccountSystemID;
                        $data['glCode'] = $masterData->AdvanceAccount;
                    }
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                    $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                    $data['documentTransAmount'] = \Helper::roundValue($ap->transAmount);
                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;

                    if(ExchangeSetupConfig::isMasterDocumentExchageRateChanged($masterData))
                    {
                        $data['documentLocalAmount'] = \Helper::roundValue($ap->transAmount/ $masterData->localCurrencyER);
                        $data['documentRptAmount'] = \Helper::roundValue($ap->transAmount/ $masterData->companyRptCurrencyER);
                    }else {
                        $data['documentLocalAmount'] = \Helper::roundValue($ap->localAmount);
                        $data['documentRptAmount'] = \Helper::roundValue($ap->rptAmount);

                    }
                    $data['timestamp'] = \Helper::currentDateTime();
                    array_push($finalData, $data);

                    $data['serviceLineSystemID'] = 24;
                    $data['serviceLineCode'] = 'X';
                    $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account") :$masterData->bank->chartOfAccountSystemID;
                    $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account") : $masterData->bank->glCodeLinked;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                    $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                    $data['documentTransAmount'] = \Helper::roundValue($ap->transAmount) * -1;
                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                    $data['timestamp'] = \Helper::currentDateTime();

                    if(ExchangeSetupConfig::isMasterDocumentExchageRateChanged($masterData))
                    {
                        $data['documentLocalAmount'] = \Helper::roundValue($ap->transAmount/ $masterData->localCurrencyER) * -1;
                        $data['documentRptAmount'] = \Helper::roundValue($ap->transAmount/ $masterData->companyRptCurrencyER) * -1;
                    }else {
                        $data['documentLocalAmount'] = \Helper::roundValue($ap->localAmount) * -1;
                        $data['documentRptAmount'] = \Helper::roundValue($ap->rptAmount) * -1;

                    }

                    array_push($finalData, $data);
                    

                    if ($masterData->invoiceType == 5 && $ap->VATAmountTotal > 0) {
                        $taxData = TaxService::getInputVATTransferGLAccount($masterData->companySystemID);

                        if (!empty($taxData)) {
                            $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxData->inputVatTransferGLAccountAutoID)
                                ->where('companySystemID', $masterData->companySystemID)
                                ->first();

                            if (!empty($chartOfAccountData)) {
                                $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                $data['glCode'] = $chartOfAccountData->AccountCode;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                $data['documentTransAmount'] = \Helper::roundValue($ap->VATAmountTotal) * -1;
                                $data['documentLocalAmount'] = \Helper::roundValue($ap->VATAmountLocalTotal) * -1;
                                $data['documentRptAmount'] = \Helper::roundValue($ap->VATAmountRptTotal) * -1;

                                if(ExchangeSetupConfig::isMasterDocumentExchageRateChanged($masterData))
                                {
                                    $data['documentLocalAmount'] = \Helper::roundValue($ap->VATAmountTotal/$masterData->localCurrencyER) * -1;
                                    $data['documentRptAmount'] = \Helper::roundValue($ap->VATAmountTotal/$masterData->companyRptCurrencyER) * -1;
                                }

                                array_push($finalData, $data);

                                $taxLedgerData['inputVatTransferAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                            } 
                        } 

                        $taxData2 = TaxService::getInputVATGLAccount($masterData->companySystemID);
                        if (!empty($taxData2)) {
                            $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxData2->inputVatGLAccountAutoID)
                                ->where('companySystemID', $masterData->companySystemID)
                                ->first();

                            if (!empty($chartOfAccountData)) {
                                $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                $data['glCode'] = $chartOfAccountData->AccountCode;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                $data['documentTransAmount'] = \Helper::roundValue($ap->VATAmountTotal);
                                $data['documentLocalAmount'] = \Helper::roundValue($ap->VATAmountLocalTotal);
                                $data['documentRptAmount'] = \Helper::roundValue($ap->VATAmountRptTotal);

                                if(ExchangeSetupConfig::isMasterDocumentExchageRateChanged($masterData))
                                {
                                    $data['documentLocalAmount'] = \Helper::roundValue($ap->VATAmountTotal/$masterData->localCurrencyER);
                                    $data['documentRptAmount'] = \Helper::roundValue($ap->VATAmountTotal/$masterData->companyRptCurrencyER);
                                }

                                array_push($finalData, $data);

                                $taxLedgerData['inputVatGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                            } 
                        } 
                    }
                }

            }


            if ($masterData->invoiceType == 3) { //Direct Payment

                $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER,payeeSystemCode")
                    ->WHERE('documentSystemCode', $masterModel["autoID"])
                    ->WHERE('documentSystemID', $masterModel["documentSystemID"])
                    ->groupBy('documentSystemCode')
                    ->first();

               if($isMasterExchangeRateChanged)
               {
                   $tax->localAmount = ($tax->transAmount/$masterData->localCurrencyER);
                   $tax->rptAmount = ($tax->transAmount/$masterData->companyRptCurrencyER);
               }

                $expenseCOA = TaxVatCategories::with(['tax'])->where('subCatgeoryType', 3)->whereHas('tax', function ($query) use ($masterData) {
                    $query->where('companySystemID', $masterData->companySystemID);
                })->where('isActive', 1)->first();

                $isVATEligible = TaxService::checkCompanyVATEligible($masterData->companySystemID);

                if ($isVATEligible == 1) {
                    if($tax){
                        if($masterData->rcmActivated == 0) {
                            $masterLocal = $masterData->payAmountCompLocal;
                            $masterRpt = $masterData->payAmountCompRpt;
                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';
                            $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account") : $masterData->bank->chartOfAccountSystemID;
                            $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account") : $masterData->bank->glCodeLinked;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                            $data['documentTransAmount'] = \Helper::roundValue($dpTotal->transAmount + $tax->transAmount) * -1;
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue($masterLocal + $tax->localAmount) * -1;
                            $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                            $data['documentRptAmount'] = \Helper::roundValue($masterRpt + $tax->rptAmount) * -1;
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);


                            $taxInputVATControl = TaxService::getInputVATGLAccount($masterData->companySystemID);
                            if (!empty($taxInputVATControl)) {

                                $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxInputVATControl->inputVatGLAccountAutoID)
                                    ->where('companySystemID', $masterData->companySystemID)
                                    ->first();
                                if (!empty($chartOfAccountData)) {

                                    $data['serviceLineSystemID'] = 24;
                                    $data['serviceLineCode'] = 'X';
                                    $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                    $data['glCode'] = $chartOfAccountData->AccountCode;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                                    $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                                    $data['documentTransAmount'] = \Helper::roundValue($tax->transAmount - $exemptVatTotal->vatAmount);
                                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                    $data['documentLocalAmount'] = \Helper::roundValue($tax->localAmount - $exemptVatTotal->VATAmountLocal);
                                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                    $data['documentRptAmount'] = \Helper::roundValue($tax->rptAmount - $exemptVatTotal->VATAmountRpt);
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    if($data['documentTransAmount'] != 0) {
                                        array_push($finalData, $data);
                                    }
                                    $taxLedgerData['inputVatGLAccountID'] = $data['chartOfAccountSystemID'];
                                }
                            }

                            $convertedLocalAmount = 0;
                            $convertedRpt = 0;
                            $convertedTrans = 0;
                            if ($dp) {
                                foreach ($dp as $val) {
                                    $taxType = TaxVatCategories::find($val->vatSubCategoryID);
                                    if ($isBankCheck) {
                                        //calculate local amount
                                        if ($val->bankCurrencyID == $val->localCurrencyID) {
                                            if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountLocal));
                                            }
                                            else{
                                                $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount));
                                            }
                                            $convertedLocalAmount += $data['documentLocalAmount'];
                                        } else {
                                            $conversion = CurrencyConversion::where('masterCurrencyID', $val->bankCurrencyID)->where('subCurrencyID', $val->localCurrencyID)->first();
                                            $data['documentLocalCurrencyER'] = $conversion->conversion;
                                            if ($conversion->conversion > 1) {
                                                if ($conversion->conversion > 1) {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                        $data['documentLocalAmount'] = \Helper::roundValue((($val->bankAmount) / $conversion->conversion) + $val->VATAmountLocal);
                                                    }
                                                    else{

                                                        $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                                    }
                                                } else {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {

                                                        $data['documentLocalAmount'] = \Helper::roundValue((($val->bankAmount) * $conversion->conversion) + $val->VATAmountLocal);
                                                    }
                                                    else{
                                                        $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                    }
                                                }
                                                $convertedLocalAmount += $data['documentLocalAmount'];
                                            } else {
                                                if ($conversion->conversion > 1) {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                        $data['documentLocalAmount'] = \Helper::roundValue((($val->bankAmount) * $conversion->conversion) + $val->VATAmountLocal);
                                                    }
                                                    else{
                                                        $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                    }
                                                } else {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                        $data['documentLocalAmount'] = \Helper::roundValue((($val->bankAmount) / $conversion->conversion) + $val->VATAmountLocal);
                                                    }
                                                    else{
                                                        $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                                    }
                                                }
                                                $convertedLocalAmount += $data['documentLocalAmount'];
                                            }
                                        }

                                        //calculate reporting amount
                                        if ($val->bankCurrencyID == $val->reportingCurrencyID) {
                                            if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountRpt));
                                            }
                                            else{
                                                $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount));
                                            }
                                            $convertedRpt += $data['documentRptAmount'];
                                        } else {
                                            $conversion = CurrencyConversion::where('masterCurrencyID', $val->bankCurrencyID)->where('subCurrencyID', $val->reportingCurrencyID)->first();
                                            $data['documentRptCurrencyER'] = $conversion->conversion;
                                            if ($conversion->conversion > 1) {
                                                if ($conversion->conversion > 1) {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                        $data['documentRptAmount'] = \Helper::roundValue((($val->bankAmount) / $conversion->conversion) + $val->VATAmountRpt);
                                                    }
                                                    else{
                                                        $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                                    }
                                                } else {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                        $data['documentRptAmount'] = \Helper::roundValue((($val->bankAmount) * $conversion->conversion) + $val->VATAmountRpt);
                                                    }
                                                    else{
                                                        $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                    }
                                                }
                                                $convertedRpt += $data['documentRptAmount'];
                                            } else {
                                                if ($conversion->conversion > 1) {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                        $data['documentRptAmount'] = \Helper::roundValue((($val->bankAmount) * $conversion->conversion) + $val->VATAmountRpt);
                                                    }
                                                    else{
                                                        $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                    }
                                                } else {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                        $data['documentRptAmount'] = \Helper::roundValue((($val->bankAmount) / $conversion->conversion) + $val->VATAmountRpt);
                                                    }
                                                    else{
                                                        $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                                    }
                                                }
                                                $convertedRpt += $data['documentRptAmount'];
                                            }
                                        }
                                    } else {
                                        $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                        if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3)  {
                                            $data['documentLocalAmount'] = \Helper::roundValue($val->localAmount + $val->VATAmountLocal);
                                        }
                                        else{
                                            $data['documentLocalAmount'] = \Helper::roundValue($val->localAmount);
                                        }
                                        $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                                        if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                            $data['documentRptAmount'] = \Helper::roundValue($val->rptAmount + $val->VATAmountRpt);
                                        }
                                        else{
                                            $data['documentRptAmount'] = \Helper::roundValue($val->rptAmount);
                                        }

                                        $convertedLocalAmount += \Helper::roundValue($data['documentLocalAmount']);
                                        $convertedRpt += \Helper::roundValue( $data['documentRptAmount']);
                                    }

                                    $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                    $data['serviceLineCode'] = $val->serviceLineCode;
                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['glCode'] = $val->financeGLcodePL;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentNarration'] = $val->comments;
                                    $data['documentTransCurrencyID'] = $val->transCurrencyID;
                                    $data['documentTransCurrencyER'] = $val->transCurrencyER;
                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                        $data['documentTransAmount'] = \Helper::roundValue($val->transAmount + $val->vatAmount);
                                    }
                                    else{
                                        $data['documentTransAmount'] = \Helper::roundValue($val->transAmount);
                                    }
                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    $convertedTrans += \Helper::roundValue($data['documentTransAmount']);
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        if($masterData->rcmActivated == 1){
                            $masterLocal = $masterData->payAmountCompLocal;
                            $masterRpt = $masterData->payAmountCompRpt;
                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';
                            $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account") : $masterData->bank->chartOfAccountSystemID;
                            $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account") : $masterData->bank->glCodeLinked;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                            $data['documentTransAmount'] = \Helper::roundValue($dpTotal->transAmount + $exemptVatTotal->vatAmount) * -1;
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue($masterLocal + $exemptVatTotal->VATAmountLocal) * -1;
                            $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                            $data['documentRptAmount'] = \Helper::roundValue($masterRpt + $exemptVatTotal->VATAmountRpt) * -1;
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);


                            $taxInputVATControl = TaxService::getInputVATGLAccount($masterData->companySystemID);
                            if (!empty($taxInputVATControl)) {

                                $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxInputVATControl->inputVatGLAccountAutoID)
                                    ->where('companySystemID', $masterData->companySystemID)
                                    ->first();
                                if (!empty($chartOfAccountData)) {

                                    $data['serviceLineSystemID'] = 24;
                                    $data['serviceLineCode'] = 'X';
                                    $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                    $data['glCode'] = $chartOfAccountData->AccountCode;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                                    $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                                    $data['documentTransAmount'] = \Helper::roundValue($tax->transAmount - $exemptVatTotal->vatAmount);
                                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                    $data['documentLocalAmount'] = \Helper::roundValue($tax->localAmount - $exemptVatTotal->VATAmountLocal);
                                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                    $data['documentRptAmount'] = \Helper::roundValue($tax->rptAmount - $exemptVatTotal->VATAmountRpt);
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                    $taxLedgerData['inputVatGLAccountID'] = $data['chartOfAccountSystemID'];

                                }

                            }


                            $taxOutputVATControl = TaxService::getOutputVATGLAccount($masterData->companySystemID);
                            if (!empty($taxOutputVATControl)) {

                                $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxOutputVATControl->outputVatGLAccountAutoID)
                                    ->where('companySystemID', $masterData->companySystemID)
                                    ->first();
                                if (!empty($chartOfAccountData)) {

                                    $data['serviceLineSystemID'] = 24;
                                    $data['serviceLineCode'] = 'X';
                                    $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                    $data['glCode'] = $chartOfAccountData->AccountCode;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                                    $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                                    $data['documentTransAmount'] = \Helper::roundValue($tax->transAmount - $exemptVatTotal->vatAmount) * -1;
                                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                    $data['documentLocalAmount'] = \Helper::roundValue($tax->localAmount - $exemptVatTotal->VATAmountLocal) * -1;
                                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                    $data['documentRptAmount'] = \Helper::roundValue($tax->rptAmount - $exemptVatTotal->VATAmountRpt) * -1;
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                    $taxLedgerData['outputVatGLAccountID'] = $data['chartOfAccountSystemID'];

                                }

                            }
                            $convertedLocalAmount = 0;
                            $convertedRpt = 0;
                            $convertedTrans = 0;
                            if ($dp) {
                                foreach ($dp as $val) {
                                    $taxType = TaxVatCategories::find($val->vatSubCategoryID);
                                    if ($isBankCheck) {
                                        //calculate local amount
                                        if ($val->bankCurrencyID == $val->localCurrencyID) {
                                            if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountLocal));
                                            }
                                            else{
                                                $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount +  + $val->VATAmountLocal));
                                            }
                                            $convertedLocalAmount += $data['documentLocalAmount'];
                                        } else {
                                            $conversion = CurrencyConversion::where('masterCurrencyID', $val->bankCurrencyID)->where('subCurrencyID', $val->localCurrencyID)->first();
                                            $data['documentLocalCurrencyER'] = $conversion->conversion;
                                            if ($conversion->conversion > 1) {
                                                if ($conversion->conversion > 1) {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                        $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountLocal) / $conversion->conversion);
                                                    }
                                                    else{

                                                        $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                                    }
                                                } else {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {

                                                        $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountLocal) * $conversion->conversion);
                                                    }
                                                    else{
                                                        $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                    }
                                                }
                                                $convertedLocalAmount += $data['documentLocalAmount'];
                                            } else {
                                                if ($conversion->conversion > 1) {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                        $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountLocal) * $conversion->conversion);
                                                    }
                                                    else{
                                                        $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                    }
                                                } else {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                        $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountLocal) / $conversion->conversion);
                                                    }
                                                    else{
                                                        $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                                    }
                                                }
                                                $convertedLocalAmount += $data['documentLocalAmount'];
                                            }
                                        }

                                        //calculate reporting amount
                                        if ($val->bankCurrencyID == $val->reportingCurrencyID) {
                                            if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountRpt));
                                            }
                                            else{
                                                $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount));
                                            }
                                            $convertedRpt += $data['documentRptAmount'];
                                        } else {
                                            $conversion = CurrencyConversion::where('masterCurrencyID', $val->bankCurrencyID)->where('subCurrencyID', $val->reportingCurrencyID)->first();
                                            $data['documentRptCurrencyER'] = $conversion->conversion;
                                            if ($conversion->conversion > 1) {
                                                if ($conversion->conversion > 1) {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                        $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountRpt) / $conversion->conversion);
                                                    }
                                                    else{
                                                        $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                                    }
                                                } else {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                        $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountRpt) * $conversion->conversion);
                                                    }
                                                    else{
                                                        $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                    }
                                                }
                                                $convertedRpt += $data['documentRptAmount'];
                                            } else {
                                                if ($conversion->conversion > 1) {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                        $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountRpt) * $conversion->conversion);
                                                    }
                                                    else{
                                                        $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                    }
                                                } else {
                                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                                        $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountRpt) / $conversion->conversion);
                                                    }
                                                    else{
                                                        $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                                    }
                                                }
                                                $convertedRpt += $data['documentRptAmount'];
                                            }
                                        }
                                    }
                                    else {
                                        $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                        if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                            $data['documentLocalAmount'] = \Helper::roundValue($val->localAmount + $val->VATAmountLocal);
                                        }
                                        else{
                                            $data['documentLocalAmount'] = \Helper::roundValue($val->localAmount);
                                        }
                                        $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                                        if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                            $data['documentRptAmount'] = \Helper::roundValue($val->rptAmount + $val->VATAmountRpt);
                                        }
                                        else{
                                            $data['documentRptAmount'] = \Helper::roundValue($val->rptAmount);
                                        }
                                        $convertedLocalAmount += \Helper::roundValue($data['documentLocalAmount']);
                                        $convertedRpt += \Helper::roundValue( $data['documentRptAmount']);
                                    }

                                    $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                    $data['serviceLineCode'] = $val->serviceLineCode;
                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['glCode'] = $val->financeGLcodePL;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentNarration'] = $val->comments;
                                    $data['documentTransCurrencyID'] = $val->transCurrencyID;
                                    $data['documentTransCurrencyER'] = $val->transCurrencyER;
                                    if(isset($expenseCOA) && $expenseCOA->expense == null && $expenseCOA->recordType == 2 && isset($taxType) && $taxType->subCatgeoryType == 3) {
                                        $data['documentTransAmount'] = \Helper::roundValue($val->transAmount + $val->vatAmount);
                                    }
                                    else{
                                        $data['documentTransAmount'] = \Helper::roundValue($val->transAmount);
                                    }
                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    $convertedTrans += \Helper::roundValue($data['documentTransAmount']);
                                    array_push($finalData, $data);
                                }
                            }

                        }


                    }
                    else{
                        $masterLocal = $masterData->payAmountCompLocal;
                        $masterRpt = $masterData->payAmountCompRpt;
                        $data['serviceLineSystemID'] = 24;
                        $data['serviceLineCode'] = 'X';
                        $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account") : $masterData->bank->chartOfAccountSystemID;
                        $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account") : $masterData->bank->glCodeLinked;
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                        $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                        $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                        $data['documentTransAmount'] = \Helper::roundValue($dpTotal->transAmount) * -1;
                        $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                        $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                        $data['documentLocalAmount'] = \Helper::roundValue($masterLocal) * -1;
                        $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                        $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                        $data['documentRptAmount'] = $masterData->expenseClaimOrPettyCash == 1? \Helper::roundValue($dpTotal->transAmount/$masterData->companyRptCurrencyER) * -1:\Helper::roundValue($masterRpt) * -1;
                        $data['timestamp'] = \Helper::currentDateTime();
                        array_push($finalData, $data);

                        $convertedLocalAmount = 0;
                        $convertedRpt = 0;
                        $convertedTrans = 0;
                        if ($dp) {
                            foreach ($dp as $val) {
                                if ($isBankCheck) {
                                    //calculate local amount
                                    if ($val->bankCurrencyID == $val->localCurrencyID) {
                                        $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount));
                                        $convertedLocalAmount += $data['documentLocalAmount'];
                                    } else {
                                        $conversion = CurrencyConversion::where('masterCurrencyID', $val->bankCurrencyID)->where('subCurrencyID', $val->localCurrencyID)->first();
                                        $data['documentLocalCurrencyER'] = $conversion->conversion;
                                        if ($conversion->conversion > 1) {
                                            if ($conversion->conversion > 1) {
                                                $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                            } else {
                                                $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                            }
                                            $convertedLocalAmount += $data['documentLocalAmount'];
                                        } else {
                                            if ($conversion->conversion > 1) {
                                                $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                            } else {
                                                $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                            }
                                            $convertedLocalAmount += $data['documentLocalAmount'];
                                        }
                                    }

                                    //calculate reporting amount
                                    if ($val->bankCurrencyID == $val->reportingCurrencyID) {
                                        $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount));
                                        $convertedRpt += $data['documentRptAmount'];
                                    } else {
                                        $conversion = CurrencyConversion::where('masterCurrencyID', $val->bankCurrencyID)->where('subCurrencyID', $val->reportingCurrencyID)->first();
                                        $data['documentRptCurrencyER'] = $conversion->conversion;
                                        if ($conversion->conversion > 1) {
                                            if ($conversion->conversion > 1) {
                                                $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                            } else {
                                                $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                            }
                                            $convertedRpt += $data['documentRptAmount'];
                                        } else {
                                            if ($conversion->conversion > 1) {
                                                $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                            } else {
                                                $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                            }
                                            $convertedRpt += $data['documentRptAmount'];
                                        }
                                    }
                                } else {
                                    $data['documentLocalCurrencyER'] =  $masterData->expenseClaimOrPettyCash == 1?$masterData->localCurrencyER:$val->localCurrencyER;
                                    $data['documentLocalAmount'] =  $masterData->expenseClaimOrPettyCash == 1? \Helper::roundValue($val->transAmount/$masterData->localCurrencyER) :\Helper::roundValue($val->localAmount);
                                    $data['documentRptCurrencyER'] = $masterData->expenseClaimOrPettyCash == 1? $masterData->companyRptCurrencyER: $val->reportingCurrencyER;
                                    $data['documentRptAmount'] = $masterData->expenseClaimOrPettyCash == 1? \Helper::roundValue($val->transAmount/$masterData->companyRptCurrencyER) : \Helper::roundValue($val->rptAmount);
                                    $convertedLocalAmount += $masterData->expenseClaimOrPettyCash == 1? \Helper::roundValue($val->transAmount/$masterData->localCurrencyER) :\Helper::roundValue($val->localAmount);
                                    $convertedRpt += $masterData->expenseClaimOrPettyCash == 1? \Helper::roundValue($val->transAmount/$masterData->companyRptCurrencyER) : \Helper::roundValue($val->rptAmount);
                                }

                                $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                $data['serviceLineCode'] = $val->serviceLineCode;
                                $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                $data['glCode'] = $val->financeGLcodePL;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentNarration'] = $val->comments;
                                $data['documentTransCurrencyID'] = $masterData->expenseClaimOrPettyCash == 1?$masterData->supplierTransCurrencyID: $val->transCurrencyID;
                                $data['documentTransCurrencyER'] = $masterData->expenseClaimOrPettyCash == 1?$masterData->supplierTransCurrencyER: $val->transCurrencyER;
                                $data['documentTransAmount'] = \Helper::roundValue($val->transAmount);
                                $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                                $data['timestamp'] = \Helper::currentDateTime();
                                $convertedTrans += \Helper::roundValue($val->transAmount);
                                array_push($finalData, $data);
 
                            }
                        }
                    }
                }
                else{
                    $masterLocal = $masterData->payAmountCompLocal;
                    $masterRpt = $masterData->payAmountCompRpt;
                    $data['serviceLineSystemID'] = 24;
                    $data['serviceLineCode'] = 'X';
                    $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account") : $masterData->bank->chartOfAccountSystemID;
                    $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account") : $masterData->bank->glCodeLinked;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                    $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                    $data['documentTransAmount'] = \Helper::roundValue($dpTotal->transAmount) * -1;
                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                    $data['documentLocalAmount'] = \Helper::roundValue($masterLocal) * -1;
                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                    $data['documentRptAmount'] = \Helper::roundValue($masterRpt) * -1;
                    $data['timestamp'] = \Helper::currentDateTime();
                    array_push($finalData, $data);

                    $convertedLocalAmount = 0;
                    $convertedRpt = 0;
                    $convertedTrans = 0;

                    if ($dp) {
                        foreach ($dp as $val) {
                            if ($isBankCheck) {
                                //calculate local amount
                                if ($val->bankCurrencyID == $val->localCurrencyID) {
                                    $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount));
                                    $convertedLocalAmount += $data['documentLocalAmount'];
                                } else {
                                    $conversion = CurrencyConversion::where('masterCurrencyID', $val->bankCurrencyID)->where('subCurrencyID', $val->localCurrencyID)->first();
                                    $data['documentLocalCurrencyER'] = $conversion->conversion;
                                    if ($conversion->conversion > 1) {
                                        if ($conversion->conversion > 1) {
                                            $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                        } else {
                                            $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                        }
                                        $convertedLocalAmount += $data['documentLocalAmount'];
                                    } else {
                                        if ($conversion->conversion > 1) {
                                            $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                        } else {
                                            $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                        }
                                        $convertedLocalAmount += $data['documentLocalAmount'];
                                    }
                                }

                                //calculate reporting amount
                                if ($val->bankCurrencyID == $val->reportingCurrencyID) {
                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount));
                                    $convertedRpt += $data['documentRptAmount'];
                                } else {
                                    $conversion = CurrencyConversion::where('masterCurrencyID', $val->bankCurrencyID)->where('subCurrencyID', $val->reportingCurrencyID)->first();
                                    $data['documentRptCurrencyER'] = $conversion->conversion;
                                    if ($conversion->conversion > 1) {
                                        if ($conversion->conversion > 1) {
                                            $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                        } else {
                                            $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                        }
                                        $convertedRpt += $data['documentRptAmount'];
                                    } else {
                                        if ($conversion->conversion > 1) {
                                            $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                        } else {
                                            $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                        }
                                        $convertedRpt += $data['documentRptAmount'];
                                    }
                                }
                            } else {
                                $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                $data['documentLocalAmount'] = \Helper::roundValue($val->localAmount);
                                $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                                $data['documentRptAmount'] = \Helper::roundValue($val->rptAmount);
                                $convertedLocalAmount += \Helper::roundValue($val->localAmount);
                                $convertedRpt += \Helper::roundValue($val->rptAmount);
                            }

                            $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                            $data['serviceLineCode'] = $val->serviceLineCode;
                            $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                            $data['glCode'] = $val->financeGLcodePL;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentNarration'] = $val->comments;
                            $data['documentTransCurrencyID'] = $val->transCurrencyID;
                            $data['documentTransCurrencyER'] = $val->transCurrencyER;
                            $data['documentTransAmount'] = \Helper::roundValue($val->transAmount);
                            $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                            $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                            $data['timestamp'] = \Helper::currentDateTime();
                            $convertedTrans += \Helper::roundValue($val->transAmount);
                            array_push($finalData, $data);
                        }
                    }
                }


                if(!empty($exemptVatTotal) && !empty($expenseCOA) && $expenseCOA->expenseGL != null && $expenseCOA->recordType == 1 && $exemptVatTotal->vatAmount > 0){
                    $exemptVatTrans = $exemptVatTotal->vatAmount;
                    $exemptVATLocal = $exemptVatTotal->VATAmountLocal;
                    $exemptVatRpt = $exemptVatTotal->VATAmountRpt;

                    $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $expenseCOA->expenseGL)->where('companySystemID', $masterData->companySystemID)->first();
                    $data['chartOfAccountSystemID'] = $expenseCOA->expenseGL;
                    $data['glCode'] = $chartOfAccountData->AccountCode;
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransAmount'] = $exemptVatTrans;
                    $data['documentLocalAmount'] = $exemptVATLocal;
                    $data['documentRptAmount'] = $exemptVatRpt;
                    $data['timestamp'] = \Helper::currentDateTime();
                    array_push($finalData, $data);
                }

                if($masterData->expenseClaimOrPettyCash == 1)
                {
                    $masterRpt1 =  \Helper::roundValue($dpTotal->transAmount/$masterData->companyRptCurrencyER);
                    $diffRptAmount = \Helper::roundValue($convertedRpt) - \Helper::roundValue($masterRpt1);
                    $tolerance = 1e-6; 
                        if (abs($diffRptAmount) < $tolerance) {
                            $diffRptAmount = 0;
                        }
                }
                else
                {
                    $diffRptAmount = $convertedRpt - $masterRpt;
                }
               

                if($exemptVatTotal && isset($expenseCOA) && $expenseCOA->recordType == 2) {
                    $diffTrans = $convertedTrans - $dpTotal->transAmount - $exemptVatTotal->vatAmount;
                    $diffLocal = $convertedLocalAmount - $masterLocal - $exemptVatTotal->VATAmountLocal;
                    $diffRpt = $diffRptAmount - $exemptVatTotal->VATAmountRpt;
                }
                else{
                    $diffTrans = $convertedTrans - $dpTotal->transAmount;
                    $diffLocal = $convertedLocalAmount - $masterLocal;
                    $diffRpt = $diffRptAmount;
                }

                if (ABS(round($diffTrans)) != 0 || ABS(round($diffLocal, $masterData->localcurrency->DecimalPlaces)) != 0 || ABS(round($diffRpt, $masterData->rptcurrency->DecimalPlaces)) != 0) {

                    $company = Company::find($masterData->companySystemID);

                    $exchangeGainServiceLine = SegmentMaster::where('companySystemID', $masterData->companySystemID)
                        ->where('isPublic', 1)
                        ->where('isActive', 1)
                        ->first();
                    Log::info('Payment Voucher ---- GL -----' . date('H:i:s'));
                    Log::info($exchangeGainServiceLine);

                    if (!empty($exchangeGainServiceLine)) {
                        Log::info('Payment Voucher ---- GL ----- Exist' . date('H:i:s'));
                        $data['serviceLineSystemID'] = $exchangeGainServiceLine->serviceLineSystemID;
                        $data['serviceLineCode'] = $exchangeGainServiceLine->ServiceLineCode;
                    } else {
                        $data['serviceLineSystemID'] = 24;
                        $data['serviceLineCode'] = 'X';
                    }

                    Log::info('Payment Voucher ---- GL -----' . date('H:i:s'));

                    $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "exchange-gainloss-gl");
                    $data['glCode'] =  SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "exchange-gainloss-gl");
                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                    $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                    $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;

                    if ($diffTrans > 0) {
                        $data['documentTransAmount'] = \Helper::roundValue(ABS($diffTrans)) * -1;
                    } else {
                        $data['documentTransAmount'] = \Helper::roundValue(ABS($diffTrans));
                    }

                    if ($diffLocal > 0) {
                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($diffLocal)) * -1;
                    } else {
                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($diffLocal));
                    }

                    if ($diffRpt > 0) {
                        $data['documentRptAmount'] = \Helper::roundValue(ABS($diffRpt)) * -1;
                    } else {
                        $data['documentRptAmount'] = \Helper::roundValue(ABS($diffRpt));
                    }

                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;

                    $data['timestamp'] = \Helper::currentDateTime();
                    array_push($finalData, $data);
                }

                $linkDocument = $dp;
            }

            if(ExchangeSetupConfig::isMasterDocumentExchageRateChanged($masterData))
            {
                $exchangeSetupGlService = new ExchangeSetupGlService();
                $finalData = $exchangeSetupGlService->postGlEntry($finalData,$masterData,$linkDocument);
            }


        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
    }
}

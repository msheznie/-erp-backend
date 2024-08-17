<?php

namespace App\Jobs;

use App\helper\TaxService;
use App\Models\AdvancePaymentDetails;
use App\Models\AdvanceReceiptDetails;
use App\Models\AssetCapitalization;
use App\Models\AssetDisposalDetail;
use App\Models\AssetDisposalMaster;
use App\Models\PdcLog;
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
use App\Models\Company;
use App\Models\SupplierAssigned;
use App\Models\ChartOfAccountsAssigned;
use App\Models\ChartOfAccount;
use App\Models\SalesReturn;
use App\Models\SystemGlCodeScenarioDetail;
use App\Models\SalesReturnDetail;
use App\Models\BankLedger;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\UnbilledGRVInsert;
use App\Jobs\BankLedgerInsert;
use App\Jobs\TaxLedgerInsert;

class PdcDoubleEntry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $masterModel;
    protected $pdcData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($masterModel, $pdcData)
    {
        $this->masterModel = $masterModel;
        $this->pdcData = $pdcData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/pdc_double_entry_jobs.log');
        $masterModel = $this->masterModel;
        $pdcData = $this->pdcData;

        if (!empty($masterModel)) {
            DB::beginTransaction();
            try {
                $data = [];
                $taxLedgerData = [];
                $finalData = [];
                $empID = Employee::find($masterModel['employeeSystemID']);
                $pdcLogData = PdcLog::find($masterModel['pdcID']);
                switch ($masterModel["documentSystemID"]) {
                    case 4: // PV - Payment Voucher
                        $masterData = PaySupplierInvoiceMaster::with(['bank', 'financeperiod_by', 'transactioncurrency', 'localcurrency', 'rptcurrency'])->find($masterModel["autoID"]);

                        //get balancesheet account
                        $si = PaySupplierInvoiceDetail::selectRaw("SUM(paymentLocalAmount) as localAmount, SUM(paymentComRptAmount) as rptAmount,SUM(supplierPaymentAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierPaymentCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierPaymentER as transCurrencyER")->WHERE('PayMasterAutoId', $masterModel["autoID"])->WHERE('matchingDocID', 0)->first();

                        $dp = DirectPaymentDetails::with(['chartofaccount'])->selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DPAmount) as transAmount,SUM(bankAmount) as bankAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DPAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,DPAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode,chartOfAccountSystemID,comments,bankCurrencyID")->WHERE('directPaymentAutoID', $masterModel["autoID"])->whereNotNull('serviceLineSystemID')->whereNotNull('chartOfAccountSystemID')->groupBy('serviceLineSystemID', 'chartOfAccountSystemID', 'comments')->get();

                        $dpTotal = DirectPaymentDetails::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DPAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DPAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,DPAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode")->WHERE('directPaymentAutoID', $masterModel["autoID"])->first();

                        $ap = AdvancePaymentDetails::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(supplierTransAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierTransCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierTransER as transCurrencyER")->WHERE('PayMasterAutoId', $masterModel["autoID"])->first();

                        $isBankCheck = DirectPaymentDetails::WHERE('directPaymentAutoID', $masterModel["autoID"])->WHERE('glCodeIsBank', 1)->first();

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if ($masterData->financeperiod_by->isActive == -1) {
                            $masterDocumentDate = $masterData->BPVdate;
                        }

                        if (isset($pdcData['date'])) {
                            $masterDocumentDate = Carbon::parse($pdcData['date']);
                        }

                        $localCurrDP = $masterData->localcurrency ? $masterData->localcurrency->DecimalPlaces : 3;
                        $rptCurrDP = $masterData->rptcurrency ? $masterData->rptcurrency->DecimalPlaces : 2;

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
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['chequeNumber'] = $pdcLogData ? $pdcLogData->chequeNo : null;
                            $data['pdcID'] = $pdcLogData ? $pdcLogData->id : null;
                            $data['documentType'] = $masterData->invoiceType;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            if ($masterData->invoiceType == 2) { //Supplier Payment
                                if ($si) {
                                    $transAmountTotal = 0;
                                    $localAmountTotal = 0;
                                    $rptAmountTotal = 0;

                                    if ($masterData->BPVbankCurrency == $masterData->supplierTransCurrencyID) {
                                        $transAmountTotal = $si->transAmount;
                                        $localAmountTotal = $si->localAmount;
                                        $rptAmountTotal = $si->rptAmount;

                                        $data['serviceLineSystemID'] = 24;
                                        $data['serviceLineCode'] = 'X';
                                        $data['glAccountType'] = 'BS';
                                        $data['glAccountTypeID'] = 1;
                                        $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                        $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;
                                        $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                        $data['documentLocalCurrencyER'] = $si->localAmount != 0 ? ($si->transAmount / $si->localAmount) : 0;
                                        $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                        $data['documentRptCurrencyER'] = $si->rptAmount != 0 ? ($si->transAmount / $si->rptAmount) : 0;
                                        $data['timestamp'] = \Helper::currentDateTime();

                                        $currencyConvertionData = \Helper::currencyConversion($masterData->companySystemID, $masterData->BPVbankCurrency, $masterData->BPVbankCurrency, $pdcData['amount']);
                                        if ($pdcData['newStatus'] == 1) {
                                            $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                            $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                            $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']);
                                            $data['documentLocalAmount'] = \Helper::roundValue($currencyConvertionData['localAmount']);
                                            $data['documentRptAmount'] = \Helper::roundValue($currencyConvertionData['reportingAmount']);
                                            array_push($finalData, $data);

                                            $data['chartOfAccountSystemID'] = $masterData->bank->chartOfAccountSystemID;
                                            $data['glCode'] = $masterData->bank->glCodeLinked;
                                            $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']) * -1;
                                            $data['documentLocalAmount'] = \Helper::roundValue($currencyConvertionData['localAmount']) * -1;
                                            $data['documentRptAmount'] = \Helper::roundValue($currencyConvertionData['reportingAmount']) * -1;
                                            array_push($finalData, $data);
                                        }

                                        if ($pdcData['newStatus'] == 2) {
                                            $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                            $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                            $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']) * -1;
                                            $data['documentLocalAmount'] = \Helper::roundValue($currencyConvertionData['localAmount']) * -1;
                                            $data['documentRptAmount'] = \Helper::roundValue($currencyConvertionData['reportingAmount']) * -1;
                                            array_push($finalData, $data);

                                            $data['chartOfAccountSystemID'] = $masterData->bank->chartOfAccountSystemID;
                                            $data['glCode'] = $masterData->bank->glCodeLinked;
                                            $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']);
                                            $data['documentLocalAmount'] = \Helper::roundValue($currencyConvertionData['localAmount']);
                                            $data['documentRptAmount'] = \Helper::roundValue($currencyConvertionData['reportingAmount']);
                                            array_push($finalData, $data);
                                        }
                                    } else {
                                        //convert amount in currency conversion
                                        $convertAmount = \Helper::convertAmountToLocalRpt(203, $masterModel["autoID"], $pdcData['amount']);

                                        $transAmountTotal = $pdcData['amount'];
                                        $localAmountTotal = $convertAmount["localAmount"];
                                        $rptAmountTotal = $convertAmount["reportingAmount"];

                                        $data['serviceLineSystemID'] = 24;
                                        $data['serviceLineCode'] = 'X';
                                        $data['glAccountType'] = 'BS';
                                        $data['glAccountTypeID'] = 1;
                                        $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                        $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;
                                        $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                        $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                        $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                        $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                        $data['timestamp'] = \Helper::currentDateTime();

                                        if ($pdcData['newStatus'] == 1) {
                                            $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                            $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                            $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']);
                                            $data['documentLocalAmount'] = $convertAmount["localAmount"];
                                            $data['documentRptAmount'] = $convertAmount["reportingAmount"];
                                            array_push($finalData, $data);

                                            $data['chartOfAccountSystemID'] = $masterData->bank->chartOfAccountSystemID;
                                            $data['glCode'] = $masterData->bank->glCodeLinked;
                                            $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']) * -1;
                                            $data['documentLocalAmount'] = $convertAmount["localAmount"] * -1;
                                            $data['documentRptAmount'] = $convertAmount["reportingAmount"] * -1;
                                            array_push($finalData, $data);
                                        }

                                        if ($pdcData['newStatus'] == 2) {
                                            $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                            $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                            $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']) * -1;
                                            $data['documentLocalAmount'] = $convertAmount["localAmount"] * -1;
                                            $data['documentRptAmount'] = $convertAmount["reportingAmount"] * -1;
                                            array_push($finalData, $data);

                                            $data['chartOfAccountSystemID'] = $masterData->bank->chartOfAccountSystemID;
                                            $data['glCode'] = $masterData->bank->glCodeLinked;
                                            $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']);
                                            $data['documentLocalAmount'] = $convertAmount["localAmount"];
                                            $data['documentRptAmount'] = $convertAmount["reportingAmount"];
                                            array_push($finalData, $data);
                                        }
                                    }
                                }
                            }

                            if ($masterData->invoiceType == 5) { //Advance Payment
                                if ($ap) {
                                    $currencyConvertionData = \Helper::currencyConversion($masterData->companySystemID, $masterData->BPVbankCurrency, $masterData->BPVbankCurrency, $pdcData['amount']);
                                    $data['serviceLineSystemID'] = 24;
                                    $data['serviceLineCode'] = 'X';
                                    $data['glAccountType'] = 'BS';
                                    $data['glAccountTypeID'] = 1;
                                    $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                    $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;
                                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;

                                    if ($pdcData['newStatus'] == 1) {
                                        $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                        $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                        $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']);
                                        $data['documentLocalAmount'] = \Helper::roundValue($currencyConvertionData['localAmount']);
                                        $data['documentRptAmount'] = \Helper::roundValue($currencyConvertionData['reportingAmount']);
                                        $data['timestamp'] = \Helper::currentDateTime();
                                        array_push($finalData, $data);

                                        $data['chartOfAccountSystemID'] = $masterData->bank->chartOfAccountSystemID;
                                        $data['glCode'] = $masterData->bank->glCodeLinked;
                                        $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']) * -1;
                                        $data['documentLocalAmount'] = \Helper::roundValue($currencyConvertionData['localAmount']) * -1;
                                        $data['documentRptAmount'] = \Helper::roundValue($currencyConvertionData['reportingAmount']) * -1;
                                        $data['timestamp'] = \Helper::currentDateTime();
                                        array_push($finalData, $data);
                                    }

                                     if ($pdcData['newStatus'] == 2) {
                                        $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                        $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                        $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']) * -1;
                                        $data['documentLocalAmount'] = \Helper::roundValue($currencyConvertionData['localAmount']) * -1;
                                        $data['documentRptAmount'] = \Helper::roundValue($currencyConvertionData['reportingAmount']) * -1;
                                        $data['timestamp'] = \Helper::currentDateTime();
                                        array_push($finalData, $data);

                                        $data['chartOfAccountSystemID'] = $masterData->bank->chartOfAccountSystemID;
                                        $data['glCode'] = $masterData->bank->glCodeLinked;
                                        $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']);
                                        $data['documentLocalAmount'] = \Helper::roundValue($currencyConvertionData['localAmount']);
                                        $data['documentRptAmount'] = \Helper::roundValue($currencyConvertionData['reportingAmount']);
                                        $data['timestamp'] = \Helper::currentDateTime();
                                        array_push($finalData, $data);
                                    }
                                }
                            }

                            if ($masterData->invoiceType == 3) { //Direct Payment
                                if(ExchangeSetupConfig::isMasterDocumentExchageRateChanged($masterData)) {
                                    $masterLocal = ($masterModel['pdcAmount']/$masterData->localCurrencyER);
                                    $masterRpt = ($masterModel['pdcAmount']/$masterData->companyRptCurrencyER);
                                }else {
                                    $currencyConvertionData = \Helper::currencyConversion($masterData->companySystemID, $masterData->BPVbankCurrency, $masterData->BPVbankCurrency, $pdcData['amount']);
                                    $masterLocal = $currencyConvertionData['localAmount'];
                                    $masterRpt = $currencyConvertionData['reportingAmount'];
                                }
                                $data['serviceLineSystemID'] = 24;
                                $data['serviceLineCode'] = 'X';
                                $data['glAccountType'] = 'BS';
                                $data['glAccountTypeID'] = 1;
                                $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;
                                $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                $data['timestamp'] = \Helper::currentDateTime();

                                if ($pdcData['newStatus'] == 1) {
                                    $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                    $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                    $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']);
                                    $data['documentLocalAmount'] = \Helper::roundValue($masterLocal);
                                    $data['documentRptAmount'] = \Helper::roundValue($masterRpt);
                                    array_push($finalData, $data);

                                    $data['chartOfAccountSystemID'] = $masterData->bank->chartOfAccountSystemID;
                                    $data['glCode'] = $masterData->bank->glCodeLinked;
                                    $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']) * -1;
                                    $data['documentLocalAmount'] = \Helper::roundValue($masterLocal) * -1;
                                    $data['documentRptAmount'] = \Helper::roundValue($masterRpt) * -1;
                                    array_push($finalData, $data);
                                }

                                if ($pdcData['newStatus'] == 2) {
                                    $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                    $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-payable-account");
                                    $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']) * -1;
                                    $data['documentLocalAmount'] = \Helper::roundValue($masterLocal) * -1;
                                    $data['documentRptAmount'] = \Helper::roundValue($masterRpt) * -1;
                                    array_push($finalData, $data);

                                    $data['chartOfAccountSystemID'] = $masterData->bank->chartOfAccountSystemID;
                                    $data['glCode'] = $masterData->bank->glCodeLinked;
                                    $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']);
                                    $data['documentLocalAmount'] = \Helper::roundValue($masterLocal);
                                    $data['documentRptAmount'] = \Helper::roundValue($masterRpt);
                                    array_push($finalData, $data);
                                }
                            }

                            $masterModel['pdcFlag'] = true;
                            $masterModel['pdcAmount'] = $pdcData['amount'];
                            $masterModel['pdcDate'] = $pdcData['date'];
                            $masterModel['pdcChequeDate'] = $pdcData['chequeDate'];
                            $masterModel['pdcChequeNo'] = $pdcData['chequeNo'];
                            $masterModel['pdcID'] = $pdcData['id'];
                            if ($pdcData['newStatus'] == 1) {
                                $bankLedger = BankLedgerInsert::dispatch($masterModel);
                            } else if ($pdcData['newStatus'] == 2) {
                                $masterModel['reversePdc'] = true;
                                $bankLedger = BankLedgerInsert::dispatch($masterModel);
                            }
                        }
                        break;
                    case 21: // BRV - Customer Receive Payment
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

                        if (isset($pdcData['date'])) {
                            $masterDocumentDate = Carbon::parse($pdcData['date']);
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
                            $data['chequeNumber'] = $pdcLogData ? $pdcLogData->chequeNo : null;
                            $data['pdcID'] = $pdcLogData ? $pdcLogData->id : null;
                            $data['documentType'] = $masterData->documentType;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();


                            if ($masterData->documentType == 13) { //Customer Receive Payment
                                if ($cpd) {
                                    $currencyConvertionData = \Helper::currencyConversion($masterData->companySystemID, $masterData->bankCurrency, $masterData->bankCurrency, $pdcData['amount']);
                                    $data['serviceLineSystemID'] = 24;
                                    $data['serviceLineCode'] = 'X';
                                    $data['glAccountType'] = 'BS';
                                    $data['glAccountTypeID'] = 1;
                                    $data['documentTransCurrencyID'] = $masterData->bankCurrency;
                                    $data['documentTransCurrencyER'] = $masterData->bankCurrencyER;
                                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                    $data['timestamp'] = \Helper::currentDateTime();


                                    if ($pdcData['newStatus'] == 1) {
                                        $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-receivable-account");
                                        $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-receivable-account");
                                        $data['documentTransAmount'] = abs(\Helper::roundValue($pdcData['amount'])) * -1;
                                        $data['documentLocalAmount'] = abs(\Helper::roundValue($currencyConvertionData['localAmount'])) * -1;
                                        $data['documentRptAmount'] = abs(\Helper::roundValue($currencyConvertionData['reportingAmount'])) * -1;
                                        array_push($finalData, $data);

                                        $data['chartOfAccountSystemID'] = $masterData->bank->chartOfAccountSystemID;
                                        $data['glCode'] = $masterData->bank->glCodeLinked;
                                        $data['documentTransAmount'] = abs(\Helper::roundValue($pdcData['amount']));
                                        $data['documentLocalAmount'] = abs(\Helper::roundValue($currencyConvertionData['localAmount']));
                                        $data['documentRptAmount'] = abs(\Helper::roundValue($currencyConvertionData['reportingAmount']));
                                        array_push($finalData, $data);
                                    }

                                    if ($pdcData['newStatus'] == 2) {
                                        $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-receivable-account");
                                        $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-receivable-account");
                                        $data['documentTransAmount'] = abs(\Helper::roundValue($pdcData['amount']));
                                        $data['documentLocalAmount'] = abs(\Helper::roundValue($currencyConvertionData['localAmount']));
                                        $data['documentRptAmount'] = abs(\Helper::roundValue($currencyConvertionData['reportingAmount']));
                                        array_push($finalData, $data);

                                        $data['chartOfAccountSystemID'] = $masterData->bank->chartOfAccountSystemID;
                                        $data['glCode'] = $masterData->bank->glCodeLinked;
                                        $data['documentTransAmount'] = abs(\Helper::roundValue($pdcData['amount'])) * -1;
                                        $data['documentLocalAmount'] = abs(\Helper::roundValue($currencyConvertionData['localAmount'])) * -1;
                                        $data['documentRptAmount'] = abs(\Helper::roundValue($currencyConvertionData['reportingAmount'])) * -1;
                                        array_push($finalData, $data);
                                    }

                                }
                            }

                            if ($masterData->documentType == 14 || $masterData->documentType == 15) { //Direct Receipt & advance receipt
                                if ($totaldd) {
                                    $currencyConvertionData = \Helper::currencyConversion($masterData->companySystemID, $masterData->custTransactionCurrencyID, $masterData->custTransactionCurrencyID, $pdcData['amount']);
                                    if($totaldd->transAmount == 0){
                                        $totaldd = $totalAdv;
                                        $data['serviceLineSystemID'] = 24;
                                        $data['serviceLineCode'] = 'X';
                                    }else{
                                        $data['serviceLineSystemID'] = $totaldd->serviceLineSystemID;
                                        $data['serviceLineCode'] = $totaldd->serviceLineCode;
                                    }


                                    $data['glAccountType'] = 'BS';
                                    $data['glAccountTypeID'] = 1;
                                    $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                    $data['timestamp'] = \Helper::currentDateTime();

                                    if ($pdcData['newStatus'] == 1) {
                                        $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-receivable-account");
                                        $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-receivable-account");
                                        $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']) * -1;
                                        $data['documentLocalAmount'] = \Helper::roundValue($currencyConvertionData['localAmount']) * -1;
                                        $data['documentRptAmount'] = \Helper::roundValue($currencyConvertionData['reportingAmount']) * -1;
                                        array_push($finalData, $data);

                                        $data['chartOfAccountSystemID'] = $masterData->bank->chartOfAccountSystemID;
                                        $data['glCode'] = $masterData->bank->glCodeLinked;
                                        $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']);
                                        $data['documentLocalAmount'] = \Helper::roundValue($currencyConvertionData['localAmount']);
                                        $data['documentRptAmount'] = \Helper::roundValue($currencyConvertionData['reportingAmount']);
                                        array_push($finalData, $data);
                                    }

                                    if ($pdcData['newStatus'] == 2) {
                                        $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-receivable-account");
                                        $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "pdc-receivable-account");
                                        $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']);
                                        $data['documentLocalAmount'] = \Helper::roundValue($currencyConvertionData['localAmount']);
                                        $data['documentRptAmount'] = \Helper::roundValue($currencyConvertionData['reportingAmount']);
                                        array_push($finalData, $data);

                                        $data['chartOfAccountSystemID'] = $masterData->bank->chartOfAccountSystemID;
                                        $data['glCode'] = $masterData->bank->glCodeLinked;
                                        $data['documentTransAmount'] = \Helper::roundValue($pdcData['amount']) * -1;
                                        $data['documentLocalAmount'] = \Helper::roundValue($currencyConvertionData['localAmount']) * -1;
                                        $data['documentRptAmount'] = \Helper::roundValue($currencyConvertionData['reportingAmount']) * -1;
                                        array_push($finalData, $data);
                                    }
                                }
                            }

                            if ($pdcData['newStatus'] == 2 || $pdcData['newStatus'] == 1) {
                                $custReceivePayment = CustomerReceivePayment::with('finance_period_by')->find($masterModel["autoID"]);
                                if ($custReceivePayment) {
                                    $masterDocumentDate = date('Y-m-d H:i:s');
                                    if ($custReceivePayment->finance_period_by->isActive == -1) {
                                        $masterDocumentDate = $custReceivePayment->custPaymentReceiveDate;
                                    }

                                    if (isset($pdcData['date'])) {
                                        $masterDocumentDate = Carbon::parse($pdcData['date']);
                                    }

                                    $currencyConvertionData = \Helper::currencyConversion($custReceivePayment->companySystemID, $custReceivePayment->custTransactionCurrencyID, $custReceivePayment->custTransactionCurrencyID, $pdcData['amount']);

                                    $pdcAmount = ($custReceivePayment->bankAmount > 0) ?  $pdcData['amount'] : $pdcData['amount'] * -1;
                                    $pdcAmountLocalAmount = ($custReceivePayment->bankAmount > 0) ?  $currencyConvertionData['localAmount'] : $currencyConvertionData['localAmount'] * -1;
                                    $pdcAmountReportingAmount = ($custReceivePayment->bankAmount > 0) ?  $currencyConvertionData['reportingAmount'] : $currencyConvertionData['reportingAmount'] * -1;

                                    $data['companySystemID'] = $custReceivePayment->companySystemID;
                                    $data['companyID'] = $custReceivePayment->companyID;
                                    $data['documentSystemID'] = $custReceivePayment->documentSystemID;
                                    $data['documentID'] = $custReceivePayment->documentID;
                                    $data['documentSystemCode'] = $custReceivePayment->custReceivePaymentAutoID;
                                    $data['documentCode'] = $custReceivePayment->custPaymentReceiveCode;
                                    $data['documentDate'] = $custReceivePayment->custPaymentReceiveDate;
                                    $data['postedDate'] = $masterDocumentDate;
                                    $data['documentNarration'] = $custReceivePayment->narration;
                                    $data['bankID'] = $custReceivePayment->bankID;
                                    $data['bankAccountID'] = $custReceivePayment->bankAccount;
                                    $data['bankCurrency'] = $custReceivePayment->bankCurrency;
                                    $data['bankCurrencyER'] = $custReceivePayment->bankCurrencyER;
                                    $data['documentChequeNo'] = $pdcData['chequeNo'];
                                    $data['documentChequeDate'] = Carbon::parse($pdcData['chequeDate']);
                                    $data['payeeID'] = $custReceivePayment->customerID;
                                    $data['pdcID'] = $pdcLogData ? $pdcLogData->id : null;
                                    
                                    $payee = CustomerMaster::find($custReceivePayment->customerID);
                                    if ($payee) {
                                        $data['payeeCode'] = $payee->CutomerCode;
                                        $data['payeeName'] = $payee->CustomerName;
                                    }
                                    $data['payeeGLCodeID'] = $custReceivePayment->customerGLCodeSystemID;
                                    $data['payeeGLCode'] = $custReceivePayment->customerGLCode;
                                    $data['supplierTransCurrencyID'] = $custReceivePayment->custTransactionCurrencyID;
                                    $data['supplierTransCurrencyER'] = $custReceivePayment->custTransactionCurrencyER;
                                    $data['localCurrencyID'] = $custReceivePayment->localCurrencyID;
                                    $data['localCurrencyER'] = $custReceivePayment->localCurrencyER;
                                    $data['companyRptCurrencyID'] = $custReceivePayment->companyRptCurrencyID;
                                    $data['companyRptCurrencyER'] = $custReceivePayment->companyRptCurrencyER;
                                    $data['payAmountBank'] = ($pdcData['newStatus'] == 1) ? $pdcAmount : $pdcAmount * -1;
                                    $data['payAmountSuppTrans'] = ($pdcData['newStatus'] == 1) ? $pdcAmount : $pdcAmount * -1;
                                    $data['payAmountCompLocal'] = ($pdcData['newStatus'] == 1) ? $pdcAmountLocalAmount : $pdcAmountLocalAmount * -1;
                                    $data['payAmountCompRpt'] = ($pdcData['newStatus'] == 1) ? $pdcAmountReportingAmount : $pdcAmountReportingAmount * -1;
                                    $data['invoiceType'] = $custReceivePayment->documentType;
                                    $data['chequePaymentYN'] = -1;

                                    if ($custReceivePayment->trsCollectedYN == 0) {
                                        $data['trsCollectedYN'] = -1;
                                    } else {
                                        $data['trsCollectedYN'] = $custReceivePayment->trsCollectedYN;
                                    }

                                    $data['trsCollectedByEmpSystemID'] = $custReceivePayment->trsCollectedByEmpSystemID;
                                    $data['trsCollectedByEmpID'] = $custReceivePayment->trsCollectedByEmpID;
                                    $data['trsCollectedByEmpName'] = $custReceivePayment->trsCollectedByEmpName;
                                    $data['trsCollectedDate'] = $custReceivePayment->trsCollectedDate;

                                    $data['createdUserID'] = $custReceivePayment->createdUserID;
                                    $data['createdUserSystemID'] = $custReceivePayment->createdUserSystemID;
                                    $data['createdPcID'] = gethostname();
                                    $data['timestamp'] = NOW();
                                    BankLedger::create($data);
                                }
                            }
                        }
                        break;
                    default:
                        Log::warning('Document ID not found ' . date('H:i:s'));
                }

                if ($finalData) {
                    //$generalLedgerInsert = GeneralLedger::insert($finalData);
                    foreach ($finalData as $data) {
                        GeneralLedger::create($data);
                    }
                    $generalLedgerInsert = true;

                    if ($generalLedgerInsert) {
                        // updating posted date in relevant documents

                        // getting general ledger document date
                        $glDocumentDate = GeneralLedger::selectRaw('documentDate')
                            ->where('documentSystemID', $masterModel["documentSystemID"])
                            ->where('companySystemID', $masterModel["companySystemID"])
                            ->where('documentSystemCode', $masterModel["autoID"])
                            ->first();

                        switch ($masterModel["documentSystemID"]) {
                            case 4: // Payment Voucher
                                $documentUpdateData = PaySupplierInvoiceMaster::find($masterModel["autoID"]);

                                if ($glDocumentDate) {
                                    $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                                    $documentUpdateData->save();
                                }
                                break;
                            case 21: //  Customer Receipt Voucher
                                $documentUpdateData = CustomerReceivePayment::find($masterModel["autoID"]);

                                if ($glDocumentDate) {
                                    $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                                    $documentUpdateData->save();
                                }
                                break;
                            default:
                                Log::warning('Posted date document id not found ' . date('H:i:s'));
                        }
                    }
                    DB::commit();
                }

            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
            }
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

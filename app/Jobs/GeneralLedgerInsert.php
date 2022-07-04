<?php

namespace App\Jobs;

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

class GeneralLedgerInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $masterModel;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($masterModel)
    {
        $this->masterModel = $masterModel;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/general_ledger_jobs.log');
        Log::info('---- GL  Start-----' . date('H:i:s'));
        $masterModel = $this->masterModel;

        if (!empty($masterModel)) {
            DB::beginTransaction();
            try {
                $data = [];
                $taxLedgerData = [];
                $finalData = [];
                $empID = Employee::find($masterModel['employeeSystemID']);
                switch ($masterModel["documentSystemID"]) {
                    case 3: // GRV
                        $masterData = GRVMaster::with(['details' => function ($query) {
                            $query->selectRaw("SUM(GRVcostPerUnitLocalCur*noQty) as localAmount, SUM(GRVcostPerUnitComRptCur*noQty) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount ,grvAutoID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER");
                        }])->find($masterModel["autoID"]);
                        //get balansheet account
                        $bs = GRVDetails::selectRaw("SUM(landingCost_LocalCur*noQty) as localAmount, SUM(landingCost_RptCur*noQty) as rptAmount,SUM(landingCost_TransCur*noQty) as transAmount,financeGLcodebBSSystemID,financeGLcodebBS,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER")->WHERE('grvAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->get();

                        //get pnl account
                        $pl = GRVDetails::selectRaw("SUM(landingCost_LocalCur*noQty) as localAmount, SUM(landingCost_RptCur*noQty) as rptAmount,SUM(landingCost_TransCur*noQty) as transAmount,financeGLcodePLSystemID,financeGLcodePL,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER")->WHERE('grvAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->where('financeGLcodePLSystemID', '>', 0)->WHERE('includePLForGRVYN', -1)->groupBy('financeGLcodePLSystemID')->get();

                        //unbilledGRV for logistic
                        $unbilledGRV = PoAdvancePayment::selectRaw("erp_grvmaster.companySystemID,erp_grvmaster.companyID,
                                            erp_purchaseorderadvpayment.supplierID,poID as purchaseOrderID,
                                            erp_purchaseorderadvpayment.grvAutoID,erp_grvmaster.grvDate,
                                            erp_purchaseorderadvpayment.currencyID as supplierTransactionCurrencyID,
                                            '1' as supplierTransactionER,erp_purchaseordermaster.companyReportingCurrencyID, 
                                            ROUND((SUM(reqAmountTransCur_amount + erp_purchaseorderadvpayment.VATAmount)/SUM(reqAmountInPORptCur + erp_purchaseorderadvpayment.VATAmountRpt)),7) as companyReportingER,
                                            erp_purchaseordermaster.localCurrencyID,
                                            ROUND((SUM(reqAmountTransCur_amount + erp_purchaseorderadvpayment.VATAmount)/SUM(reqAmountInPOLocalCur + erp_purchaseorderadvpayment.VATAmountLocal)),7) as localCurrencyER,
                                            SUM(reqAmountTransCur_amount) as transAmount,
                                            SUM(reqAmountInPOLocalCur) as localAmount, 
                                            SUM(reqAmountInPORptCur) as rptAmount,
                                            erp_purchaseorderadvpayment.grvAutoID, 
                                            erp_purchaseorderadvpayment.poID,
                                            erp_purchaseorderadvpayment.VATPercentage,
                                            erp_purchaseorderadvpayment.reqAmount,
                                            erp_purchaseorderadvpayment.VATAmount,
                                            erp_purchaseorderadvpayment.VATAmountLocal,
                                            erp_purchaseorderadvpayment.reqAmountInPOLocalCur,
                                            erp_purchaseorderadvpayment.VATAmountRpt,
                                            erp_purchaseorderadvpayment.reqAmountInPORptCur,
                                            erp_purchaseorderadvpayment.addVatOnPO,
                                            'POG' as grvType,
                                            NOW() as timeStamp,erp_purchaseorderadvpayment.UnbilledGRVAccountSystemID,
                                            erp_purchaseorderadvpayment.UnbilledGRVAccount")
                            ->leftJoin('erp_grvmaster', 'erp_purchaseorderadvpayment.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
                            ->leftJoin('erp_purchaseordermaster', 'erp_purchaseorderadvpayment.poID', '=', 'erp_purchaseordermaster.purchaseOrderID')
                            ->where('erp_purchaseorderadvpayment.grvAutoID', $masterModel["autoID"])
                            ->groupBy('erp_purchaseorderadvpayment.UnbilledGRVAccountSystemID', 'erp_purchaseorderadvpayment.supplierID')
                            ->get();


                        $unbilledGRVVATAddVatOnPO = TaxService::poLogisticVATDistributionForGRV($masterModel["autoID"]);
                        $vatDetails = TaxService::processGrvVAT($masterModel["autoID"]);

                        Log::info('Total Logistic VAT');
                        Log::info($unbilledGRVVATAddVatOnPO);

                        if ($masterData) {

                            $unbilledTransVATAmount =  $unbilledGRVVATAddVatOnPO['vatOnPOTotalAmountTrans'];
                            $unbilledLocalVATAmount =  $unbilledGRVVATAddVatOnPO['vatOnPOTotalAmountLocal'];
                            $unbilledRptVATAmount   =  $unbilledGRVVATAddVatOnPO['vatOnPOTotalAmountRpt'];

                            $transVATAmount = isset($vatDetails['masterVATTrans']) ? $vatDetails['masterVATTrans'] : 0;
                            $localVATAmount = isset($vatDetails['masterVATLocal']) ? $vatDetails['masterVATLocal'] : 0;
                            $rptVATAmount = isset($vatDetails['masterVATRpt']) ? $vatDetails['masterVATRpt'] : 0;

                            $exemptVATTrans = isset($vatDetails['exemptVATTrans']) ? $vatDetails['exemptVATTrans'] : 0;
                            $exemptVATRpt = isset($vatDetails['exemptVATRpt']) ? $vatDetails['exemptVATRpt'] : 0;
                            $exemptVATLocal = isset($vatDetails['exemptVATLocal']) ? $vatDetails['exemptVATLocal'] : 0;

                            $valEligible = TaxService::checkGRVVATEligible($masterData->companySystemID, $masterData->supplierID);
                            $rcmActivated = TaxService::isGRVRCMActivation($masterModel["autoID"]);
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                            $data['serviceLineCode'] = $masterData->serviceLineCode;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->grvPrimaryCode;
                            $data['documentDate'] = date('Y-m-d H:i:s');
                            $data['documentYear'] = \Helper::dateYear($masterData->grvDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterData->grvDate);
                            $data['chartOfAccountSystemID'] = $masterData->UnbilledGRVAccountSystemID;
                            $data['glCode'] = $masterData->UnbilledGRVAccount;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] =ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            $data['documentConfirmedDate'] = $masterData->grvConfirmedDate;
                            $data['documentConfirmedBy'] = $masterData->grvConfirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->grvConfirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->grvNarration;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = $masterData->supplierID;
                            $data['documentTransCurrencyID'] = $masterData->details[0]->supplierTransactionCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->details[0]->supplierTransactionER;
                            $data['documentTransAmount'] = \Helper::roundValue((($valEligible && !$rcmActivated) ? $masterData->details[0]->transAmount + $transVATAmount : ($masterData->details[0]->transAmount - $exemptVATTrans)) * -1);
                            $data['documentLocalCurrencyID'] = $masterData->details[0]->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->details[0]->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue((($valEligible && !$rcmActivated) ? $masterData->details[0]->localAmount + $localVATAmount : ($masterData->details[0]->localAmount - $exemptVATLocal)) * -1);
                            $data['documentRptCurrencyID'] = $masterData->details[0]->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->details[0]->companyReportingER;
                            $data['documentRptAmount'] = \Helper::roundValue((($valEligible && !$rcmActivated) ? $masterData->details[0]->rptAmount + $rptVATAmount : ($masterData->details[0]->rptAmount - $exemptVATRpt)) * -1);
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);

                             if ((($valEligible || TaxService::isGRVRCMActivation($masterModel["autoID"])) && ($vatDetails['masterVATTrans'] || $exemptVATTrans)) || ($unbilledTransVATAmount > 0)) {
                                Log::info('Inside the Vat Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                $taxData = TaxService::getInputVATTransferGLAccount($masterData->companySystemID);

                                if ($vatDetails['masterVATTrans'] > 0 || $unbilledTransVATAmount > 0) {
                                    if (!empty($taxData)) {
                                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxData->inputVatTransferGLAccountAutoID)
                                            ->where('companySystemID', $masterData->companySystemID)
                                            ->first();

                                        if (!empty($chartOfAccountData)) {
                                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            $data['glCode'] = $chartOfAccountData->AccountCode;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);


                                            $data['documentTransAmount'] = \Helper::roundValue($vatDetails['masterVATTrans'] + $unbilledTransVATAmount);
                                            $data['documentLocalAmount'] = \Helper::roundValue($vatDetails['masterVATLocal'] + $unbilledLocalVATAmount);
                                            $data['documentRptAmount'] = \Helper::roundValue($vatDetails['masterVATRpt'] + $unbilledRptVATAmount);

                                            array_push($finalData, $data);

                                            $taxLedgerData['inputVatTransferAccountID'] = $chartOfAccountData->chartOfAccountSystemID;

                                            Log::info('Inside the Vat Entry InputVATTransferGLAccount Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        } else {
                                            Log::info('GRV VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                            Log::info('Input Vat Transfer GL Account not assigned to company' . date('H:i:s'));
                                        }
                                    } else {
                                        Log::info('GRV VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Input Vat Transfer GL Account not configured' . date('H:i:s'));
                                    }
                                }


                                if(TaxService::isGRVRCMActivation($masterModel["autoID"])){

                                    $taxDataOutputTransfer = TaxService::getOutputVATTransferGLAccount($masterData->companySystemID);
                                    Log::info('Inside the Vat Entry OutputVATTransferGLAccoun Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                    if (!empty($taxDataOutputTransfer)) {
                                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxDataOutputTransfer->outputVatTransferGLAccountAutoID)
                                            ->where('companySystemID', $masterData->companySystemID)
                                            ->first();

                                        if (!empty($chartOfAccountData)) {
                                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            $data['glCode'] = $chartOfAccountData->AccountCode;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                            $data['documentTransAmount'] = \Helper::roundValue(($transVATAmount + $exemptVATTrans)) * -1;
                                            $data['documentLocalAmount'] = \Helper::roundValue(($localVATAmount + $exemptVATLocal)) * -1;
                                            $data['documentRptAmount'] = \Helper::roundValue(($rptVATAmount + $exemptVATRpt)) * -1;
                                            $data['timestamp'] = \Helper::currentDateTime();
                                            array_push($finalData, $data);

                                            $taxLedgerData['outputVatTransferGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            Log::info('Inside the Vat Entry OutVATTransferGLAccount Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        } else {
                                            Log::info('GRV VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                            Log::info('Output Vat Transfer GL Account not assigned to company' . date('H:i:s'));
                                        }
                                    } else {
                                        Log::info('GRV VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Output Vat Transfer GL Account not configured' . date('H:i:s'));
                                    }
                                }

                            }

                            if ($bs) {
                                foreach ($bs as $val) {

                                    $transBSVAT = isset($vatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['transVATAmount']) ? $vatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['transVATAmount'] : 0;
                                    $rptBSVAT = isset($vatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['rptVATAmount']) ? $vatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['rptVATAmount'] : 0;
                                    $localBSVAT = isset($vatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['localVATAmount']) ? $vatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['localVATAmount'] : 0;

                                    $exemptVATTransAmount = isset($vatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATTransAmount']) ? $vatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATTransAmount'] : 0;
                                    $exemptVATLocalAmount = isset($vatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATLocalAmount']) ? $vatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATLocalAmount'] : 0;
                                    $exemptVATRptAmount = isset($vatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATRptAmount']) ? $vatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATRptAmount'] : 0;

                                    $data['chartOfAccountSystemID'] = $val->financeGLcodebBSSystemID;
                                    $data['glCode'] = $val->financeGLcodebBS;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                                    $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount) + $transBSVAT + $exemptVATTransAmount);

                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount) + $localBSVAT + $exemptVATLocalAmount);

                                    $data['documentRptCurrencyID'] = $val->companyReportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $val->companyReportingER;
                                    $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount) + $rptBSVAT + $exemptVATRptAmount);
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }

                            if ($pl) {
                                foreach ($pl as $val) {

                                    $transPLVAT = isset($vatDetails['plVAT'][$val->financeGLcodePLSystemID]['transVATAmount']) ? $vatDetails['plVAT'][$val->financeGLcodePLSystemID]['transVATAmount'] : 0;
                                    $rptPLVAT = isset($vatDetails['plVAT'][$val->financeGLcodePLSystemID]['rptVATAmount']) ? $vatDetails['plVAT'][$val->financeGLcodePLSystemID]['rptVATAmount'] : 0;
                                    $localPLVAT = isset($vatDetails['plVAT'][$val->financeGLcodePLSystemID]['localVATAmount']) ? $vatDetails['plVAT'][$val->financeGLcodePLSystemID]['localVATAmount'] : 0;

                                    $exemptVATTransAmount = isset($vatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATTransAmount']) ? $vatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATTransAmount'] : 0;
                                    $exemptVATLocalAmount = isset($vatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATLocalAmount']) ? $vatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATLocalAmount'] : 0;
                                    $exemptVATRptAmount = isset($vatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATRptAmount']) ? $vatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATRptAmount'] : 0;

                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['glCode'] = $val->financeGLcodePL;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                                    $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount) + $transPLVAT + $exemptVATTransAmount);

                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount) + $localPLVAT + $exemptVATLocalAmount);

                                    $data['documentRptCurrencyID'] = $val->companyReportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $val->companyReportingER;
                                    $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount) + $rptPLVAT + $exemptVATRptAmount);
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }

                            $unbilledTransVATAmount = 0;
                            $unbilledLocalVATAmount = 0;
                            $unbilledRptVATAmount = 0;

                            if ($unbilledGRV) {
                                foreach ($unbilledGRV as $val) {

                                    //$vatData = TaxService::poLogisticForLineWise($val);
                                    $vatData = TaxService::poLogisticVATDistributionForGRV($masterModel["autoID"],0,$val->supplierID);

                                    Log::info('$unbilledGRV item');
                                    Log::info($val);
                                    Log::info('$unbilledGRV, VAtT');
                                    Log::info($vatData);

                                    $data['documentTransCurrencyID'] = $val->currencyID;
                                    $data['documentTransCurrencyID'] = 1;
                                    $data['supplierCodeSystem'] = $val->supplierID;
                                    $data['chartOfAccountSystemID'] = $val->UnbilledGRVAccountSystemID;
                                    $data['glCode'] = $val->UnbilledGRVAccount;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                                    //$data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount) * -1);
                                    $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount + $vatData['vatOnPOTotalAmountTrans']) * -1);

                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                    //$data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount) * -1);
                                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount + $vatData['vatOnPOTotalAmountLocal']) * -1);

                                    $data['documentRptCurrencyID'] = $val->companyReportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $val->companyReportingER;
                                    ///$data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount) * -1);
                                    $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount + $vatData['vatOnPOTotalAmountRpt']) * -1);
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        break;
                    case 8: // MI - Material issue
                        $masterData = ItemIssueMaster::find($masterModel["autoID"]);
                        //get balansheet account
                        $bs = ItemIssueDetails::selectRaw("SUM(qtyIssuedDefaultMeasure * issueCostLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * issueCostRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,reportingCurrencyID")->WHERE('itemIssueAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->first();
                        //get pnl account
                        $pl = ItemIssueDetails::selectRaw("SUM(qtyIssuedDefaultMeasure * issueCostLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * issueCostRpt) as rptAmount,financeGLcodePLSystemID,financeGLcodePL,localCurrencyID,reportingCurrencyID")->WHERE('itemIssueAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->where('financeGLcodePLSystemID', '>', 0)->groupBy('financeGLcodePLSystemID')->get();
                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                            $data['serviceLineCode'] = $masterData->serviceLineCode;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->itemIssueCode;
                            $data['documentDate'] = date('Y-m-d H:i:s');
                            $data['documentYear'] = \Helper::dateYear($masterData->issueDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterData->issueDate);
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->comment;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = 0;
                            $data['documentTransCurrencyID'] = 0;
                            $data['documentTransCurrencyER'] = 0;
                            $data['documentTransAmount'] = 0;
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            if ($bs) {
                                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                $data['glCode'] = $bs->financeGLcodebBS;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentLocalCurrencyID'] = $bs->localCurrencyID;
                                $data['documentLocalCurrencyER'] = 1;
                                $data['documentLocalAmount'] = ABS($bs->localAmount) * -1;
                                $data['documentRptCurrencyID'] = $bs->reportingCurrencyID;
                                $data['documentRptCurrencyER'] = 1;
                                $data['documentRptAmount'] = ABS($bs->rptAmount) * -1;
                                $data['timestamp'] = \Helper::currentDateTime();
                                array_push($finalData, $data);
                            }

                            if ($pl) {
                                foreach ($pl as $val) {
                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['glCode'] = $val->financeGLcodePL;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = 1;
                                    $data['documentLocalAmount'] = ABS($val->localAmount);
                                    $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                                    $data['documentRptCurrencyER'] = 1;
                                    $data['documentRptAmount'] = ABS($val->rptAmount);
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        break;
                    case 12: // SR - Material Return
                        $masterData = ItemReturnMaster::find($masterModel["autoID"]);
                        //get balansheet account
                        $bs = ItemReturnDetails::selectRaw("SUM(qtyIssuedDefaultMeasure* unitCostLocal) as localAmount, SUM(qtyIssuedDefaultMeasure* unitCostRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,reportingCurrencyID")->WHERE('itemReturnAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->first();
                        //get pnl account
                        $pl = ItemReturnDetails::selectRaw("SUM(qtyIssuedDefaultMeasure* unitCostLocal) as localAmount, SUM(qtyIssuedDefaultMeasure* unitCostRpt) as rptAmount,financeGLcodePLSystemID,financeGLcodePL,localCurrencyID,reportingCurrencyID")->WHERE('itemReturnAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->where('financeGLcodePLSystemID', '>', 0)->groupBy('financeGLcodePLSystemID')->get();
                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                            $data['serviceLineCode'] = $masterData->serviceLineCode;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->itemReturnCode;
                            $data['documentDate'] = date('Y-m-d H:i:s');
                            $data['documentYear'] = \Helper::dateYear($masterData->ReturnDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterData->ReturnDate);
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->comment;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = 0;
                            $data['documentTransCurrencyID'] = 0;
                            $data['documentTransCurrencyER'] = 0;
                            $data['documentTransAmount'] = 0;
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            if ($bs) {
                                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                $data['glCode'] = $bs->financeGLcodebBS;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentLocalCurrencyID'] = $bs->localCurrencyID;
                                $data['documentLocalCurrencyER'] = 1;
                                $data['documentLocalAmount'] = ABS($bs->localAmount);
                                $data['documentRptCurrencyID'] = $bs->reportingCurrencyID;
                                $data['documentRptCurrencyER'] = 1;
                                $data['documentRptAmount'] = ABS($bs->rptAmount);
                                $data['timestamp'] = \Helper::currentDateTime();
                                array_push($finalData, $data);
                            }

                            if ($pl) {
                                foreach ($pl as $val) {
                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['glCode'] = $val->financeGLcodePL;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = 1;
                                    $data['documentLocalAmount'] = ABS($val->localAmount) * -1;
                                    $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                                    $data['documentRptCurrencyER'] = 1;
                                    $data['documentRptAmount'] = ABS($val->rptAmount) * -1;
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        break;
                    case 13: // ST - Stock Transfer
                        $masterData = StockTransfer::find($masterModel["autoID"]);
                        //get balansheet account
                        $bs = StockTransferDetails::selectRaw("SUM(qty* unitCostLocal) as localAmount, SUM(qty* unitCostRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,reportingCurrencyID")->WHERE('stockTransferAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->get();
                        //get pnl account
                        $pl = StockTransferDetails::selectRaw("SUM(qty* unitCostLocal) as localAmount, SUM(qty* unitCostRpt) as rptAmount,localCurrencyID,reportingCurrencyID")->WHERE('stockTransferAutoID', $masterModel["autoID"])->first();
                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                            $data['serviceLineCode'] = $masterData->serviceLineCode;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->stockTransferCode;
                            $data['documentDate'] = date('Y-m-d H:i:s');
                            $data['documentYear'] = \Helper::dateYear($masterData->tranferDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterData->tranferDate);
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->comment;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = 0;
                            $data['documentTransCurrencyID'] = 0;
                            $data['documentTransCurrencyER'] = 0;
                            $data['documentTransAmount'] = 0;
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            if ($bs) {
                                foreach ($bs as $keyBs => $valueBs) {
                                    $data['chartOfAccountSystemID'] = $valueBs->financeGLcodebBSSystemID;
                                    $data['glCode'] = $valueBs->financeGLcodebBS;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentLocalCurrencyID'] = $valueBs->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = 1;
                                    $data['documentLocalAmount'] = ABS($valueBs->localAmount) * -1;
                                    $data['documentRptCurrencyID'] = $valueBs->reportingCurrencyID;
                                    $data['documentRptCurrencyER'] = 1;
                                    $data['documentRptAmount'] = ABS($valueBs->rptAmount) * -1;
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }

                            if ($pl) {
                                if ($masterData->interCompanyTransferYN == -1) {
                                    $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 1);
                                    $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 1);
                                } else {
                                    $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 2);
                                    $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 2);
                                }
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentLocalCurrencyID'] = $pl->localCurrencyID;
                                $data['documentLocalCurrencyER'] = 1;
                                $data['documentLocalAmount'] = ABS($pl->localAmount);
                                $data['documentRptCurrencyID'] = $pl->reportingCurrencyID;
                                $data['documentRptCurrencyER'] = 1;
                                $data['documentRptAmount'] = ABS($pl->rptAmount);
                                $data['timestamp'] = \Helper::currentDateTime();
                                array_push($finalData, $data);
                            }
                        }
                        break;
                    case 10: // RS - Stock Receive
                        $masterData = StockReceive::find($masterModel["autoID"]);
                        //get balansheet account
                        $bs = StockReceiveDetails::selectRaw("SUM(qty* unitCostLocal) as localAmount, SUM(qty* unitCostRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,reportingCurrencyID")->WHERE('stockReceiveAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->get();
                        //get pnl account
                        $pl = StockReceiveDetails::selectRaw("SUM(qty* unitCostLocal) as localAmount, SUM(qty* unitCostRpt) as rptAmount,localCurrencyID,reportingCurrencyID")->WHERE('stockReceiveAutoID', $masterModel["autoID"])->first();
                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                            $data['serviceLineCode'] = $masterData->serviceLineCode;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->stockReceiveCode;
                            $data['documentDate'] = date('Y-m-d H:i:s');
                            $data['documentYear'] = \Helper::dateYear($masterData->receivedDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterData->receivedDate);
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->comment;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = 0;
                            $data['documentTransCurrencyID'] = 0;
                            $data['documentTransCurrencyER'] = 0;
                            $data['documentTransAmount'] = 0;
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            if ($bs) {
                                foreach ($bs as $keyBs => $valueBs) {
                                    $data['chartOfAccountSystemID'] = $valueBs->financeGLcodebBSSystemID;
                                    $data['glCode'] = $valueBs->financeGLcodebBS;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentLocalCurrencyID'] = $valueBs->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = 1;
                                    $data['documentLocalAmount'] = ABS($valueBs->localAmount);
                                    $data['documentRptCurrencyID'] = $valueBs->reportingCurrencyID;
                                    $data['documentRptCurrencyER'] = 1;
                                    $data['documentRptAmount'] = ABS($valueBs->rptAmount);
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }

                            if ($pl) {
                                if ($masterData->interCompanyTransferYN == -1) {
                                    $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 1);
                                    $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 1);
                                } else {
                                    $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 2);
                                    $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 2);
                                }
                                $data['glAccountType'] =ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentLocalCurrencyID'] = $pl->localCurrencyID;
                                $data['documentLocalCurrencyER'] = 1;
                                $data['documentLocalAmount'] = ABS($pl->localAmount) * -1;
                                $data['documentRptCurrencyID'] = $pl->reportingCurrencyID;
                                $data['documentRptCurrencyER'] = 1;
                                $data['documentRptAmount'] = ABS($pl->rptAmount) * -1;
                                $data['timestamp'] = \Helper::currentDateTime();
                                array_push($finalData, $data);
                            }
                        }
                        break;
                    case 61: // INRC - Inventory Reclassififcation
                        $masterData = InventoryReclassification::find($masterModel["autoID"]);
                        //get balansheet account
                        $bs = InventoryReclassificationDetail::selectRaw("SUM(currentStockQty * unitCostLocal) as localAmount, SUM(currentStockQty * unitCostRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,reportingCurrencyID")->WHERE('inventoryreclassificationID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->first();
                        //get pnl account
                        $pl = InventoryReclassificationDetail::selectRaw("SUM(currentStockQty * unitCostLocal) as localAmount, SUM(currentStockQty * unitCostRpt) as rptAmount,financeGLcodePLSystemID,financeGLcodePL,localCurrencyID,reportingCurrencyID")->WHERE('inventoryreclassificationID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->where('financeGLcodePLSystemID', '>', 0)->groupBy('financeGLcodePLSystemID')->get();
                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                            $data['serviceLineCode'] = $masterData->serviceLineCode;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->documentCode;
                            $data['documentDate'] = date('Y-m-d H:i:s');
                            $data['documentYear'] = \Helper::dateYear($masterData->inventoryReclassificationDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterData->inventoryReclassificationDate);
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->narration;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = 0;
                            $data['documentTransCurrencyID'] = 0;
                            $data['documentTransCurrencyER'] = 0;
                            $data['documentTransAmount'] = 0;
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            if ($bs) {
                                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                $data['glCode'] = $bs->financeGLcodebBS;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentLocalCurrencyID'] = $bs->localCurrencyID;
                                $data['documentLocalCurrencyER'] = 1;
                                $data['documentLocalAmount'] = ABS($bs->localAmount) * -1;
                                $data['documentRptCurrencyID'] = $bs->reportingCurrencyID;
                                $data['documentRptCurrencyER'] = 1;
                                $data['documentRptAmount'] = ABS($bs->rptAmount) * -1;
                                $data['timestamp'] = \Helper::currentDateTime();
                                array_push($finalData, $data);

                                $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 9);
                                $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 9);
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentLocalCurrencyID'] = $bs->localCurrencyID;
                                $data['documentLocalCurrencyER'] = 1;
                                $data['documentLocalAmount'] = ABS($bs->localAmount);
                                $data['documentRptCurrencyID'] = $bs->reportingCurrencyID;
                                $data['documentRptCurrencyER'] = 1;
                                $data['documentRptAmount'] = ABS($bs->rptAmount);
                                $data['timestamp'] = \Helper::currentDateTime();
                                array_push($finalData, $data);
                            }
                        }
                        break;
                    case 24: // PRN - Purchase Return
                        $masterData = PurchaseReturn::with(['details' => function ($query) {
                            $query->selectRaw("SUM(noQty * GRVcostPerUnitLocalCur) as localAmount, SUM(noQty * GRVcostPerUnitComRptCur) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,purhaseReturnAutoID, SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount, supplierTransactionCurrencyID, supplierTransactionER, localCurrencyID, localCurrencyER, companyReportingCurrencyID, companyReportingER");
                        }])->find($masterModel["autoID"]);
                        //get balansheet account
                        $bs = PurchaseReturnDetails::selectRaw("SUM(erp_purchasereturndetails.noQty * erp_grvdetails.landingCost_LocalCur) as localAmount, SUM(erp_purchasereturndetails.noQty * erp_grvdetails.landingCost_RptCur) as rptAmount,SUM(erp_grvdetails.landingCost_TransCur * erp_purchasereturndetails.noQty) as transAmount,erp_purchasereturndetails.financeGLcodebBSSystemID,erp_purchasereturndetails.financeGLcodebBS,erp_purchasereturndetails.localCurrencyID,erp_purchasereturndetails.companyReportingCurrencyID as reportingCurrencyID,erp_purchasereturndetails.supplierTransactionCurrencyID,erp_purchasereturndetails.supplierTransactionER,erp_purchasereturndetails.companyReportingER,erp_purchasereturndetails.localCurrencyER")
                                                   ->join('erp_grvdetails', 'erp_grvdetails.grvDetailsID', '=', 'erp_purchasereturndetails.grvDetailsID')
                                                   ->WHERE('erp_purchasereturndetails.purhaseReturnAutoID', $masterModel["autoID"])
                                                   ->first();

                        $valEligible = TaxService::checkGRVVATEligible($masterData->companySystemID, $masterData->supplierID);

                        $rcmActivated = TaxService::isPRNRCMActivation($masterModel["autoID"]);
                        $vatDetails = TaxService::processPRVAT($masterModel["autoID"]);

                        $logisticDetails = TaxService::processPRNLogisticDetails($masterModel["autoID"]);

                        $transVATAmount = isset($vatDetails['masterVATTrans']) ? $vatDetails['masterVATTrans'] : 0;
                        $localVATAmount = isset($vatDetails['masterVATLocal']) ? $vatDetails['masterVATLocal'] : 0;
                        $rptVATAmount = isset($vatDetails['masterVATRpt']) ? $vatDetails['masterVATRpt'] : 0;

                        $exemptVATTransAmount = isset($vatDetails['exemptVATTrans']) ? $vatDetails['exemptVATTrans'] : 0;
                        $exemptVATLocalAmount = isset($vatDetails['exemptVATLocal']) ? $vatDetails['exemptVATLocal'] : 0;
                        $exemptVATRptAmount = isset($vatDetails['exemptVATRpt']) ? $vatDetails['exemptVATRpt'] : 0;

                        $logisticData = PurchaseReturnLogistic::selectRaw('SUM(logisticAmountTrans) as logisticAmountTransTotal, SUM(logisticAmountRpt) as logisticAmountRptTotal, SUM(logisticAmountLocal) as logisticAmountLocalTotal, SUM(logisticVATAmount) as logisticVATAmountTotal, SUM(logisticVATAmountLocal) as logisticVATAmountLocalTotal, SUM(logisticVATAmountRpt) as logisticVATAmountRptTotal, purchase_return_logistic.UnbilledGRVAccountSystemID, purchase_return_logistic.supplierID, purchase_return_logistic.supplierTransactionCurrencyID, "1" as supplierTransactionER,erp_purchaseordermaster.companyReportingCurrencyID, 
                                            ROUND((SUM(reqAmountTransCur_amount + erp_purchaseorderadvpayment.VATAmount)/SUM(reqAmountInPORptCur + erp_purchaseorderadvpayment.VATAmountRpt)),7) as companyReportingER,
                                            erp_purchaseordermaster.localCurrencyID,
                                            ROUND((SUM(reqAmountTransCur_amount + erp_purchaseorderadvpayment.VATAmount)/SUM(reqAmountInPOLocalCur + erp_purchaseorderadvpayment.VATAmountLocal)),7) as localCurrencyER')
                                                              ->leftJoin('erp_purchaseorderadvpayment', 'erp_purchaseorderadvpayment.poAdvPaymentID', '=', 'purchase_return_logistic.poAdvPaymentID')
                                                              ->leftJoin('erp_purchaseordermaster', 'erp_purchaseorderadvpayment.poID', '=', 'erp_purchaseordermaster.purchaseOrderID')
                                                              ->where('purchaseReturnID', $masterModel["autoID"])
                                                              ->groupBy('UnbilledGRVAccountSystemID', 'supplierID')
                                                              ->get();

                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                            $data['serviceLineCode'] = $masterData->serviceLineCode;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->purchaseReturnCode;
                            $data['documentDate'] = date('Y-m-d H:i:s');
                            $data['documentYear'] = \Helper::dateYear($masterData->purchaseReturnDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterData->purchaseReturnDate);
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->narration;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = $masterData->supplierID;
                            $data['chartOfAccountSystemID'] = ($masterData->isInvoiceCreatedForGrv == 1) ? $masterData->liabilityAccountSysemID : $masterData->UnbilledGRVAccountSystemID;
                            $data['glCode'] = ($masterData->isInvoiceCreatedForGrv == 1) ? $masterData->liabilityAccount : $masterData->UnbilledGRVAccount;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);


                            $data['documentTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->supplierTransactionER;
                            $data['documentTransAmount'] = \Helper::roundValue((($valEligible && !$rcmActivated) ? $masterData->details[0]->transAmount + $transVATAmount  : $masterData->details[0]->transAmount - $exemptVATTransAmount));
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue((($valEligible && !$rcmActivated) ? $masterData->details[0]->localAmount + $localVATAmount : $masterData->details[0]->localAmount - $exemptVATLocalAmount));
                            $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                            $data['documentRptAmount'] = \Helper::roundValue((($valEligible && !$rcmActivated) ? $masterData->details[0]->rptAmount + $rptVATAmount : $masterData->details[0]->rptAmount - $exemptVATRptAmount));
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);

                            if ($valEligible && ($vatDetails['masterVATTrans'] > 0 || $logisticDetails['logisticTransVATAmount'] > 0 || $exemptVATTransAmount > 0)) {

                                if ($masterData->isInvoiceCreatedForGrv == 1) {
                                    $taxData = TaxService::getInputVATGLAccount($masterData->companySystemID);
                                } else {
                                    $taxData = TaxService::getInputVATTransferGLAccount($masterData->companySystemID);
                                }

                                if (!empty($taxData)) {
                                    if ($masterData->isInvoiceCreatedForGrv == 1) {
                                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxData->inputVatGLAccountAutoID)
                                            ->where('companySystemID', $masterData->companySystemID)
                                            ->first();

                                        $taxLedgerData['inputVATGlAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                    } else {
                                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxData->inputVatTransferGLAccountAutoID)
                                            ->where('companySystemID', $masterData->companySystemID)
                                            ->first();

                                        $taxLedgerData['inputVatTransferAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                    }

                                    if (!empty($chartOfAccountData)) {
                                        $data['supplierCodeSystem'] = $masterData->supplierID;
                                        $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                        $data['glCode'] = $chartOfAccountData->AccountCode;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                        $data['documentTransCurrencyID'] = $masterData->details[0]->supplierTransactionCurrencyID;
                                        $data['documentTransCurrencyER'] = $masterData->details[0]->supplierTransactionER;
                                        $data['documentTransAmount'] = ABS(\Helper::roundValue($vatDetails['masterVATTrans'] + $logisticDetails['logisticTransVATAmount'])) * -1;

                                        $data['documentLocalCurrencyID'] = $masterData->details[0]->localCurrencyID;
                                        $data['documentLocalCurrencyER'] = $masterData->details[0]->localCurrencyER;
                                        $data['documentLocalAmount'] = ABS(\Helper::roundValue($vatDetails['masterVATLocal'] + $logisticDetails['logisticLocalVATAmount'])) * -1;

                                        $data['documentRptCurrencyID'] = $masterData->details[0]->companyReportingCurrencyID;
                                        $data['documentRptCurrencyER'] = $masterData->details[0]->companyReportingER;
                                        $data['documentRptAmount'] = ABS(\Helper::roundValue($vatDetails['masterVATRpt'] + $logisticDetails['logisticRptVATAmount'])) * -1;
                                        $data['timestamp'] = \Helper::currentDateTime();
                                        array_push($finalData, $data);
                                    } else {
                                        Log::info('GRV VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Input Vat GL Account not assigned to company' . date('H:i:s'));
                                    }
                                } else {
                                    Log::info('GRV VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                    Log::info('Input Vat Transfer GL Account not configured' . date('H:i:s'));
                                }


                                if($rcmActivated && ($exemptVATTransAmount > 0 || $vatDetails['masterVATTrans'] > 0)){
                                    $taxDataOutputTransfer = TaxService::getOutputVATTransferGLAccount($masterData->companySystemID);
                                    Log::info('Inside the Vat Entry OutputVATTransferGLAccoun Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                    if (!empty($taxDataOutputTransfer)) {
                                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxDataOutputTransfer->outputVatTransferGLAccountAutoID)
                                            ->where('companySystemID', $masterData->companySystemID)
                                            ->first();

                                        if (!empty($chartOfAccountData)) {
                                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            $data['glCode'] = $chartOfAccountData->AccountCode;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                            $data['documentTransAmount'] = ABS(\Helper::roundValue($vatDetails['masterVATTrans'] + $exemptVATTransAmount));
                                            $data['documentLocalAmount'] = ABS(\Helper::roundValue($vatDetails['masterVATLocal'] + $exemptVATLocalAmount));
                                            $data['documentRptAmount'] = ABS(\Helper::roundValue($vatDetails['masterVATRpt'] + $exemptVATRptAmount));
                                            $data['timestamp'] = \Helper::currentDateTime();
                                            array_push($finalData, $data);

                                            $taxLedgerData['outputVatTransferGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            Log::info('Inside the Vat Entry OutVATTransferGLAccount Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        } else {
                                            Log::info('GRV VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                            Log::info('Output Vat Transfer GL Account not assigned to company' . date('H:i:s'));
                                        }
                                    } else {
                                        Log::info('GRV VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Output Vat Transfer GL Account not configured' . date('H:i:s'));
                                    }
                                }
                            }

                            if ($bs) {
                                $transBSVAT = isset($vatDetails['bsVAT'][$bs->financeGLcodebBSSystemID]['transVATAmount']) ? $vatDetails['bsVAT'][$bs->financeGLcodebBSSystemID]['transVATAmount'] : 0;
                                $rptBSVAT = isset($vatDetails['bsVAT'][$bs->financeGLcodebBSSystemID]['rptVATAmount']) ? $vatDetails['bsVAT'][$bs->financeGLcodebBSSystemID]['rptVATAmount'] : 0;
                                $localBSVAT = isset($vatDetails['bsVAT'][$bs->financeGLcodebBSSystemID]['localVATAmount']) ? $vatDetails['bsVAT'][$bs->financeGLcodebBSSystemID]['localVATAmount'] : 0;

                                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                $data['glCode'] = $bs->financeGLcodebBS;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentTransCurrencyID'] = $bs->supplierTransactionCurrencyID;
                                $data['documentTransCurrencyER'] = $bs->supplierTransactionER;
                                $data['documentTransAmount'] = ABS($bs->transAmount + $transBSVAT) * -1;
                                $data['documentLocalCurrencyID'] = $bs->localCurrencyID;
                                $data['documentLocalCurrencyER'] = $bs->localCurrencyER;
                                $data['documentLocalAmount'] = ABS($bs->localAmount + $localBSVAT) * -1;
                                $data['documentRptCurrencyID'] = $bs->reportingCurrencyID;
                                $data['documentRptCurrencyER'] = $bs->companyReportingER;
                                $data['documentRptAmount'] = ABS($bs->rptAmount + $rptBSVAT) * -1;
                                $data['timestamp'] = \Helper::currentDateTime();
                                array_push($finalData, $data);
                            }

                            if ($logisticData) {
                                foreach ($logisticData as $val) {
                                    $data['supplierCodeSystem'] = $val->supplierID;
                                    $data['chartOfAccountSystemID'] = $val->UnbilledGRVAccountSystemID;
                                    $data['glCode'] = ChartOfAccount::getAccountCode($val->UnbilledGRVAccountSystemID);
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                                    $data['documentTransAmount'] = \Helper::roundValue(ABS($val->logisticAmountTransTotal + $val->logisticVATAmountTotal));

                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->logisticAmountLocalTotal + $val->logisticVATAmountLocalTotal));

                                    $data['documentRptCurrencyID'] = $val->companyReportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $val->companyReportingER;
                                    $data['documentRptAmount'] = \Helper::roundValue(ABS($val->logisticAmountRptTotal + $val->logisticVATAmountRptTotal));
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        break;
                    case 20:
                        /*customer Invoice*/
                        $masterData = CustomerInvoiceDirect::with(['finance_period_by'])->find($masterModel["autoID"]);
                        $company = Company::select('masterComapanyID')->where('companySystemID', $masterData->companySystemID)->first();
                        if ($masterData->isPerforma == 2 || $masterData->isPerforma == 4 || $masterData->isPerforma == 5) {   // item sales invoice || from sales order || from sales quotation
                            $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->customerGLSystemID)->first();
                            $masterDocumentDate = Carbon::now();

                            $time = Carbon::now();

                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['masterCompanyID'] = $company->masterComapanyID;
                            $data['documentID'] = "INV";
                            $data['documentSystemID'] = $masterData->documentSystemiD;
                            $data['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                            $data['documentCode'] = $masterData->bookingInvCode;

                            $data['documentDate'] = $masterDocumentDate;
                            $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                            $data['invoiceNumber'] = $masterData->customerInvoiceNo;
                            $data['invoiceDate'] = $masterData->customerInvoiceDate;
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

                            $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                            $data['serviceLineCode'] = $masterData->serviceLineCode;

                            // from customer invoice master table
                            $data['chartOfAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
                            $data['glCode'] = $chartOfAccount->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                            $data['documentNarration'] = $masterData->comments;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = $masterData->customerID;

                            $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                            $data['documentTransAmount'] = 0;

                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = $masterData->bookingAmountLocal + $masterData->VATAmountLocal;

                            $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                            $data['documentRptAmount'] = $masterData->bookingAmountRpt + $masterData->VATAmountRpt;

                            $data['documentType'] = 11;

                            $data['createdUserSystemID'] = $empID->empID;
                            $data['createdDateTime'] = $time;
                            $data['createdUserID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = getenv('COMPUTERNAME');
                            $data['timestamp'] = $time;
                            array_push($finalData, $data);

                            $bs = CustomerInvoiceItemDetails::selectRaw("0 as transAmount, SUM(qtyIssuedDefaultMeasure * issueCostLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * issueCostRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,localCurrencyER,reportingCurrencyER,reportingCurrencyID")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->first();
                            //get pnl account
                            $pl = CustomerInvoiceItemDetails::selectRaw("0 as transAmount,SUM(qtyIssuedDefaultMeasure * issueCostLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * issueCostRpt) as rptAmount,financeGLcodePLSystemID,financeGLcodePL,localCurrencyID,localCurrencyER,reportingCurrencyER,reportingCurrencyID")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->where('financeGLcodePLSystemID', '>', 0)->groupBy('financeGLcodePLSystemID')->get();

                            $revenue = CustomerInvoiceItemDetails::selectRaw("0 as transAmount,SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * sellingCostAfterMarginRpt) as rptAmount,financeGLcodeRevenueSystemID,financeGLcodeRevenue,localCurrencyID,localCurrencyER,reportingCurrencyER,reportingCurrencyID")->WHERE('custInvoiceDirectAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodeRevenueSystemID')->where('financeGLcodeRevenueSystemID', '>', 0)->groupBy('financeGLcodeRevenueSystemID')->get();

                            if ($bs) {

                                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                $data['glCode'] = $bs->financeGLcodebBS;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                                $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                                $data['documentTransAmount'] = ABS($bs->transAmount) * -1;

                                $data['documentLocalCurrencyID'] = $bs->localCurrencyID;
                                $data['documentLocalCurrencyER'] = $bs->localCurrencyER;
                                $data['documentLocalAmount'] = ABS($bs->localAmount) * -1;

                                $data['documentRptCurrencyID'] = $bs->reportingCurrencyID;
                                $data['documentRptCurrencyER'] = $bs->reportingCurrencyER;
                                $data['documentRptAmount'] = ABS($bs->rptAmount) * -1;

                                array_push($finalData, $data);
                            }

                            if ($pl) {
                                foreach ($pl as $item) {
                                    $data['chartOfAccountSystemID'] = $item->financeGLcodePLSystemID;
                                    $data['glCode'] = $item->financeGLcodePL;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                    $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                                    $data['documentTransAmount'] = ABS($item->transAmount);

                                    $data['documentLocalCurrencyID'] = $item->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $item->localCurrencyER;
                                    $data['documentLocalAmount'] = ABS($item->localAmount);

                                    $data['documentRptCurrencyID'] = $item->reportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $item->reportingCurrencyER;
                                    $data['documentRptAmount'] = ABS($item->rptAmount);

                                    array_push($finalData, $data);
                                }
                            }

                            if ($revenue) {

                                foreach ($revenue as $item) {

                                    $data['chartOfAccountSystemID'] = $item->financeGLcodeRevenueSystemID;
                                    $data['glCode'] = $item->financeGLcodeRevenue;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                    $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                                    $data['documentTransAmount'] = ABS($item->transAmount) * -1;

                                    $data['documentLocalCurrencyID'] = $item->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $item->localCurrencyER;
                                    $data['documentLocalAmount'] = ABS($item->localAmount) * -1;

                                    $data['documentRptCurrencyID'] = $item->reportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $item->reportingCurrencyER;
                                    $data['documentRptAmount'] = ABS($item->rptAmount) * -1;

                                    array_push($finalData, $data);
                                }

                            }

                            $erp_taxdetail = Taxdetail::where('companySystemID', $masterData->companySystemID)
                                ->where('documentSystemCode', $masterData->custInvoiceDirectAutoID)
                                ->where('documentSystemID', 20)
                                ->get();

                            if (!empty($erp_taxdetail)) {
                                $taxConfigData = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);
                                if (!empty($taxConfigData)) {
                                    $taxGL = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')
                                        ->where('chartOfAccountSystemID', $taxConfigData->outputVatGLAccountAutoID)
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
                                            $data['documentTransAmount'] = 0;
                                            $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                                            $data['documentLocalAmount'] = $tax->localAmount * -1;
                                            $data['documentRptCurrencyID'] = $tax->rptCurrencyID;
                                            $data['documentRptCurrencyER'] = $tax->rptCurrencyER;
                                            $data['documentRptAmount'] = $tax->rptAmount * -1;
                                            array_push($finalData, $data);

                                            $taxLedgerData['outputVatGLAccountID'] = $taxGL['chartOfAccountSystemID'];
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
                        elseif ($masterData->isPerforma == 3) { // From Deivery Note
                            $customer = CustomerMaster::find($masterData->customerID);
                            $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->customerGLSystemID)->first();
                            $unbilledhartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $customer->custUnbilledAccountSystemID)->first();

                            $masterDocumentDate = Carbon::now();
                            $time = Carbon::now();
                            if ($masterData->finance_period_by->isActive == -1) {
                                $masterDocumentDate = $masterData->bookingDate;
                            }

                            if ($chartOfAccount) {
                                $data['companySystemID'] = $masterData->companySystemID;
                                $data['companyID'] = $masterData->companyID;
                                $data['masterCompanyID'] = $company->masterComapanyID;
                                $data['documentID'] = "INV";
                                $data['documentSystemID'] = $masterData->documentSystemiD;
                                $data['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                                $data['documentCode'] = $masterData->bookingInvCode;
                                $data['documentDate'] = $masterDocumentDate;
                                $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                                $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                                $data['invoiceNumber'] = $masterData->customerInvoiceNo;
                                $data['invoiceDate'] = $masterData->customerInvoiceDate;
                                $data['documentConfirmedDate'] = $masterData->confirmedDate;
                                $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                                $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                                $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                                $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                                $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

                                $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                                $data['serviceLineCode'] = $masterData->serviceLineCode;

                                // from customer invoice master table
                                $data['chartOfAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
                                $data['glCode'] = $chartOfAccount->AccountCode;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                $data['documentNarration'] = $masterData->comments;
                                $data['clientContractID'] = 'X';
                                $data['contractUID'] = 159;
                                $data['supplierCodeSystem'] = $masterData->customerID;

                                $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                                $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                                $data['documentTransAmount'] = $masterData->bookingAmountTrans + $masterData->VATAmount;

                                $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                $data['documentLocalAmount'] = $masterData->bookingAmountLocal + $masterData->VATAmountLocal;

                                $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                                $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                                $data['documentRptAmount'] = $masterData->bookingAmountRpt + $masterData->VATAmountRpt;

                                $data['documentType'] = 11;

                                $data['createdUserSystemID'] = $empID->empID;
                                $data['createdDateTime'] = $time;
                                $data['createdUserID'] = $empID->employeeSystemID;
                                $data['createdUserPC'] = getenv('COMPUTERNAME');
                                $data['timestamp'] = $time;
                                array_push($finalData, $data);
                            }

                            if ($unbilledhartOfAccount) {
                                $data['companySystemID'] = $masterData->companySystemID;
                                $data['companyID'] = $masterData->companyID;
                                $data['masterCompanyID'] = $company->masterComapanyID;
                                $data['documentID'] = "INV";
                                $data['documentSystemID'] = $masterData->documentSystemiD;
                                $data['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                                $data['documentCode'] = $masterData->bookingInvCode;
                                //$data['documentDate'] = ($masterData->isPerforma == 1) ? $time : $masterData->bookingDate;
                                $data['documentDate'] = $masterDocumentDate;
                                $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                                $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                                $data['invoiceNumber'] = $masterData->customerInvoiceNo;
                                $data['invoiceDate'] = $masterData->customerInvoiceDate;
                                $data['documentConfirmedDate'] = $masterData->confirmedDate;
                                $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                                $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                                $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                                $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                                $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

                                $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                                $data['serviceLineCode'] = $masterData->serviceLineCode;

                                // from customer invoice master table
                                $data['chartOfAccountSystemID'] = $unbilledhartOfAccount->chartOfAccountSystemID;
                                $data['glCode'] = $unbilledhartOfAccount->AccountCode;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                $data['documentNarration'] = $masterData->comments;
                                $data['clientContractID'] = 'X';
                                $data['contractUID'] = 159;
                                $data['supplierCodeSystem'] = $masterData->customerID;

                                $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                                $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                                $data['documentTransAmount'] = (ABS($masterData->bookingAmountTrans) + ABS($masterData->VATAmount)) * -1;

                                $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                $data['documentLocalAmount'] = (ABS($masterData->bookingAmountLocal) + ABS($masterData->VATAmountLocal)) * -1;

                                $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                                $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                                $data['documentRptAmount'] = (ABS($masterData->bookingAmountRpt) + ABS($masterData->VATAmountRpt)) * -1;

                                $data['documentType'] = 11;

                                $data['createdUserSystemID'] = $empID->empID;
                                $data['createdDateTime'] = $time;
                                $data['createdUserID'] = $empID->employeeSystemID;
                                $data['createdUserPC'] = getenv('COMPUTERNAME');
                                $data['timestamp'] = $time;
                                array_push($finalData, $data);
                            }


                            $erp_taxdetail = Taxdetail::where('companySystemID', $masterData->companySystemID)
                                ->where('documentSystemCode', $masterData->custInvoiceDirectAutoID)
                                ->where('documentSystemID', 20)
                                ->get();

                            if (!empty($erp_taxdetail)) {

                                $taxConfigData = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);
                                if (!empty($taxConfigData)) {
                                    $taxGL = ChartOfAccountsAssigned::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')
                                        ->where('chartOfAccountSystemID', $taxConfigData->outputVatGLAccountAutoID)
                                        ->where('companySystemID', $masterData->companySystemID)
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
                                            $data['documentTransAmount'] = ABS($tax->amount) * -1;
                                            $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                                            $data['documentLocalAmount'] = ABS($tax->localAmount) * -1;
                                            $data['documentRptCurrencyID'] = $tax->rptCurrencyID;
                                            $data['documentRptCurrencyER'] = $tax->rptCurrencyER;
                                            $data['documentRptAmount'] = ABS($tax->rptAmount) * -1;
                                            array_push($finalData, $data);

                                            $taxLedgerData['outputVatGLAccountID'] = $taxGL['chartOfAccountSystemID'];
                                        }
                                    } else {
                                        Log::info('Customer Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                                    }
                                } else {
                                    Log::info('Customer Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                    Log::info('Output Vat GL Account not configured' . date('H:i:s'));
                                }


                                $taxConfigData2 = TaxService::getOutputVATTransferGLAccount($masterModel["companySystemID"]);
                                if (!empty($taxConfigData2)) {
                                    $taxGL = ChartOfAccountsAssigned::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')
                                        ->where('chartOfAccountSystemID', $taxConfigData2->outputVatTransferGLAccountAutoID)
                                        ->where('companySystemID', $taxConfigData2->companySystemID)
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
                                            $data['documentTransAmount'] = ABS($tax->amount);
                                            $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                                            $data['documentLocalAmount'] = ABS($tax->localAmount);
                                            $data['documentRptCurrencyID'] = $tax->rptCurrencyID;
                                            $data['documentRptCurrencyER'] = $tax->rptCurrencyER;
                                            $data['documentRptAmount'] = ABS($tax->rptAmount);
                                            array_push($finalData, $data);

                                            $taxLedgerData['outputVatTransferGLAccountID'] = $taxGL['chartOfAccountSystemID'];
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
                        else {
                            $detOne = CustomerInvoiceDirectDetail::with(['contract'])->where('custInvoiceDirectID', $masterModel["autoID"])->first();
                            $detail = CustomerInvoiceDirectDetail::selectRaw("sum(comRptAmount) as comRptAmount, comRptCurrency, sum(localAmount) as localAmount , localCurrencyER, localCurrency, sum(invoiceAmount) as invoiceAmount, invoiceAmountCurrencyER, invoiceAmountCurrency,comRptCurrencyER, customerID, clientContractID, comments, glSystemID,   serviceLineSystemID,serviceLineCode, sum(VATAmount) as VATAmount, sum(VATAmountLocal) as VATAmountLocal, sum(VATAmountRpt) as VATAmountRpt")->WHERE('custInvoiceDirectID', $masterModel["autoID"])->groupBy('glCode', 'serviceLineCode', 'comments')->get();
                            $company = Company::select('masterComapanyID')->where('companySystemID', $masterData->companySystemID)->first();
                            $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->customerGLSystemID)->first();

                            $date = new Carbon($masterData->bookingDate);
                            $time = Carbon::now();

                            $masterDocumentDate = $time;
                            if ($masterData->isPerforma == 1) {
                                $masterDocumentDate = $time;
                            } else {
                                if ($masterData->finance_period_by->isActive == -1) {
                                    $masterDocumentDate = $masterData->bookingDate;
                                }
                            }
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['masterCompanyID'] = $company->masterComapanyID;
                            $data['documentID'] = "INV";
                            $data['documentSystemID'] = $masterData->documentSystemiD;
                            $data['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                            $data['documentCode'] = $masterData->bookingInvCode;
                            //$data['documentDate'] = ($masterData->isPerforma == 1) ? $time : $masterData->bookingDate;
                            $data['documentDate'] = $masterDocumentDate;
                            $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                            $data['invoiceNumber'] = $masterData->customerInvoiceNo;
                            $data['invoiceDate'] = $masterData->customerInvoiceDate;
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

                            $data['serviceLineSystemID'] = $detOne->serviceLineSystemID;
                            $data['serviceLineCode'] = $detOne->serviceLineCode;

                            // from customer invoice master table
                            $data['chartOfAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
                            $data['glCode'] = $chartOfAccount->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                            $data['documentNarration'] = $masterData->comments;
                            $data['clientContractID'] = $detOne->clientContractID;
                            $data['contractUID'] = $detOne->contract ? $detOne->contract->contractUID : 0;
                            $data['supplierCodeSystem'] = $masterData->customerID;

                            $data['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                            $data['documentTransAmount'] = $masterData->bookingAmountTrans + $masterData->VATAmount;
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = $masterData->bookingAmountLocal + $masterData->VATAmountLocal;
                            $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                            $data['documentRptAmount'] = $masterData->bookingAmountRpt + $masterData->VATAmountRpt;

                            $data['documentType'] = 11;

                            $data['createdUserSystemID'] = $empID->empID;
                            $data['createdDateTime'] = $time;
                            $data['createdUserID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = getenv('COMPUTERNAME');
                            $data['timestamp'] = $time;
                            array_push($finalData, $data);

                            if (!empty($detail)) {
                                foreach ($detail as $item) {
                                    $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $item->glSystemID)->first();

                                    $data['companySystemID'] = $masterData->companySystemID;
                                    $data['companyID'] = $masterData->companyID;
                                    $data['documentSystemID'] = $masterData->documentSystemiD;
                                    $data['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                                    $data['documentCode'] = $masterData->bookingInvCode;
                                    $data['documentDate'] = $masterDocumentDate;
                                    $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                                    $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                                    $data['invoiceNumber'] = $masterData->customerInvoiceNo;
                                    $data['invoiceDate'] = $masterData->customerInvoiceDate;
                                    $data['documentConfirmedDate'] = $masterData->confirmedDate;
                                    $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                                    $data['masterCompanyID'] = $company->masterComapanyID;

                                    $data['serviceLineSystemID'] = $item->serviceLineSystemID;
                                    $data['serviceLineCode'] = $item->serviceLineCode;

                                    // from customer invoice master table
                                    $data['chartOfAccountSystemID'] = $item->glSystemID;
                                    $data['glCode'] = $chartOfAccount->AccountCode;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                    $data['documentNarration'] = $item->comments;
                                    $data['clientContractID'] = $item->clientContractID;
                                    $data['supplierCodeSystem'] = $item->customerID;

                                    $data['documentTransCurrencyID'] = $item->invoiceAmountCurrency;
                                    $data['documentTransCurrencyER'] = $item->invoiceAmountCurrencyER;
                                    $data['documentTransAmount'] = (($masterData->isPerforma == 1) ? ($item->invoiceAmount - $item->VATAmount) : $item->invoiceAmount) * -1;
                                    $data['documentLocalCurrencyID'] = $item->localCurrency;

                                    $data['documentLocalCurrencyER'] = $item->localCurrencyER;
                                    $data['documentLocalAmount'] = (($masterData->isPerforma == 1) ? ($item->localAmount - $item->VATAmountLocal) : $item->localAmount) * -1;
                                    $data['documentRptCurrencyID'] = $item->comRptCurrency;
                                    $data['documentRptCurrencyER'] = $item->comRptCurrencyER;
                                    $data['documentRptAmount'] = (($masterData->isPerforma == 1) ? ($item->comRptAmount - $item->VATAmountRpt) : $item->comRptAmount) * -1;
                                    array_push($finalData, $data);
                                }
                            }

                            $erp_taxdetail = Taxdetail::where('companySystemID', $masterData->companySystemID)
                                ->where('documentSystemCode', $masterData->custInvoiceDirectAutoID)
                                ->where('documentSystemID', 20)
                                ->get();
                            if (!empty($erp_taxdetail)) {

                                // Input VAT control
                                $taxConfigData = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);
                                if (!empty($taxConfigData)) {
                                    $taxGL = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')
                                        ->where('chartOfAccountSystemID', $taxConfigData->outputVatGLAccountAutoID)
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
                                            $data['clientContractID'] = $detOne->clientContractID;
                                            $data['supplierCodeSystem'] = $masterData->customerID;

                                            $data['documentTransCurrencyID'] = $tax->currency;
                                            $data['documentTransCurrencyER'] = $tax->currencyER;
                                            $data['documentTransAmount'] = $tax->amount * -1;
                                            $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                                            $data['documentLocalAmount'] = $tax->localAmount * -1;
                                            $data['documentRptCurrencyID'] = $tax->rptCurrencyID;
                                            $data['documentRptCurrencyER'] = $tax->rptCurrencyER;
                                            $data['documentRptAmount'] = $tax->rptAmount * -1;
                                            array_push($finalData, $data);

                                            $taxLedgerData['outputVatGLAccountID'] = $taxGL['chartOfAccountSystemID'];
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

                        break;
                    case 7: // SA - Stock Adjustment
                        $masterData = StockAdjustment::find($masterModel["autoID"]);
                        //get balansheet account
                        if ($masterData->stockAdjustmentType == 2) {

                            $bs = StockAdjustmentDetails::selectRaw("SUM(currenctStockQty * (wacAdjLocal - currentWaclocal)) as localAmount, SUM(currenctStockQty * (wacAdjRpt - currentWacRpt)) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,currentWacLocalCurrencyID as localCurrencyID,currentWacRptCurrencyID as reportingCurrencyID,wacAdjRptER as reportingCurrencyER,wacAdjLocalER as localCurrencyER")->WHERE('stockAdjustmentAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->first();
                            //get pnl account
                            $pl = StockAdjustmentDetails::selectRaw("SUM(currenctStockQty * (wacAdjLocal - currentWaclocal)) as localAmount, SUM(currenctStockQty * (wacAdjRpt - currentWacRpt)) as rptAmount,financeGLcodePLSystemID,financeGLcodePL,currentWacLocalCurrencyID as localCurrencyID,currentWacRptCurrencyID as reportingCurrencyID,wacAdjRptER as reportingCurrencyER,wacAdjLocalER as localCurrencyER")->WHERE('stockAdjustmentAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->where('financeGLcodePLSystemID', '>', 0)->groupBy('financeGLcodePLSystemID')->get();

                        } else {
                            $bs = StockAdjustmentDetails::selectRaw("SUM(noQty * wacAdjLocal) as localAmount, SUM(noQty * wacAdjRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,currentWacLocalCurrencyID as localCurrencyID,currentWacRptCurrencyID as reportingCurrencyID,wacAdjRptER as reportingCurrencyER,wacAdjLocalER as localCurrencyER")->WHERE('stockAdjustmentAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->first();
                            //get pnl account
                            $pl = StockAdjustmentDetails::selectRaw("SUM(noQty * wacAdjLocal) as localAmount, SUM(noQty * wacAdjRpt) as rptAmount,financeGLcodePLSystemID,financeGLcodePL,currentWacLocalCurrencyID as localCurrencyID,currentWacRptCurrencyID as reportingCurrencyID,wacAdjRptER as reportingCurrencyER,wacAdjLocalER as localCurrencyER")->WHERE('stockAdjustmentAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->where('financeGLcodePLSystemID', '>', 0)->groupBy('financeGLcodePLSystemID')->get();
                        }

                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                            $data['serviceLineCode'] = $masterData->serviceLineCode;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->stockAdjustmentCode;
                            $data['documentDate'] = date('Y-m-d H:i:s');
                            $data['documentYear'] = \Helper::dateYear($masterData->stockAdjustmentDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterData->stockAdjustmentDate);
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->comment;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = 0;
                            $data['documentTransCurrencyID'] = 0;
                            $data['documentTransCurrencyER'] = 0;
                            $data['documentTransAmount'] = 0;
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            if ($bs) {
                                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                $data['glCode'] = $bs->financeGLcodebBS;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentLocalCurrencyID'] = $bs->localCurrencyID;
                                $data['documentLocalCurrencyER'] = $bs->localCurrencyER;
                                $data['documentLocalAmount'] = $bs->localAmount;
                                $data['documentRptCurrencyID'] = $bs->reportingCurrencyID;
                                $data['documentRptCurrencyER'] = $bs->reportingCurrencyER;
                                $data['documentRptAmount'] = $bs->rptAmount;
                                $data['timestamp'] = \Helper::currentDateTime();
                                array_push($finalData, $data);
                            }

                            if ($pl) {
                                foreach ($pl as $val) {
                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['glCode'] = $val->financeGLcodePL;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                    $data['documentLocalAmount'] = $val->localAmount * -1;
                                    $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                                    $data['documentRptAmount'] = $val->rptAmount * -1;
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        break;
                    case 11: // SI - Supplier Invoice
                        $masterData = BookInvSuppMaster::with(['detail' => function ($query) {
                            $query->selectRaw("SUM(totLocalAmount) as localAmount, SUM(totRptAmount) as rptAmount,SUM(totTransactionAmount) as transAmount,SUM(VATAmount) as totalVATAmount,SUM(VATAmountLocal) as totalVATAmountLocal,SUM(VATAmountRpt) as totalVATAmountRpt,bookingSuppMasInvAutoID");
                        }, 'item_details' => function($query) {
                            $query->selectRaw("SUM(netAmount) as netAmountTotal, SUM(VATAmount*noQty) as totalVATAmount,SUM(VATAmountLocal*noQty) as totalVATAmountLocal,SUM(VATAmountRpt*noQty) as totalVATAmountRpt, bookingSuppMasInvAutoID");
                        }, 'directdetail' => function ($query) {
                            $query->selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DIAmount) as transAmount,directInvoiceAutoID");
                        }, 'financeperiod_by'])->find($masterModel["autoID"]);
                        //get balansheet account
                        $bs = DirectInvoiceDetails::with(['chartofaccount'])
                                                    ->selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DIAmount) as transAmount, SUM(netAmountLocal) as netLocalAmount, SUM(netAmountRpt) as netRptAmount,SUM(netAmount) as netTransAmount,chartOfAccountSystemID as financeGLcodebBSSystemID,glCode as financeGLcodebBS,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DIAmountCurrency as supplierTransactionCurrencyID,DIAmountCurrencyER as supplierTransactionER,comRptCurrencyER as companyReportingER,localCurrencyER,serviceLineSystemID,serviceLineCode,chartOfAccountSystemID,comments")
                                                    ->WHERE('directInvoiceAutoID', $masterModel["autoID"])
                                                    ->groupBy('chartOfAccountSystemID', 'serviceLineSystemID', 'comments')
                                                    ->get();

                        $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER,payeeSystemCode")
                                        ->WHERE('documentSystemCode', $masterModel["autoID"])
                                        ->WHERE('documentSystemID', $masterModel["documentSystemID"])
                                        ->groupBy('documentSystemCode')
                                        ->first();


                         //get balansheet account
                        $bsItemDirect = SupplierInvoiceDirectItem::selectRaw("SUM(costPerUnitLocalCur*noQty) as localAmount, SUM(costPerUnitComRptCur*noQty) as rptAmount,SUM(costPerUnitSupTransCur*noQty) as transAmount,financeGLcodebBSSystemID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,companyReportingCurrencyID,companyReportingER,localCurrencyID,localCurrencyER")->WHERE('bookingSuppMasInvAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->get();

                        //get pnl account
                        $plItemDirect = SupplierInvoiceDirectItem::selectRaw("SUM(costPerUnitLocalCur*noQty) as localAmount, SUM(costPerUnitComRptCur*noQty) as rptAmount,SUM(costPerUnitSupTransCur*noQty) as transAmount,financeGLcodePLSystemID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,companyReportingCurrencyID,companyReportingER,localCurrencyID,localCurrencyER")->WHERE('bookingSuppMasInvAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->where('financeGLcodePLSystemID', '>', 0)->WHERE('includePLForGRVYN', -1)->groupBy('financeGLcodePLSystemID')->get();


                        $taxLocal = 0;
                        $taxRpt = 0;
                        $taxTrans = 0;
                        $retentionPercentage = ($masterData->retentionPercentage > 0) ? $masterData->retentionPercentage : 0;

                        $poInvoiceDirectLocalExtCharge = 0;
                        $poInvoiceDirectRptExtCharge = 0;
                        $poInvoiceDirectTransExtCharge = 0;

                        $directVATDetails = TaxService::processDirectSupplierInvoiceVAT($masterModel["autoID"], $masterModel["documentSystemID"]);
                        $rcmActivated = TaxService::isGRVRCMActivation($masterModel["autoID"]);


                        $directItemVatDetails = [];
                        if ($masterData->documentType == 3) {
                            $directItemVatDetails = TaxService::processSupplierInvoiceItemsVAT($masterModel["autoID"]);
                        }

                        if ($tax) {
                            $taxLocal = $tax->localAmount;
                            $taxRpt = $tax->rptAmount;
                            $taxTrans = $tax->transAmount;
                        }

                        if (count($masterData->directdetail) > 0) {
                            $poInvoiceDirectLocalExtCharge = (isset($masterData->directdetail[0]->localAmount)) ? $masterData->directdetail[0]->localAmount : 0;
                            $poInvoiceDirectRptExtCharge = (isset($masterData->directdetail[0]->rptAmount)) ? $masterData->directdetail[0]->rptAmount : 0;
                            $poInvoiceDirectTransExtCharge = (isset($masterData->directdetail[0]->transAmount)) ? $masterData->directdetail[0]->transAmount : 0;
                        }

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if ($masterData->financeperiod_by->isActive == -1) {
                            $masterDocumentDate = $masterData->bookingDate;
                        }

                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->bookingInvCode;
                            $data['documentDate'] = $masterDocumentDate;
                            $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->comments;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = $masterData->supplierID;;
                            $data['chartOfAccountSystemID'] = ($masterData->documentType == 4) ? $masterData->employeeControlAcID : $masterData->supplierGLCodeSystemID;
                            $data['glCode'] = ($masterData->documentType == 4) ? ChartOfAccount::getAccountCode($masterData->employeeControlAcID) : $masterData->supplierGLCode;
                            $data['documentTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->supplierTransactionCurrencyER;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;

                            $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                            $data['invoiceNumber'] = $masterData->supplierInvoiceNo;
                            $data['invoiceDate'] = $masterData->supplierInvoiceDate;

                            if ($masterData->documentType == 0 || $masterData->documentType == 2) { // check if it is supplier invoice
                                $data['documentTransAmount'] = \Helper::roundValue($masterData->detail[0]->transAmount + $poInvoiceDirectTransExtCharge + $taxTrans) * -1;
                                $data['documentLocalAmount'] = \Helper::roundValue($masterData->detail[0]->localAmount + $poInvoiceDirectLocalExtCharge + $taxLocal) * -1;
                                $data['documentRptAmount'] = \Helper::roundValue($masterData->detail[0]->rptAmount + $poInvoiceDirectRptExtCharge + $taxRpt) * -1;
                            } else if ($masterData->documentType == 3) { // check if it is supplier item invoice
                                $directItemCurrencyConversion = \Helper::currencyConversion($masterData->companySystemID, $masterData->supplierTransactionCurrencyID, $masterData->supplierTransactionCurrencyID, $masterData->item_details[0]->netAmountTotal);

                                $data['documentTransAmount'] = \Helper::roundValue($masterData->item_details[0]->netAmountTotal + $masterData->item_details[0]->totalVATAmount + $poInvoiceDirectTransExtCharge) * -1;
                                $data['documentLocalAmount'] = \Helper::roundValue($directItemCurrencyConversion['localAmount'] + $masterData->item_details[0]->totalVATAmountLocal + $poInvoiceDirectLocalExtCharge) * -1;
                                $data['documentRptAmount'] = \Helper::roundValue($directItemCurrencyConversion['reportingAmount'] + $masterData->item_details[0]->totalVATAmountRpt + $poInvoiceDirectRptExtCharge) * -1;
                            } else { // check if it is direct invoice
                                if($masterData->documentType == 1 && $masterData->rcmActivated){
                                    $data['documentTransAmount'] = \Helper::roundValue($masterData->directdetail[0]->transAmount) * -1;
                                    $data['documentLocalAmount'] = \Helper::roundValue($masterData->directdetail[0]->localAmount) * -1;
                                    $data['documentRptAmount'] = \Helper::roundValue($masterData->directdetail[0]->rptAmount) * -1;
                                }else{
                                    $data['documentTransAmount'] = \Helper::roundValue($masterData->directdetail[0]->transAmount + $taxTrans) * -1;
                                    $data['documentLocalAmount'] = \Helper::roundValue($masterData->directdetail[0]->localAmount + $taxLocal) * -1;
                                    $data['documentRptAmount'] = \Helper::roundValue($masterData->directdetail[0]->rptAmount + $taxRpt ) * -1;
                                }
                            }
                        
                            $retentionTrans = 0;
                            $retentionLocal = 0;
                            $retentionRpt = 0;
                            if ($retentionPercentage > 0) {
                                if ($masterData->documentType != 4) {

                                    if($masterData->documentType == 3){
                                        $directVATDetails = TaxService::processSupplierInvoiceItemsVAT($masterModel["autoID"]);
                                        $totalVATAmount = 0;
                                        $totalVATAmountLocal = 0;
                                        $totalVATAmountRpt = 0;
                                        $totalVATAmount = \Helper::roundValue(ABS($directVATDetails['masterVATTrans']));
                                        $totalVATAmountLocal = \Helper::roundValue(ABS($directVATDetails['masterVATLocal']));
                                        $totalVATAmountRpt = \Helper::roundValue(ABS($directVATDetails['masterVATRpt']));

                                            $retentionTransWithoutVat = ($data['documentTransAmount'] + ABS($totalVATAmount)) * ($retentionPercentage / 100);
                                            $retentionLocalWithoutVat = ($data['documentLocalAmount'] + ABS($totalVATAmountLocal)) * ($retentionPercentage / 100);
                                            $retentionRptWithoutVat = ($data['documentRptAmount'] + ABS($totalVATAmountRpt)) * ($retentionPercentage / 100);

                                    }


                                    else if($masterData->documentType == 1){
                                        $directVATDetails = TaxService::processDirectSupplierInvoiceVAT($masterModel["autoID"],
                                        $masterModel["documentSystemID"]);
                                        $totalVATAmount = 0;
                                        $totalVATAmountLocal = 0;
                                        $totalVATAmountRpt = 0;
                                        $totalVATAmount = \Helper::roundValue(ABS($directVATDetails['masterVATTrans']));
                                        $totalVATAmountLocal = \Helper::roundValue(ABS($directVATDetails['masterVATLocal']));
                                        $totalVATAmountRpt = \Helper::roundValue(ABS($directVATDetails['masterVATRpt']));
                                        if($masterData->rcmActivated != 1){
                                            $retentionTransWithoutVat = ($data['documentTransAmount'] + ABS($totalVATAmount)) * ($retentionPercentage / 100);
                                            $retentionLocalWithoutVat = ($data['documentLocalAmount'] + ABS($totalVATAmountLocal)) * ($retentionPercentage / 100);
                                            $retentionRptWithoutVat = ($data['documentRptAmount'] + ABS($totalVATAmountRpt)) * ($retentionPercentage / 100);
                                        }
                                        else{
                                            $retentionTrans = $data['documentTransAmount'] * ($retentionPercentage/100);
                                            $retentionLocal = $data['documentLocalAmount'] * ($retentionPercentage/100);
                                            $retentionRpt = $data['documentRptAmount'] * ($retentionPercentage/100);
                                        }
                                    }


                                    else if ($masterData->documentType == 0) {
                                        $vatDetails = TaxService::processPoBasedSupllierInvoiceVAT($masterModel["autoID"]);
                                        $totalVATAmount = 0;
                                        $totalVATAmountLocal = 0;
                                        $totalVATAmountRpt = 0;
                                        $totalVATAmount = $vatDetails['totalVAT'];
                                        $totalVATAmountLocal = $vatDetails['totalVATLocal'];
                                        $totalVATAmountRpt = $vatDetails['totalVATRpt'];
                                        if (!TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {                        $retentionTransWithoutVat = ($data['documentTransAmount'] + ABS($totalVATAmount)) * ($retentionPercentage / 100);
                                            $retentionLocalWithoutVat = ($data['documentLocalAmount'] + ABS($totalVATAmountLocal)) * ($retentionPercentage / 100);
                                            $retentionRptWithoutVat = ($data['documentRptAmount'] + ABS($totalVATAmountRpt)) * ($retentionPercentage / 100);
                                        }
                                        else{
                                            $retentionTrans = $data['documentTransAmount'] * ($retentionPercentage/100);
                                            $retentionLocal = $data['documentLocalAmount'] * ($retentionPercentage/100);
                                            $retentionRpt = $data['documentRptAmount'] * ($retentionPercentage/100);
                                        }
                                    }
                                    else{
                                        $retentionTrans = $data['documentTransAmount'] * ($retentionPercentage/100);
                                        $retentionLocal = $data['documentLocalAmount'] * ($retentionPercentage/100);
                                        $retentionRpt = $data['documentRptAmount'] * ($retentionPercentage/100);
                                    }

                                    $data['documentTransAmount'] = $data['documentTransAmount'] * (1-($retentionPercentage/100));
                                    $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1-($retentionPercentage/100));
                                    $data['documentRptAmount'] = $data['documentRptAmount'] * (1-($retentionPercentage/100));
                                }
                            } 

                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['documentType'] = $masterData->documentType;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);

                            if ($retentionPercentage > 0) {
                                if ($masterData->documentType != 4) {
                                    $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 13);
                                    $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 13);
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    if ($masterData->documentType == 0) {
                                        if (!TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {

                                            $data['documentTransAmount'] = $retentionTransWithoutVat;
                                            $data['documentLocalAmount'] = $retentionLocalWithoutVat;
                                            $data['documentRptAmount'] = $retentionRptWithoutVat;
                                        }
                                        else{
                                            $data['documentTransAmount'] = $retentionTrans;
                                            $data['documentLocalAmount'] = $retentionLocal;
                                            $data['documentRptAmount'] = $retentionRpt;
                                        }
                                    }
                                    else if($masterData->documentType == 1){
                                        if($masterData->rcmActivated != 1){
                                            $data['documentTransAmount'] = $retentionTransWithoutVat;
                                            $data['documentLocalAmount'] = $retentionLocalWithoutVat;
                                            $data['documentRptAmount'] = $retentionRptWithoutVat;
                                        }
                                        else{
                                            $data['documentTransAmount'] = $retentionTrans;
                                            $data['documentLocalAmount'] = $retentionLocal;
                                            $data['documentRptAmount'] = $retentionRpt;
                                        }

                                    }
                                    else if($masterData->documentType == 3){
                                        $data['documentTransAmount'] = $retentionTransWithoutVat;
                                        $data['documentLocalAmount'] = $retentionLocalWithoutVat;
                                        $data['documentRptAmount'] = $retentionRptWithoutVat;
                                    }
                                    else{
                                        $data['documentTransAmount'] = $retentionTrans;
                                        $data['documentLocalAmount'] = $retentionLocal;
                                        $data['documentRptAmount'] = $retentionRpt;
                                    }
                                    array_push($finalData, $data);
                                }
                            }
                            if ($masterData->documentType == 0 || $masterData->documentType == 2) {
                                $data['chartOfAccountSystemID'] = $masterData->UnbilledGRVAccountSystemID;
                                $data['glCode'] = $masterData->UnbilledGRVAccount;
                                $data['documentTransAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->transAmount));
                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->localAmount));
                                $data['documentRptAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->rptAmount));
                                array_push($finalData, $data);

                                if ($bs) {
                                    foreach ($bs as $val) {
                                        $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                        $data['serviceLineCode'] = $val->serviceLineCode;
                                        $data['chartOfAccountSystemID'] = $val->financeGLcodebBSSystemID;
                                        $data['glCode'] = $val->financeGLcodebBS;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentNarration'] = $val->comments;
                                        $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                                        $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                                        $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount));
                                        $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                        $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount));
                                        $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                                        $data['documentRptCurrencyER'] = $val->companyReportingER;
                                        $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount));
                                        $data['timestamp'] = \Helper::currentDateTime();
                                        array_push($finalData, $data);
                                    }
                                }
                            } else if ($masterData->documentType == 3) {
                                if ($bsItemDirect) {
                                    foreach ($bsItemDirect as $val) {

                                        $transBSVAT = isset($directItemVatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['transVATAmount']) ? $directItemVatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['transVATAmount'] : 0;
                                        $rptBSVAT = isset($directItemVatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['rptVATAmount']) ? $directItemVatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['rptVATAmount'] : 0;
                                        $localBSVAT = isset($directItemVatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['localVATAmount']) ? $directItemVatDetails['bsVAT'][$val->financeGLcodebBSSystemID]['localVATAmount'] : 0;

                                        $exemptVATTransAmount = isset($directItemVatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATTransAmount']) ? $directItemVatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATTransAmount'] : 0;
                                        $exemptVATLocalAmount = isset($directItemVatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATLocalAmount']) ? $directItemVatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATLocalAmount'] : 0;
                                        $exemptVATRptAmount = isset($directItemVatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATRptAmount']) ? $directItemVatDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID]['exemptVATRptAmount'] : 0;

                                        $data['chartOfAccountSystemID'] = $val->financeGLcodebBSSystemID;
                                        $data['glCode'] = ChartOfAccount::getAccountCode($val->financeGLcodebBSSystemID);
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                                        $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                                        $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount) + $transBSVAT + $exemptVATTransAmount);

                                        $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                        $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount) + $localBSVAT + $exemptVATLocalAmount);

                                        $data['documentRptCurrencyID'] = $val->companyReportingCurrencyID;
                                        $data['documentRptCurrencyER'] = $val->companyReportingER;
                                        $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount) + $rptBSVAT + $exemptVATRptAmount);
                                        $data['timestamp'] = \Helper::currentDateTime();
                                        array_push($finalData, $data);
                                    }
                                }

                                if ($plItemDirect) {
                                    foreach ($plItemDirect as $val) {

                                        $transPLVAT = isset($directItemVatDetails['plVAT'][$val->financeGLcodePLSystemID]['transVATAmount']) ? $directItemVatDetails['plVAT'][$val->financeGLcodePLSystemID]['transVATAmount'] : 0;
                                        $rptPLVAT = isset($directItemVatDetails['plVAT'][$val->financeGLcodePLSystemID]['rptVATAmount']) ? $directItemVatDetails['plVAT'][$val->financeGLcodePLSystemID]['rptVATAmount'] : 0;
                                        $localPLVAT = isset($directItemVatDetails['plVAT'][$val->financeGLcodePLSystemID]['localVATAmount']) ? $directItemVatDetails['plVAT'][$val->financeGLcodePLSystemID]['localVATAmount'] : 0;

                                        $exemptVATTransAmount = isset($directItemVatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATTransAmount']) ? $directItemVatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATTransAmount'] : 0;
                                        $exemptVATLocalAmount = isset($directItemVatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATLocalAmount']) ? $directItemVatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATLocalAmount'] : 0;
                                        $exemptVATRptAmount = isset($directItemVatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATRptAmount']) ? $directItemVatDetails['exemptVATportionPL'][$val->financeGLcodebBSSystemID]['exemptVATRptAmount'] : 0;

                                        $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                        $data['glCode'] = ChartOfAccount::getAccountCode($val->financeGLcodePLSystemID);
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                                        $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                                        $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount) + $transPLVAT + $exemptVATTransAmount);

                                        $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                        $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount) + $localPLVAT + $exemptVATLocalAmount);

                                        $data['documentRptCurrencyID'] = $val->companyReportingCurrencyID;
                                        $data['documentRptCurrencyER'] = $val->companyReportingER;
                                        $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount) + $rptPLVAT + $exemptVATRptAmount);
                                        $data['timestamp'] = \Helper::currentDateTime();
                                        array_push($finalData, $data);
                                    }
                                }


                                if ($bs) {
                                    foreach ($bs as $val) {
                                        $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                        $data['serviceLineCode'] = $val->serviceLineCode;
                                        $data['chartOfAccountSystemID'] = $val->financeGLcodebBSSystemID;
                                        $data['glCode'] = $val->financeGLcodebBS;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentNarration'] = $val->comments;
                                        $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                                        $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                                        $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount));
                                        $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                        $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount));
                                        $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                                        $data['documentRptCurrencyER'] = $val->companyReportingER;
                                        $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount));
                                        $data['timestamp'] = \Helper::currentDateTime();
                                        array_push($finalData, $data);
                                    }
                                }
                            }
                            else {
                                if ($bs) {
                                    foreach ($bs as $val) {
                                        $transBSVAT = isset($directVATDetails['bsVAT'][$val->financeGLcodebBSSystemID.$val->serviceLineSystemID.$val->comments]['transVATAmount']) ? $directVATDetails['bsVAT'][$val->financeGLcodebBSSystemID.$val->serviceLineSystemID.$val->comments]['transVATAmount'] : 0;
                                        $rptBSVAT = isset($directVATDetails['bsVAT'][$val->financeGLcodebBSSystemID.$val->serviceLineSystemID.$val->comments]['rptVATAmount']) ? $directVATDetails['bsVAT'][$val->financeGLcodebBSSystemID.$val->serviceLineSystemID.$val->comments]['rptVATAmount'] : 0;
                                        $localBSVAT = isset($directVATDetails['bsVAT'][$val->financeGLcodebBSSystemID.$val->serviceLineSystemID.$val->comments]['localVATAmount']) ? $directVATDetails['bsVAT'][$val->financeGLcodebBSSystemID.$val->serviceLineSystemID.$val->comments]['localVATAmount'] : 0;

                                        $exemptVATTransAmount = isset($directVATDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID.$val->serviceLineSystemID.$val->comments]['exemptVATTransAmount']) ? $directVATDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID.$val->serviceLineSystemID.$val->comments]['exemptVATTransAmount'] : 0;
                                        $exemptVATLocalAmount = isset($directVATDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID.$val->serviceLineSystemID.$val->comments]['exemptVATLocalAmount']) ? $directVATDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID.$val->serviceLineSystemID.$val->comments]['exemptVATLocalAmount'] : 0;
                                        $exemptVATRptAmount = isset($directVATDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID.$val->serviceLineSystemID.$val->comments]['exemptVATRptAmount']) ? $directVATDetails['exemptVATportionBs'][$val->financeGLcodebBSSystemID.$val->serviceLineSystemID.$val->comments]['exemptVATRptAmount'] : 0;


                                        $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                        $data['serviceLineCode'] = $val->serviceLineCode;
                                        $data['chartOfAccountSystemID'] = $val->financeGLcodebBSSystemID;
                                        $data['glCode'] = $val->financeGLcodebBS;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentNarration'] = $val->comments;
                                        $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                                        $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                                        $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount) + abs($transBSVAT) + abs($exemptVATTransAmount));
                                        $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                        $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount) + abs($localBSVAT) + abs($exemptVATLocalAmount));
                                        $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                                        $data['documentRptCurrencyER'] = $val->companyReportingER;
                                        $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount) + abs($rptBSVAT) + abs($exemptVATRptAmount));
                                        $data['timestamp'] = \Helper::currentDateTime();
                                        array_push($finalData, $data);
                                    }
                                }
                            }


                            //VAT entries
                            $vatDetails = TaxService::processPoBasedSupllierInvoiceVAT($masterModel["autoID"]);
                            $totalVATAmount = $vatDetails['totalVAT'];
                            $totalExemptVAT = $vatDetails['exemptVAT'];
                            $totalVATAmountLocal = $vatDetails['totalVATLocal'];
                            $totalVATAmountRpt = $vatDetails['totalVATRpt'];

                            if (($masterData->documentType == 0 || $masterData->documentType == 2) && $masterData->detail && count($masterData->detail) > 0 && ($totalVATAmount > 0 || $vatDetails['exemptVAT'] > 0)) {

                                if ($totalVATAmount > 0) {
                                    // Input VAT control
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
                                            $data['documentTransAmount'] = \Helper::roundValue(ABS($totalVATAmount));
                                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($totalVATAmountLocal));
                                            $data['documentRptAmount'] = \Helper::roundValue(ABS($totalVATAmountRpt));

                                            if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                                $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage/100));
                                                $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage/100));
                                                $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage/100));
                                            }

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


                                    //Input VAT transfer
                                    $taxConfigData = TaxService::getInputVATTransferGLAccount($masterModel["companySystemID"]);
                                    if (!empty($taxConfigData)) {
                                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->inputVatTransferGLAccountAutoID)
                                            ->where('companySystemID', $masterData->companySystemID)
                                            ->first();

                                        if (!empty($chartOfAccountData)) {
                                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            $data['glCode'] = $chartOfAccountData->AccountCode;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                                $data['documentTransAmount'] = \Helper::roundValue(ABS($totalVATAmount)) * -1;
                                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($totalVATAmountLocal)) * -1;
                                                $data['documentRptAmount'] = \Helper::roundValue(ABS($totalVATAmountRpt)) * -1;


                                            if (TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {
                                                if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                                    $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage / 100));
                                                    $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage / 100));
                                                    $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage / 100));
                                                }
                                            }

                                            array_push($finalData, $data);

                                            $taxLedgerData['inputVatTransferAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                        } else {
                                            Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                            Log::info('Input Vat GL Account not assigned to company' . date('H:i:s'));
                                        }
                                    } else {
                                        Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Input Vat Transfer GL Account not configured' . date('H:i:s'));
                                    }
                                }

                                if (TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {
                                    // output vat transfer entry
                                    $taxOutputVATTransfer= TaxService::getOutputVATTransferGLAccount($masterModel["companySystemID"]);
                                    if (!empty($taxOutputVATTransfer)) {
                                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxOutputVATTransfer->outputVatTransferGLAccountAutoID)
                                            ->where('companySystemID', $masterData->companySystemID)
                                            ->first();

                                        if (!empty($chartOfAccountData)) {
                                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            $data['glCode'] = $chartOfAccountData->AccountCode;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                            $data['documentTransAmount'] = \Helper::roundValue(ABS(($vatDetails['totalVAT'] + $vatDetails['exemptVAT'])));
                                            $data['documentLocalAmount'] = \Helper::roundValue(ABS(($vatDetails['totalVATLocal'] + $vatDetails['exemptVATLocal'])));
                                            $data['documentRptAmount'] = \Helper::roundValue(ABS(($vatDetails['totalVATRpt'] + $vatDetails['exemptVATRpt'])));
                                            
                                            if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                                $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage/100));
                                                $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage/100));
                                                $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage/100));
                                            }

                                            array_push($finalData, $data);

                                            $taxLedgerData['outputVatTransferGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                        } else {
                                            Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                            Log::info('Output Vat transfer GL Account not assigned to company' . date('H:i:s'));
                                        }
                                    } else {
                                        Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Output Vat transfer GL Account not configured' . date('H:i:s'));
                                    }

                                     //output vat entry
                                    $taxOutputVAT = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);
                                    if (!empty($taxOutputVAT)) {
                                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxOutputVAT->outputVatGLAccountAutoID)
                                            ->where('companySystemID', $masterData->companySystemID)
                                            ->first();

                                        if (!empty($chartOfAccountData)) {
                                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            $data['glCode'] = $chartOfAccountData->AccountCode;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                            $data['documentTransAmount'] = \Helper::roundValue(ABS(($vatDetails['totalVAT'] + $vatDetails['exemptVAT']))) * -1;
                                            $data['documentLocalAmount'] = \Helper::roundValue(ABS(($vatDetails['totalVATLocal'] + $vatDetails['exemptVATLocal']))) * -1;
                                            $data['documentRptAmount'] = \Helper::roundValue(ABS(($vatDetails['totalVATRpt'] + $vatDetails['exemptVATRpt']))) * -1;
                                            
                                            if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                                $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage/100));
                                                $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage/100));
                                                $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage/100));
                                            }

                                            array_push($finalData, $data);

                                            $taxLedgerData['outputVatGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                        } else {
                                            Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                            Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                                        }
                                    } else {
                                        Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Output Vat GL Account not configured' . date('H:i:s'));
                                    }
                                }
                            } else if ($masterData->documentType == 3 && $masterData->item_details && count($masterData->item_details) > 0 && $masterData->item_details[0]->totalVATAmount > 0 && $directItemVatDetails['masterVATTrans']) {
                                
                                Log::info('Inside the Vat Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                $taxData = TaxService::getInputVATGLAccount($masterData->companySystemID);

                                if ($directItemVatDetails['masterVATTrans'] > 0) {
                                    if (!empty($taxData)) {
                                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxData->inputVatGLAccountAutoID)
                                            ->where('companySystemID', $masterData->companySystemID)
                                            ->first();

                                        if (!empty($chartOfAccountData)) {
                                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            $data['glCode'] = $chartOfAccountData->AccountCode;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                            $data['documentTransAmount'] = \Helper::roundValue($directItemVatDetails['masterVATTrans']);
                                            $data['documentLocalAmount'] = \Helper::roundValue($directItemVatDetails['masterVATLocal']);
                                            $data['documentRptAmount'] = \Helper::roundValue($directItemVatDetails['masterVATRpt']);

                                            if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                                $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage/100));
                                                $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage/100));
                                                $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage/100));
                                            }

                                            array_push($finalData, $data);

                                            $taxLedgerData['inputVATGlAccountID'] = $chartOfAccountData->chartOfAccountSystemID;

                                            Log::info('Inside the Vat Entry InputVATTransferGLAccount Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        } else {
                                            Log::info('GRV VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                            Log::info('Input Vat Transfer GL Account not assigned to company' . date('H:i:s'));
                                        }
                                    } else {
                                        Log::info('GRV VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Input Vat Transfer GL Account not configured' . date('H:i:s'));
                                    }

                                    if (TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {
                                        if ($retentionPercentage > 0) {
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

                                                    $data['documentTransAmount'] = \Helper::roundValue($directItemVatDetails['masterVATTrans']);
                                                    $data['documentLocalAmount'] = \Helper::roundValue($directItemVatDetails['masterVATLocal']);
                                                    $data['documentRptAmount'] = \Helper::roundValue($directItemVatDetails['masterVATRpt']);

                                                    if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                                        $data['documentTransAmount'] = $data['documentTransAmount'] * ($retentionPercentage / 100);
                                                        $data['documentLocalAmount'] = $data['documentLocalAmount'] * ($retentionPercentage / 100);
                                                        $data['documentRptAmount'] = $data['documentRptAmount'] * ($retentionPercentage / 100);
                                                    }

                                                    array_push($finalData, $data);

                                                    $taxLedgerData['inputVatTransferAccountID'] = $chartOfAccountData->chartOfAccountSystemID;

                                                    Log::info('Inside the Vat Entry InputVATTransferGLAccount Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                                } else {
                                                    Log::info('GRV VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                                    Log::info('Input Vat Transfer GL Account not assigned to company' . date('H:i:s'));
                                                }
                                            } else {
                                                Log::info('GRV VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                                Log::info('Input Vat Transfer GL Account not configured' . date('H:i:s'));
                                            }
                                        }
                                    }

                                }
                            }


                            if ($tax && $directVATDetails['masterVATTrans'] > 0) {
                                //input vat entry
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
                                        $data['documentTransAmount'] = \Helper::roundValue(ABS($directVATDetails['masterVATTrans']));
                                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($directVATDetails['masterVATLocal']));
                                        $data['documentRptAmount'] = \Helper::roundValue(ABS($directVATDetails['masterVATRpt']));
                                        
                                        if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                            $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage/100));
                                            $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage/100));
                                            $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage/100));
                                        }

                                        array_push($finalData, $data);

                                        $taxLedgerData['inputVATGlAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                    } else {
                                        Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Input Vat GL Account not assigned to company' . date('H:i:s'));
                                    }
                                } else {
                                    Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                    Log::info('Input Vat GL Account not configured' . date('H:i:s'));
                                }

                                if (TaxService::isSupplierInvoiceRcmActivated($masterModel["autoID"])) {
                                    if ($retentionPercentage > 0 && $masterData->documentType == 1) {
                                        $taxConfigData = TaxService::getInputVATTransferGLAccount($masterModel["companySystemID"]);
                                        if (!empty($taxConfigData)) {
                                            $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->inputVatTransferGLAccountAutoID)
                                                ->where('companySystemID', $masterData->companySystemID)
                                                ->first();

                                            if (!empty($chartOfAccountData)) {
                                                $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                                $data['glCode'] = $chartOfAccountData->AccountCode;
                                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                                $data['documentTransAmount'] = \Helper::roundValue(ABS($directVATDetails['masterVATTrans']));
                                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($directVATDetails['masterVATLocal']));
                                                $data['documentRptAmount'] = \Helper::roundValue(ABS($directVATDetails['masterVATRpt']));

                                                if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                                    $data['documentTransAmount'] = $data['documentTransAmount'] * ($retentionPercentage / 100);
                                                    $data['documentLocalAmount'] = $data['documentLocalAmount'] * ($retentionPercentage / 100);
                                                    $data['documentRptAmount'] = $data['documentRptAmount'] * ($retentionPercentage / 100);
                                                }

                                                array_push($finalData, $data);

                                                $taxLedgerData['inputVatTransferAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            } else {
                                                Log::info('Supplier Invoice VAT Transfer GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                                Log::info('Input Vat Transfer GL Account not assigned to company' . date('H:i:s'));
                                            }
                                        } else {
                                            Log::info('Supplier Invoice VAT Transfer GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                            Log::info('Input Vat Transfer GL Account not configured' . date('H:i:s'));
                                        }
                                    }
                                }


                                //if rcm activated tax entries
                                if($masterData->rcmActivated == 1){
                                    if ($masterData->documentType == 0 || $masterData->documentType == 2) {
                                        // input vat transfer entry
                                        $taxInputVATTransfer = TaxService::getInputVATTransferGLAccount($masterModel["companySystemID"]);
                                        if (!empty($taxConfigData)) {
                                            $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxInputVATTransfer->inputVatTransferGLAccountAutoID)
                                                ->where('companySystemID', $masterData->companySystemID)
                                                ->first();

                                            if (!empty($chartOfAccountData)) {
                                                $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                                $data['glCode'] = $chartOfAccountData->AccountCode;
                                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                                $data['documentTransAmount'] = \Helper::roundValue(ABS($taxTrans)) * -1;
                                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($taxLocal)) * -1;
                                                $data['documentRptAmount'] = \Helper::roundValue(ABS($taxRpt)) * -1;

                                                if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                                    $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage/100));
                                                    $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage/100));
                                                    $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage/100));
                                                }

                                                array_push($finalData, $data);

                                                $taxLedgerData['inputVatTransferAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            } else {
                                                Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                                Log::info('Input Vat transfer GL Account not assigned to company' . date('H:i:s'));
                                            }
                                        } else {
                                            Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                            Log::info('Input Vat transfer GL Account not configured' . date('H:i:s'));
                                        }

                                        // output vat transfer entry
                                        $taxOutputVATTransfer= TaxService::getOutputVATTransferGLAccount($masterModel["companySystemID"]);
                                        if (!empty($taxConfigData)) {
                                            $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxOutputVATTransfer->outputVatTransferGLAccountAutoID)
                                                ->where('companySystemID', $masterData->companySystemID)
                                                ->first();

                                            if (!empty($chartOfAccountData)) {
                                                $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                                $data['glCode'] = $chartOfAccountData->AccountCode;
                                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                                $data['documentTransAmount'] = \Helper::roundValue(ABS($taxTrans));
                                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($taxLocal));
                                                $data['documentRptAmount'] = \Helper::roundValue(ABS($taxRpt));
                                                
                                                // if ($retentionPercentage > 0 && $masterData->documentType != 4) {
                                                //     $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage/100));
                                                //     $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage/100));
                                                //     $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage/100));
                                                // }

                                                array_push($finalData, $data);

                                                $taxLedgerData['outputVatTransferGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            } else {
                                                Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                                Log::info('Output Vat transfer GL Account not assigned to company' . date('H:i:s'));
                                            }
                                        } else {
                                            Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                            Log::info('Output Vat transfer GL Account not configured' . date('H:i:s'));
                                        }
                                    }


                                    //output vat entry
                                    $taxOutputVAT = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);
                                    if (!empty($taxConfigData)) {
                                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxOutputVAT->outputVatGLAccountAutoID)
                                            ->where('companySystemID', $masterData->companySystemID)
                                            ->first();

                                        if (!empty($chartOfAccountData)) {
                                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                            $data['glCode'] = $chartOfAccountData->AccountCode;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                            $data['documentTransAmount'] = \Helper::roundValue(ABS($taxTrans)) * -1;
                                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($taxLocal)) * -1;
                                            $data['documentRptAmount'] = \Helper::roundValue(ABS($taxRpt)) * -1;
                                            
                                            if ($retentionPercentage > 0 && $masterData->documentType == 1) {
                                                $data['documentTransAmount'] = $data['documentTransAmount'] * (1 - ($retentionPercentage/100));
                                                $data['documentLocalAmount'] = $data['documentLocalAmount'] * (1 - ($retentionPercentage/100));
                                                $data['documentRptAmount'] = $data['documentRptAmount'] * (1 - ($retentionPercentage/100));
                                            }

                                            array_push($finalData, $data);

                                            $taxLedgerData['outputVatGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                        } else {
                                            Log::info('Supplier Invoice VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                            Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                                        }
                                    } else {
                                        Log::info('Supplier Invoice VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Output Vat GL Account not configured' . date('H:i:s'));
                                    }

                                }
                            }
                        }
                        break;
                    case 15: // DN - Debit Note
                        $masterData = DebitNote::with(['detail' => function ($query) {
                            $query->selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(debitAmount) as transAmount,debitNoteAutoID");
                        }, 'finance_period_by'])->find($masterModel["autoID"]);

                        //all account
                        $allAcc = DebitNoteDetails::with(['chartofaccount'])
                            ->selectRaw("SUM(netAmountLocal) as localAmount, SUM(netAmountRpt) as rptAmount,SUM(netAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,debitAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,debitAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode,comments,chartOfAccountSystemID")
                            ->where('debitNoteAutoID', $masterModel["autoID"])
                            ->whereNotNull('serviceLineSystemID')
                            ->whereNotNull('chartOfAccountSystemID')
                            ->groupBy('serviceLineSystemID', 'chartOfAccountSystemID', 'comments')
                            ->get();

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if ($masterData->finance_period_by->isActive == -1) {
                            $masterDocumentDate = $masterData->debitNoteDate;
                        }

                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['serviceLineSystemID'] = 24;
                            $data['serviceLineCode'] = 'X';
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->debitNoteCode;
                            $data['documentDate'] = $masterDocumentDate;
                            $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->comments;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = $masterData->supplierID;

                            if($masterData->type == 1)
                            {
                                $data['chartOfAccountSystemID'] = $masterData->supplierGLCodeSystemID;
                                $data['glCode'] = $masterData->supplierGLCode;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                            }
                            else if($masterData->type == 2)
                            {

                                $emp_control_acc = SystemGlCodeScenarioDetail::where('systemGlScenarioID',12)->where('companySystemID',$masterData->companySystemID)->first();
                                if(isset($emp_control_acc))
                                {
                                    $emp_chart_acc = $emp_control_acc->chartOfAccountSystemID;
                                    if(!empty($emp_chart_acc) && $emp_chart_acc != null)
                                    {
                                        $data['chartOfAccountSystemID'] = $emp_chart_acc;
                                        $data['glCode'] = ChartOfAccount::getGlAccountCode($data['chartOfAccountSystemID']);
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    }
                                }
                            }

                           
                            $data['documentTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->supplierTransactionCurrencyER;
                            $data['documentTransAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->transAmount));
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->localAmount));
                            $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                            $data['documentRptAmount'] = \Helper::roundValue(ABS($masterData->detail[0]->rptAmount));

                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['documentType'] = $masterData->documentType;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);

                            $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER,payeeSystemCode")
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
                            }

                            if ($tax) {
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
                                        $data['documentTransAmount'] = \Helper::roundValue(ABS($taxTrans)) * -1;
                                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($taxLocal)) * -1;
                                        $data['documentRptAmount'] = \Helper::roundValue(ABS($taxRpt)) * -1;
                                        array_push($finalData, $data);

                                        $taxLedgerData['inputVATGlAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                    } else {
                                        Log::info('Debit Note VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Input Vat GL Account not assigned to company' . date('H:i:s'));
                                    }
                                } else {
                                    Log::info('Debit Note VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                    Log::info('Input Vat GL Account not configured' . date('H:i:s'));
                                }
                            }


                            if ($allAcc) {
                                foreach ($allAcc as $val) {
                                    $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                    $data['serviceLineCode'] = $val->serviceLineCode;
                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['documentNarration'] = $val->comments;
                                    $data['glCode'] = $val->financeGLcodePL;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $val->transCurrencyID;
                                    $data['documentTransCurrencyER'] = $val->transCurrencyER;
                                    $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount) * -1);
                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount) * -1);
                                    $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                                    $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount) * -1);
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        break;
                    case 19: // CN - Credit Note
                        $masterData = CreditNote::with(['details' => function ($query) {
                            $query->selectRaw('SUM(netAmountLocal) as localAmount, SUM(netAmountRpt) as rptAmount,SUM(netAmount) as transAmount,creditNoteAutoID,serviceLineSystemID,serviceLineCode,clientContractID,contractUID');
                        }], 'finance_period_by')->find($masterModel["autoID"]);

                        //all acoount
                        $allAc = CreditNoteDetails::with(['chartofaccount'])
                            ->selectRaw("SUM(netAmountLocal) as localAmount, SUM(netAmountRpt) as rptAmount,SUM(netAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,creditAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,creditAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode,clientContractID,contractUID,comments,chartOfAccountSystemID")
                            ->WHERE('creditNoteAutoID', $masterModel["autoID"])
                            ->groupBy('serviceLineSystemID', 'chartOfAccountSystemID', 'clientContractID', 'comments')
                            ->get();

                        $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER")
                            ->WHERE('documentSystemCode', $masterModel["autoID"])
                            ->WHERE('documentSystemID', $masterModel["documentSystemID"])
                            ->groupBy('documentSystemCode')
                            ->first();

                        $taxGLCode = Company::find($masterModel["companySystemID"]);

                        $taxLocal = 0;
                        $taxRpt = 0;
                        $taxTrans = 0;

                        if ($tax) {
                            $taxLocal = $tax->localAmount;
                            $taxRpt = $tax->rptAmount;
                            $taxTrans = $tax->transAmount;
                        }

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if ($masterData->finance_period_by->isActive == -1) {
                            $masterDocumentDate = $masterData->creditNoteDate;
                        }

                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemiD;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->creditNoteCode;
                            $data['documentDate'] = $masterDocumentDate;
                            $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->comments;

                            $data['chartOfAccountSystemID'] = $masterData->customerGLCodeSystemID;
                            $data['glCode'] = $masterData->customerGLCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                            $data['documentTransCurrencyID'] = $masterData->customerCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->customerCurrencyER;
                            $data['documentTransAmount'] = \Helper::roundValue(ABS($masterData->details[0]->transAmount + $taxTrans)) * -1;
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($masterData->details[0]->localAmount + $taxLocal)) * -1;
                            $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                            $data['documentRptAmount'] = \Helper::roundValue(ABS($masterData->details[0]->rptAmount + $taxRpt)) * -1;
                            if ($masterData->details[0]->serviceLineSystemID) {
                                $data['serviceLineSystemID'] = $masterData->details[0]->serviceLineSystemID;
                                $data['serviceLineCode'] = $masterData->details[0]->serviceLineCode;
                            } else {
                                $data['serviceLineSystemID'] = 24;
                                $data['serviceLineCode'] = 'X';
                            }
                            if ($masterData->details[0]->clientContractID) {
                                $data['clientContractID'] = $masterData->details[0]->clientContractID;
                                $data['contractUID'] = $masterData->details[0]->contractUID;
                            } else {
                                $data['clientContractID'] = 'X';
                                $data['contractUID'] = 159;
                            }
                            $data['supplierCodeSystem'] = $masterData->customerID;
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['chequeNumber'] = 0;
                            $data['invoiceNumber'] = 0;
                            $data['documentType'] = $masterData->documentType;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);

                            if ($allAc) {
                                foreach ($allAc as $val) {
                                    if ($val->serviceLineSystemID) {
                                        $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                        $data['serviceLineCode'] = $val->serviceLineCode;
                                    } else {
                                        $data['serviceLineSystemID'] = 24;
                                        $data['serviceLineCode'] = 'X';
                                    }

                                    if ($val->clientContractID) {
                                        $data['clientContractID'] = $val->clientContractID;
                                        $data['contractUID'] = $val->contractUID;
                                    } else {
                                        $data['clientContractID'] = 'X';
                                        $data['contractUID'] = 159;
                                    }

                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['glCode'] = $val->financeGLcodePL;
                                    $data['documentNarration'] = $val->comments;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentNarration'] = $val->comments;
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
                                    array_push($finalData, $data);
                                }
                            }

                            if ($tax) {

                                $taxConfigData = TaxService::getOutputVATGLAccount($masterModel["companySystemID"]);

                                if (!empty($taxConfigData)) {
                                    $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxConfigData->outputVatGLAccountAutoID)
                                        ->where('companySystemID', $masterData->companySystemID)
                                        ->first();

                                    if (!empty($chartOfAccountData)) {
                                        $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                        $data['glCode'] = $chartOfAccountData->AccountCode;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                        $taxLedgerData['outputVatGLAccountID'] = $chartOfAccountData->chartOfAccountSystemID;
                                    } else {
                                        Log::info('Credit Note VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                                    }
                                } else {
                                    Log::info('Credit Note VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                    Log::info('Output Vat GL Account not configured' . date('H:i:s'));
                                }

                                $data['serviceLineSystemID'] = 24;
                                $data['serviceLineCode'] = 'X';
                                $data['clientContractID'] = 'X';
                                $data['contractUID'] = 159;

                                $data['documentTransCurrencyID'] = $tax->supplierTransactionCurrencyID;
                                $data['documentTransCurrencyER'] = $tax->supplierTransactionER;
                                $data['documentTransAmount'] = \Helper::roundValue(ABS($taxTrans));
                                $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                                $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                                $data['documentLocalAmount'] = \Helper::roundValue(ABS($taxLocal));
                                $data['documentRptCurrencyID'] = $tax->reportingCurrencyID;
                                $data['documentRptCurrencyER'] = $tax->companyReportingER;
                                $data['documentRptAmount'] = \Helper::roundValue(ABS($taxRpt));
                                array_push($finalData, $data);
                            }

                        }
                        break;
                    case 4: // PV - Payment Voucher
                        $masterData = PaySupplierInvoiceMaster::with(['bank', 'financeperiod_by', 'transactioncurrency', 'localcurrency', 'rptcurrency'])->find($masterModel["autoID"]);

                        //get balancesheet account
                        $si = PaySupplierInvoiceDetail::selectRaw("SUM(paymentLocalAmount) as localAmount, SUM(paymentComRptAmount) as rptAmount,SUM(supplierPaymentAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierPaymentCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierPaymentER as transCurrencyER")->WHERE('PayMasterAutoId', $masterModel["autoID"])->WHERE('matchingDocID', 0)->first();

                        $siApData = PaySupplierInvoiceDetail::selectRaw("SUM(paymentLocalAmount) as localAmount, SUM(paymentComRptAmount) as rptAmount,SUM(supplierPaymentAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierPaymentCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierPaymentER as transCurrencyER")->WHERE('PayMasterAutoId', $masterModel["autoID"])->WHERE('matchingDocID', 0)->WHERE('isRetention', 0)->first();

                        $retentionData = PaySupplierInvoiceDetail::selectRaw("SUM(paymentLocalAmount) as localAmount, SUM(paymentComRptAmount) as rptAmount,SUM(supplierPaymentAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierPaymentCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierPaymentER as transCurrencyER")->WHERE('PayMasterAutoId', $masterModel["autoID"])->WHERE('matchingDocID', 0)->WHERE('isRetention', 1)->first();

                        $dp = DirectPaymentDetails::with(['chartofaccount'])->selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DPAmount) as transAmount,SUM(bankAmount) as bankAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DPAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,DPAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode,chartOfAccountSystemID,comments,bankCurrencyID,vatSubCategoryID,vatAmount,VATAmountLocal,VATAmountRpt")->WHERE('directPaymentAutoID', $masterModel["autoID"])->whereNotNull('serviceLineSystemID')->whereNotNull('chartOfAccountSystemID')->groupBy('serviceLineSystemID', 'chartOfAccountSystemID', 'comments')->get();

                        $dpTotal = DirectPaymentDetails::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(DPAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,DPAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,DPAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode")->WHERE('directPaymentAutoID', $masterModel["autoID"])->first();

                        $exemptVatTotal = DirectPaymentDetails::selectRaw("SUM(vatAmount) as vatAmount, SUM(VATAmountLocal) as VATAmountLocal, SUM(VATAmountRpt) as VATAmountRpt")->WHERE('directPaymentAutoID', $masterModel["autoID"])->WHERE('vatSubCategoryID', 3)->first();

                        $ap = AdvancePaymentDetails::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(supplierTransAmount) as transAmount,localCurrencyID,comRptCurrencyID as reportingCurrencyID,supplierTransCurrencyID as transCurrencyID,comRptER as reportingCurrencyER,localER as localCurrencyER,supplierTransER as transCurrencyER")->WHERE('PayMasterAutoId', $masterModel["autoID"])->first();

                        $isBankCheck = DirectPaymentDetails::WHERE('directPaymentAutoID', $masterModel["autoID"])->WHERE('glCodeIsBank', 1)->first();

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if ($masterData->financeperiod_by->isActive == -1) {
                            $masterDocumentDate = $masterData->BPVdate;
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

                                    $masterTransAmountTotal = $si->transAmount;
                                    $masterLocalAmountTotal = $si->localAmount;
                                    $masterRptAmountTotal = $si->rptAmount;

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
                                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                    $data['documentLocalAmount'] = \Helper::roundValue($siApData->localAmount);
                                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                    $data['documentRptAmount'] = \Helper::roundValue($siApData->rptAmount);
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    if ($siApData && $siApData->transAmount > 0) {
                                        array_push($finalData, $data);
                                    }

                                    if ($retentionData && $retentionData->transAmount > 0) {


                                            $retentionTransAmount = $retentionData->transAmount;
                                            $retentionLocalAmount = $retentionData->localAmount;
                                            $retentionRptAmount = $retentionData->rptAmount;


                                        $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 13);
                                        $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 13);
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentTransAmount'] = \Helper::roundValue($retentionTransAmount);
                                        $data['documentLocalAmount'] = \Helper::roundValue($retentionLocalAmount);
                                        $data['documentRptAmount'] = \Helper::roundValue($retentionRptAmount);
                                        array_push($finalData, $data);
                                    }

                                    if ($masterData->BPVbankCurrency == $masterData->supplierTransCurrencyID) {
                                        $transAmountTotal = $si->transAmount;
                                        $localAmountTotal = $si->localAmount;
                                        $rptAmountTotal = $si->rptAmount;

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
                                        $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 5) :$masterData->bank->chartOfAccountSystemID;
                                        $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 5) : $masterData->bank->glCodeLinked;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                        $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;


                                        $data['documentTransAmount'] = \Helper::roundValue($si->transAmount + $retationVATAmount) * -1;
                                        $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                        $data['documentLocalCurrencyER'] = $si->localAmount != 0 ? ($si->transAmount / $si->localAmount) : 0;
                                        $data['documentLocalAmount'] = \Helper::roundValue($si->localAmount + $retentionLocalVatAmount) * -1;
                                        $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                        $data['documentRptCurrencyER'] = $si->rptAmount != 0 ? ($si->transAmount / $si->rptAmount) : 0;
                                        $data['documentRptAmount'] = \Helper::roundValue($si->rptAmount + $retentionRptVatAmount) * -1;

                                        $retationRcmVATAmount = TaxService::calculateRCMRetentionVatAmount($masterModel["autoID"]);
                                        if ($retationRcmVATAmount > 0) {
                                            $data['documentTransAmount'] = \Helper::roundValue($si->transAmount) * -1;
                                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = $si->localAmount != 0 ? ($si->transAmount / $si->localAmount) : 0;
                                            $data['documentLocalAmount'] = \Helper::roundValue($si->localAmount) * -1;
                                            $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                            $data['documentRptCurrencyER'] = $si->rptAmount != 0 ? ($si->transAmount / $si->rptAmount) : 0;
                                            $data['documentRptAmount'] = \Helper::roundValue($si->rptAmount) * -1;
                                        }
                                        $data['timestamp'] = \Helper::currentDateTime();
                                        array_push($finalData, $data);
                                    } else {
                                        //convert amount in currency conversion
                                        $convertAmount = \Helper::convertAmountToLocalRpt(203, $masterModel["autoID"], $si->transAmount);

                                        $transAmountTotal = $si->transAmount;
                                        $localAmountTotal = $convertAmount["localAmount"];
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
                                        $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 5) :$masterData->bank->chartOfAccountSystemID;
                                        $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 5) : $masterData->bank->glCodeLinked;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                        $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;
                                        $data['documentTransAmount'] = \Helper::roundValue($si->transAmount + $retationVATAmount) * -1;
                                        $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                        $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                        $data['documentLocalAmount'] = ($convertAmount["localAmount"] + $retentionLocalVatAmount) * -1;
                                        $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                        $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                        $data['documentRptAmount'] = ($convertAmount["reportingAmount"] + $retentionRptVatAmount) * -1;
                                        $retationRcmVATAmount = TaxService::calculateRCMRetentionVatAmount($masterModel["autoID"]);

                                        if ($retationRcmVATAmount > 0) {
                                            $data['documentTransAmount'] = \Helper::roundValue($si->transAmount) * -1;
                                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                            $data['documentLocalAmount'] = ($convertAmount["localAmount"]) * -1;
                                            $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                            $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                            $data['documentRptAmount'] = ($convertAmount["reportingAmount"]) * -1;
                                        }
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

                                        $data['chartOfAccountSystemID'] = $company->exchangeGainLossGLCodeSystemID;
                                        $data['glCode'] = $company->exchangeGainLossGLCode;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                        $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;

                                        if ($diffTrans > 0 || $diffLocal > 0 || $diffRpt > 0) {
                                            $data['documentTransAmount'] = \Helper::roundValue(ABS($diffTrans));
                                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($diffLocal));
                                            $data['documentRptAmount'] = \Helper::roundValue(ABS($diffRpt));
                                        } else {
                                            $data['documentTransAmount'] = \Helper::roundValue(ABS($diffTrans)) * -1;
                                            $data['documentLocalAmount'] = \Helper::roundValue(ABS($diffLocal)) * -1;
                                            $data['documentRptAmount'] = \Helper::roundValue(ABS($diffRpt)) * -1;
                                        }

                                        $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                        $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                        $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                        $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
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

                            if ($masterData->invoiceType == 5) { //Advance Payment
                                if ($ap) {
                                    $data['serviceLineSystemID'] = 24;
                                    $data['serviceLineCode'] = 'X';
                                    $data['chartOfAccountSystemID'] = $masterData->supplierGLCodeSystemID;
                                    $data['glCode'] = $masterData->supplierGLCode;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $masterData->supplierTransCurrencyID;
                                    $data['documentTransCurrencyER'] = $masterData->supplierTransCurrencyER;
                                    $data['documentTransAmount'] = \Helper::roundValue($ap->transAmount);
                                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                    $data['documentLocalAmount'] = \Helper::roundValue($ap->localAmount);
                                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                    $data['documentRptAmount'] = \Helper::roundValue($ap->rptAmount);
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);

                                    $data['serviceLineSystemID'] = 24;
                                    $data['serviceLineCode'] = 'X';
                                    $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 5) :$masterData->bank->chartOfAccountSystemID;
                                    $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 5) : $masterData->bank->glCodeLinked;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                    $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;
                                    $data['documentTransAmount'] = \Helper::roundValue($ap->transAmount) * -1;
                                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                    $data['documentLocalAmount'] = \Helper::roundValue($ap->localAmount) * -1;
                                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;
                                    $data['documentRptAmount'] = \Helper::roundValue($ap->rptAmount) * -1;
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }


                            if ($masterData->invoiceType == 3) { //Direct Payment

                                $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER,payeeSystemCode")
                                    ->WHERE('documentSystemCode', $masterModel["autoID"])
                                    ->WHERE('documentSystemID', $masterModel["documentSystemID"])
                                    ->groupBy('documentSystemCode')
                                    ->first();

                                $isVATEligible = TaxService::checkCompanyVATEligible($masterData->companySystemID);

                                if ($isVATEligible == 1) {
                                    if($tax){
                                       if($masterData->rcmActivated == 0) {
                                        $masterLocal = $masterData->payAmountCompLocal;
                                        $masterRpt = $masterData->payAmountCompRpt;
                                        $data['serviceLineSystemID'] = 24;
                                        $data['serviceLineCode'] = 'X';
                                        $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 5) : $masterData->bank->chartOfAccountSystemID;
                                        $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 5) : $masterData->bank->glCodeLinked;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                        $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;
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
                                                $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                                $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;
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

                                        $convertedLocalAmount = 0;
                                        $convertedRpt = 0;
                                        $convertedTrans = 0;
                                        if ($dp) {
                                            foreach ($dp as $val) {
                                                if ($isBankCheck) {
                                                    //calculate local amount
                                                    if ($val->bankCurrencyID == $val->localCurrencyID) {
                                                        if($val->vatSubCategoryID == 3) {
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
                                                                if($val->vatSubCategoryID == 3) {
                                                                    $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountLocal) / $conversion->conversion);
                                                                }
                                                                else{

                                                                    $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                                                }
                                                            } else {
                                                                if($val->vatSubCategoryID == 3) {

                                                                    $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountLocal) * $conversion->conversion);
                                                                }
                                                                else{
                                                                    $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                                }
                                                            }
                                                            $convertedLocalAmount += $data['documentLocalAmount'];
                                                        } else {
                                                            if ($conversion->conversion > 1) {
                                                                if($val->vatSubCategoryID == 3) {
                                                                    $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountLocal) * $conversion->conversion);
                                                                }
                                                                else{
                                                                    $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                                }
                                                            } else {
                                                                if($val->vatSubCategoryID == 3) {
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
                                                        if($val->vatSubCategoryID == 3) {
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
                                                                if($val->vatSubCategoryID == 3) {
                                                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountRpt) / $conversion->conversion);
                                                                }
                                                                else{
                                                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                                                }
                                                            } else {
                                                                if($val->vatSubCategoryID == 3) {
                                                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountRpt) * $conversion->conversion);
                                                                }
                                                                else{
                                                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                                }
                                                            }
                                                            $convertedRpt += $data['documentRptAmount'];
                                                        } else {
                                                            if ($conversion->conversion > 1) {
                                                                if($val->vatSubCategoryID == 3) {
                                                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountRpt) * $conversion->conversion);
                                                                }
                                                                else{
                                                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                                }
                                                            } else {
                                                                if($val->vatSubCategoryID == 3) {
                                                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountRpt) / $conversion->conversion);
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
                                                    if($val->vatSubCategoryID == 3) {
                                                        $data['documentLocalAmount'] = \Helper::roundValue($val->localAmount + $val->VATAmountLocal);
                                                    }
                                                    else{
                                                        $data['documentLocalAmount'] = \Helper::roundValue($val->localAmount);
                                                    }
                                                    $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                                                    if($val->vatSubCategoryID == 3) {
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
                                                if($val->vatSubCategoryID == 3) {
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
                                        $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 5) : $masterData->bank->chartOfAccountSystemID;
                                        $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 5) : $masterData->bank->glCodeLinked;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                        $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;
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
                                                $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                                $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;
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
                                                $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                                $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;
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
                                                if ($isBankCheck) {
                                                    //calculate local amount
                                                    if ($val->bankCurrencyID == $val->localCurrencyID) {
                                                        if($val->vatSubCategoryID == 3) {
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
                                                                if($val->vatSubCategoryID == 3) {
                                                                    $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountLocal) / $conversion->conversion);
                                                                }
                                                                else{

                                                                    $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                                                }
                                                            } else {
                                                                if($val->vatSubCategoryID == 3) {

                                                                    $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountLocal) * $conversion->conversion);
                                                                }
                                                                else{
                                                                    $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                                }
                                                            }
                                                            $convertedLocalAmount += $data['documentLocalAmount'];
                                                        } else {
                                                            if ($conversion->conversion > 1) {
                                                                if($val->vatSubCategoryID == 3) {
                                                                    $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountLocal) * $conversion->conversion);
                                                                }
                                                                else{
                                                                    $data['documentLocalAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                                }
                                                            } else {
                                                                if($val->vatSubCategoryID == 3) {
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
                                                        if($val->vatSubCategoryID == 3) {
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
                                                                if($val->vatSubCategoryID == 3) {
                                                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountRpt) / $conversion->conversion);
                                                                }
                                                                else{
                                                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) / $conversion->conversion);
                                                                }
                                                            } else {
                                                                if($val->vatSubCategoryID == 3) {
                                                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountRpt) * $conversion->conversion);
                                                                }
                                                                else{
                                                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                                }
                                                            }
                                                            $convertedRpt += $data['documentRptAmount'];
                                                        } else {
                                                            if ($conversion->conversion > 1) {
                                                                if($val->vatSubCategoryID == 3) {
                                                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountRpt) * $conversion->conversion);
                                                                }
                                                                else{
                                                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount) * $conversion->conversion);
                                                                }
                                                            } else {
                                                                if($val->vatSubCategoryID == 3) {
                                                                    $data['documentRptAmount'] = \Helper::roundValue(($val->bankAmount + $val->VATAmountRpt) / $conversion->conversion);
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
                                                    if($val->vatSubCategoryID == 3) {
                                                        $data['documentLocalAmount'] = \Helper::roundValue($val->localAmount + $val->VATAmountLocal);
                                                    }
                                                    else{
                                                        $data['documentLocalAmount'] = \Helper::roundValue($val->localAmount);
                                                    }
                                                    $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                                                    if($val->vatSubCategoryID == 3) {
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
                                                if($val->vatSubCategoryID == 3) {
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
                                        $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 5) : $masterData->bank->chartOfAccountSystemID;
                                        $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 5) : $masterData->bank->glCodeLinked;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                        $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;
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
                                }
                                else{
                                    $masterLocal = $masterData->payAmountCompLocal;
                                    $masterRpt = $masterData->payAmountCompRpt;
                                    $data['serviceLineSystemID'] = 24;
                                    $data['serviceLineCode'] = 'X';
                                    $data['chartOfAccountSystemID'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, 5) : $masterData->bank->chartOfAccountSystemID;
                                    $data['glCode'] = ($masterData->pdcChequeYN) ? SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, 5) : $masterData->bank->glCodeLinked;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                    $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;
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


                            if($exemptVatTotal) {
                                $diffTrans = $convertedTrans - $dpTotal->transAmount - $exemptVatTotal->vatAmount;
                                $diffLocal = $convertedLocalAmount - $masterLocal - $exemptVatTotal->VATAmountLocal;
                                $diffRpt = $convertedRpt - $masterRpt - $exemptVatTotal->VATAmountRpt;
                            }
                            else{
                                $diffTrans = $convertedTrans - $dpTotal->transAmount;
                                $diffLocal = $convertedLocalAmount - $masterLocal;
                                $diffRpt = $convertedRpt - $masterRpt;
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

                                    $data['chartOfAccountSystemID'] = $company->exchangeGainLossGLCodeSystemID;
                                    $data['glCode'] = $company->exchangeGainLossGLCode;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentTransCurrencyID'] = $masterData->BPVbankCurrency;
                                    $data['documentTransCurrencyER'] = $masterData->BPVbankCurrencyER;

                                    if ($diffTrans > 0 || $diffLocal > 0 || $diffRpt > 0) {
                                        $data['documentTransAmount'] = \Helper::roundValue(ABS($diffTrans)) * -1;
                                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($diffLocal)) * -1;
                                        $data['documentRptAmount'] = \Helper::roundValue(ABS($diffRpt)) * -1;
                                    } else {
                                        $data['documentTransAmount'] = \Helper::roundValue(ABS($diffTrans));
                                        $data['documentLocalAmount'] = \Helper::roundValue(ABS($diffLocal));
                                        $data['documentRptAmount'] = \Helper::roundValue(ABS($diffRpt));
                                    }

                                    $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                                    $data['documentRptCurrencyID'] = $masterData->companyRptCurrencyID;
                                    $data['documentRptCurrencyER'] = $masterData->companyRptCurrencyER;

                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
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
                                        Log::info('Receipt voucher VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Output Vat GL Account not assigned to company' . date('H:i:s'));
                                    }
                                } else {
                                    Log::info('Receipt voucher VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                    Log::info('Output Vat GL Account not configured' . date('H:i:s'));
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
                                            Log::info('Receipt voucher VAT GL Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                            Log::info('Output Vat transfer GL Account not assigned to company' . date('H:i:s'));
                                        }
                                    } else {
                                        Log::info('Receipt voucher VAT GL Entry IssuesId :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                                        Log::info('Output VAT transfer GL Account not configured' . date('H:i:s'));
                                    }

                                    $data['documentTransAmount'] = \Helper::roundValue(ABS($taxTrans));
                                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($taxLocal));
                                    $data['documentRptAmount'] = \Helper::roundValue(ABS($taxRpt));
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        break;
                    case 17: // JV - Journal Voucher
                        $masterData = JvMaster::with(['detail' => function ($query) {
                            $query->selectRaw('SUM(debitAmount) as debitAmountTot, SUM(creditAmount) as creditAmountTot,jvMasterAutoId');
                        }], 'financeperiod_by', 'company')->find($masterModel["autoID"]);

                        $detailRecords = JvDetail::selectRaw("sum(debitAmount) as debitAmountTot, sum(creditAmount) as creditAmountTot, contractUID, clientContractID, comments, chartOfAccountSystemID, serviceLineSystemID,serviceLineCode,currencyID,currencyER")->WHERE('jvMasterAutoId', $masterModel["autoID"])->groupBy('chartOfAccountSystemID', 'serviceLineSystemID', 'comments', 'contractUID')->get();

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if ($masterData->financeperiod_by->isActive == -1) {
                            $masterDocumentDate = $masterData->JVdate;
                        }

                        $time = Carbon::now();

                        if (!empty($detailRecords)) {
                            foreach ($detailRecords as $item) {
                                $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $item->chartOfAccountSystemID)->first();

                                $data['companySystemID'] = $masterData->companySystemID;
                                $data['companyID'] = $masterData->companyID;
                                $data['serviceLineSystemID'] = $item->serviceLineSystemID;
                                $data['serviceLineCode'] = $item->serviceLineCode;
                                $data['masterCompanyID'] = $masterData->companyID;
                                $data['documentSystemID'] = $masterData->documentSystemID;
                                $data['documentID'] = $masterData->documentID;
                                $data['documentSystemCode'] = $masterData->jvMasterAutoId;
                                $data['documentCode'] = $masterData->JVcode;
                                $data['documentDate'] = $masterDocumentDate;
                                $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                                $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);
                                //$data['invoiceNumber'] = ;
                                //$data['invoiceDate'] = ;

                                // from customer invoice master table
                                $data['chartOfAccountSystemID'] = $item->chartOfAccountSystemID;
                                $data['glCode'] = $chartOfAccount->AccountCode;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentConfirmedDate'] = $masterData->confirmedDate;
                                $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                                $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                                $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                                $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                                $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                                $data['documentNarration'] = $item->comments;

                                if ($item->clientContractID && $item->contractUID) {
                                    $data['clientContractID'] = $item->clientContractID;
                                    $data['contractUID'] = $item->contractUID;
                                } else {
                                    $data['clientContractID'] = 'X';
                                    $data['contractUID'] = 159;
                                }

                                $data['documentTransCurrencyID'] = $item->currencyID;
                                $data['documentTransCurrencyER'] = $item->currencyER;

                                $data['createdUserSystemID'] = $empID->empID;
                                $data['createdDateTime'] = $time;
                                $data['createdUserID'] = $empID->employeeSystemID;
                                $data['createdUserPC'] = getenv('COMPUTERNAME');


                                if ($item->debitAmountTot > 0) {
                                    $currencyConvertionDebit = \Helper::currencyConversion($masterData->companySystemID, $item->currencyID, $item->currencyID, $item->debitAmountTot);

                                    $data['documentTransAmount'] = $item->debitAmountTot;
                                    $data['documentLocalCurrencyID'] = $masterData->company->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = \Helper::roundValue($currencyConvertionDebit['trasToLocER']);
                                    $data['documentLocalAmount'] = \Helper::roundValue($currencyConvertionDebit['localAmount']);
                                    $data['documentRptCurrencyID'] = $masterData->company->reportingCurrency;
                                    $data['documentRptCurrencyER'] = \Helper::roundValue($currencyConvertionDebit['trasToRptER']);
                                    $data['documentRptAmount'] = \Helper::roundValue($currencyConvertionDebit['reportingAmount']);
                                    array_push($finalData, $data);
                                }
                                if ($item->creditAmountTot > 0) {
                                    $currencyConvertionCredit = \Helper::currencyConversion($masterData->companySystemID, $item->currencyID, $item->currencyID, $item->creditAmountTot);

                                    $data['documentTransAmount'] = $item->creditAmountTot * -1;
                                    $data['documentLocalCurrencyID'] = $masterData->company->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = \Helper::roundValue($currencyConvertionCredit['trasToLocER']);
                                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($currencyConvertionCredit['localAmount'])) * -1;
                                    $data['documentRptCurrencyID'] = $masterData->company->reportingCurrency;
                                    $data['documentRptCurrencyER'] = \Helper::roundValue(ABS($currencyConvertionCredit['trasToRptER']));
                                    $data['documentRptAmount'] = \Helper::roundValue(ABS($currencyConvertionCredit['reportingAmount'])) * -1;
                                    array_push($finalData, $data);
                                }

                            }
                        }
                        break;
                    case 22: // FA - Fixed Asset Master
                        $masterData = FixedAssetMaster::with(['grvdetail_by', 'posttogl_by'])->find($masterModel["autoID"]);
                        $companyCurrency = Company::find($masterModel["companySystemID"]);

                        if ($masterData) {
                            if ($masterData->assetType == 1) {
                                $data['companySystemID'] = $masterData->companySystemID;
                                $data['companyID'] = $masterData->companyID;
                                $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                                $data['serviceLineCode'] = $masterData->serviceLineCode;
                                $data['masterCompanyID'] = null;
                                $data['documentSystemID'] = $masterData->documentSystemID;
                                $data['documentID'] = $masterData->documentID;
                                $data['documentSystemCode'] = $masterModel["autoID"];
                                $data['documentCode'] = $masterData->faCode;
                                $data['documentDate'] = ($masterData->documentDate) ? $masterData->documentDate : date('Y-m-d H:i:s');
                                $data['documentYear'] = \Helper::dateYear(date('Y-m-d H:i:s'));
                                $data['documentMonth'] = \Helper::dateMonth(date('Y-m-d H:i:s'));
                                $data['documentConfirmedDate'] = $masterData->confirmedDate;
                                $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                                $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                                $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                                $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                                $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                                $data['documentNarration'] = $masterData->COMMENTS;
                                $data['clientContractID'] = 'X';
                                $data['contractUID'] = 159;
                                $data['supplierCodeSystem'] = 0;
                                $data['chartOfAccountSystemID'] = $masterData->costglCodeSystemID;
                                $data['glCode'] = $masterData->COSTGLCODE;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentLocalCurrencyID'] = $companyCurrency->localCurrencyID;
                                $data['documentLocalCurrencyER'] = 0;
                                $data['documentLocalAmount'] = ABS($masterData->COSTUNIT);
                                $data['documentRptCurrencyID'] = $companyCurrency->reportingCurrency;
                                $data['documentRptCurrencyER'] = 0;
                                $data['documentRptAmount'] = ABS($masterData->costUnitRpt);
                                $data['documentTransCurrencyID'] = 0;
                                $data['documentTransCurrencyER'] = 0;
                                $data['documentTransAmount'] = 0;
                                $data['holdingShareholder'] = null;
                                $data['holdingPercentage'] = 0;
                                $data['nonHoldingPercentage'] = 0;
                                $data['contraYN'] = 0;
                                $data['createdDateTime'] = \Helper::currentDateTime();
                                $data['createdUserID'] = $empID->empID;
                                $data['createdUserSystemID'] = $empID->employeeSystemID;
                                $data['createdUserPC'] = gethostname();
                                $data['timestamp'] = \Helper::currentDateTime();
                                array_push($finalData, $data);

                                //if the asset from asset capitalization pass a gl entry
                                if ($masterData->docOriginSystemCode) {
                                    if ($masterData->docOriginDocumentSystemID == 63) {
                                        $assetCapitalization = AssetCapitalization::find($masterData->docOriginSystemCode);
                                        if ($assetCapitalization) {
                                            $data['chartOfAccountSystemID'] = $assetCapitalization->contraAccountSystemID;
                                            $data['glCode'] = $assetCapitalization->contraAccountGLCode;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                            $data['documentLocalCurrencyID'] = $companyCurrency->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = 0;
                                            $data['documentLocalAmount'] = ABS($masterData->COSTUNIT) * -1;
                                            $data['documentRptCurrencyID'] = $companyCurrency->reportingCurrency;
                                            $data['documentRptCurrencyER'] = 0;
                                            $data['documentRptAmount'] = ABS($masterData->costUnitRpt) * -1;
                                            $data['contraYN'] = -1;
                                            $data['timestamp'] = \Helper::currentDateTime();
                                            array_push($finalData, $data);
                                        }
                                    } else {
                                        if ($masterData->grvdetail_by) {
                                            $data['chartOfAccountSystemID'] = $masterData->grvdetail_by->financeGLcodebBSSystemID;
                                            $data['glCode'] = $masterData->grvdetail_by->financeGLcodebBS;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                            $data['documentLocalCurrencyID'] = $companyCurrency->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = 0;
                                            $data['documentLocalAmount'] = ABS($masterData->COSTUNIT) * -1;
                                            $data['documentRptCurrencyID'] = $companyCurrency->reportingCurrency;
                                            $data['documentRptCurrencyER'] = 0;
                                            $data['documentRptAmount'] = ABS($masterData->costUnitRpt) * -1;
                                            $data['contraYN'] = 0;
                                            $data['timestamp'] = \Helper::currentDateTime();
                                            array_push($finalData, $data);
                                        }
                                    }
                                } else {
                                    if ($masterData->postToGLYN) {
                                        $data['chartOfAccountSystemID'] = $masterData->postToGLCodeSystemID;
                                        $data['glCode'] = $masterData->postToGLCode;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentLocalCurrencyID'] = $companyCurrency->localCurrencyID;
                                        $data['documentLocalCurrencyER'] = 0;
                                        $data['documentLocalAmount'] = ABS($masterData->COSTUNIT) * -1;
                                        $data['documentRptCurrencyID'] = $companyCurrency->reportingCurrency;
                                        $data['documentRptCurrencyER'] = 0;
                                        $data['documentRptAmount'] = ABS($masterData->costUnitRpt) * -1;
                                        $data['timestamp'] = \Helper::currentDateTime();
                                        $data['contraYN'] = 0;
                                        array_push($finalData, $data);
                                    } else {
                                        if ($masterData->grvdetail_by) {
                                            $data['chartOfAccountSystemID'] = $masterData->grvdetail_by->financeGLcodebBSSystemID;
                                            $data['glCode'] = $masterData->grvdetail_by->financeGLcodebBS;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                            $data['documentLocalCurrencyID'] = $companyCurrency->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = 0;
                                            $data['documentLocalAmount'] = ABS($masterData->COSTUNIT) * -1;
                                            $data['documentRptCurrencyID'] = $companyCurrency->reportingCurrency;
                                            $data['documentRptCurrencyER'] = 0;
                                            $data['documentRptAmount'] = ABS($masterData->costUnitRpt) * -1;
                                            $data['timestamp'] = \Helper::currentDateTime();
                                            $data['contraYN'] = 0;
                                            array_push($finalData, $data);
                                        }
                                    }
                                }
                            } else {
                                $documentUpdateData = FixedAssetMaster::find($masterModel["autoID"]);
                                $documentUpdateData->postedDate = date('Y-m-d H:i:s');
                                $documentUpdateData->save();
                                DB::commit();
                            }
                        }
                        break;
                    case 23: // FAD - Fixed Asset Depreciation
                        $masterData = FixedAssetDepreciationMaster::find($masterModel["autoID"]);

                        $debit = DB::table('erp_fa_assetdepreciationperiods')
                            ->selectRaw('erp_fa_assetdepreciationperiods.*,erp_fa_asset_master.depglCodeSystemID,erp_fa_asset_master.DEPGLCODE,
                                        SUM(depAmountLocal) as sumDepAmountLocal, SUM(depAmountRpt) as sumDepAmountRpt,catogaryBLorPL,catogaryBLorPLID')
                            ->join('erp_fa_asset_master', 'erp_fa_asset_master.faID', 'erp_fa_assetdepreciationperiods.faID')
                            ->join('chartofaccounts', 'chartOfAccountSystemID', 'depglCodeSystemID')
                            ->where('depMasterAutoID', $masterModel["autoID"])
                            ->groupBy('erp_fa_assetdepreciationperiods.serviceLineSystemID', 'erp_fa_asset_master.depglCodeSystemID')
                            ->get();

                        $credit = DB::table('erp_fa_assetdepreciationperiods')
                            ->selectRaw('erp_fa_assetdepreciationperiods.*,erp_fa_asset_master.accdepglCodeSystemID,erp_fa_asset_master.ACCDEPGLCODE,
                                        SUM(depAmountLocal) as sumDepAmountLocal, SUM(depAmountRpt) as sumDepAmountRpt,catogaryBLorPL,catogaryBLorPLID')
                            ->join('erp_fa_asset_master', 'erp_fa_asset_master.faID', 'erp_fa_assetdepreciationperiods.faID')
                            ->join('chartofaccounts', 'chartOfAccountSystemID', 'accdepglCodeSystemID')
                            ->where('depMasterAutoID', $masterModel["autoID"])
                            ->groupBy('erp_fa_assetdepreciationperiods.serviceLineSystemID', 'erp_fa_asset_master.accdepglCodeSystemID')
                            ->get();

                        if(!$masterData->is_acc_dep)
                        {
                            if ($debit) {
                                foreach ($debit as $val) {
                                    $data['companySystemID'] = $val->companySystemID;
                                    $data['companyID'] = $val->companyID;
                                    $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                    $data['serviceLineCode'] = $val->serviceLineCode;
                                    $data['masterCompanyID'] = null;
                                    $data['documentSystemID'] = $masterData->documentSystemID;
                                    $data['documentID'] = $masterData->documentID;
                                    $data['documentSystemCode'] = $masterModel["autoID"];
                                    $data['documentCode'] = $masterData->depCode;
                                    $data['documentDate'] = $masterData->depDate;
                                    $data['documentYear'] = \Helper::dateYear($masterData->depDate);
                                    $data['documentMonth'] = \Helper::dateMonth($masterData->depDate);
                                    $data['documentConfirmedDate'] = $masterData->confirmedDate;
                                    $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                                    $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                                    $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                                    $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                                    $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                                    $data['documentNarration'] = null;
                                    $data['clientContractID'] = 'X';
                                    $data['contractUID'] = 159;
                                    $data['supplierCodeSystem'] = 0;
                                    $data['chartOfAccountSystemID'] = $val->depglCodeSystemID;
                                    $data['glCode'] = $val->DEPGLCODE;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentLocalCurrencyID'] = $val->depAmountLocalCurr;
                                    $data['documentLocalCurrencyER'] = 0;
                                    $data['documentLocalAmount'] = ABS($val->sumDepAmountLocal);
                                    $data['documentRptCurrencyID'] = $val->depAmountRptCurr;
                                    $data['documentRptCurrencyER'] = 0;
                                    $data['documentRptAmount'] = ABS($val->sumDepAmountRpt);
                                    $data['documentTransCurrencyID'] = 0;
                                    $data['documentTransCurrencyER'] = 0;
                                    $data['documentTransAmount'] = 0;
                                    $data['holdingShareholder'] = null;
                                    $data['holdingPercentage'] = 0;
                                    $data['nonHoldingPercentage'] = 0;
                                    $data['createdDateTime'] = \Helper::currentDateTime();
                                    $data['createdUserID'] = $empID->empID;
                                    $data['createdUserSystemID'] = $empID->employeeSystemID;
                                    $data['createdUserPC'] = gethostname();
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
    
                                if ($credit) {
                                    foreach ($credit as $val) {
                                        $data['companySystemID'] = $val->companySystemID;
                                        $data['companyID'] = $val->companyID;
                                        $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                        $data['serviceLineCode'] = $val->serviceLineCode;
                                        $data['masterCompanyID'] = null;
                                        $data['documentSystemID'] = $masterData->documentSystemID;
                                        $data['documentID'] = $masterData->documentID;
                                        $data['documentSystemCode'] = $masterModel["autoID"];
                                        $data['documentCode'] = $masterData->depCode;
                                        $data['documentDate'] = $masterData->depDate;
                                        $data['documentYear'] = \Helper::dateYear($masterData->depDate);
                                        $data['documentMonth'] = \Helper::dateMonth($masterData->depDate);
                                        $data['documentConfirmedDate'] = $masterData->confirmedDate;
                                        $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                                        $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                                        $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                                        $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                                        $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                                        $data['documentNarration'] = null;
                                        $data['clientContractID'] = 'X';
                                        $data['contractUID'] = 159;
                                        $data['supplierCodeSystem'] = 0;
                                        $data['chartOfAccountSystemID'] = $val->accdepglCodeSystemID;
                                        $data['glCode'] = $val->ACCDEPGLCODE;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentLocalCurrencyID'] = $val->depAmountLocalCurr;
                                        $data['documentLocalCurrencyER'] = 0;
                                        $data['documentLocalAmount'] = ABS($val->sumDepAmountLocal) * -1;
                                        $data['documentRptCurrencyID'] = $val->depAmountRptCurr;
                                        $data['documentRptCurrencyER'] = 0;
                                        $data['documentRptAmount'] = ABS($val->sumDepAmountRpt) * -1;
                                        $data['documentTransCurrencyID'] = 0;
                                        $data['documentTransCurrencyER'] = 0;
                                        $data['documentTransAmount'] = 0;
                                        $data['holdingShareholder'] = null;
                                        $data['holdingPercentage'] = 0;
                                        $data['nonHoldingPercentage'] = 0;
                                        $data['createdDateTime'] = \Helper::currentDateTime();
                                        $data['createdUserID'] = $empID->empID;
                                        $data['createdUserSystemID'] = $empID->employeeSystemID;
                                        $data['createdUserPC'] = gethostname();
                                        $data['timestamp'] = \Helper::currentDateTime();
                                        array_push($finalData, $data);
                                    }
                                }
                            }
                        }  
                        else if($masterData->is_acc_dep)
                        {

                            $accumulate_Dep = DB::table('erp_fa_assetdepreciationperiods')
                            ->selectRaw('erp_fa_assetdepreciationperiods.*,erp_fa_asset_master.*,
                                        SUM(depAmountLocal) as sumDepAmountLocal, SUM(depAmountRpt) as sumDepAmountRpt,catogaryBLorPL,catogaryBLorPLID')
                            ->join('erp_fa_asset_master', 'erp_fa_asset_master.faID', 'erp_fa_assetdepreciationperiods.faID')
                            ->join('chartofaccounts', 'chartOfAccountSystemID', 'depglCodeSystemID')
                            ->where('depMasterAutoID', $masterModel["autoID"])
                            ->groupBy('erp_fa_assetdepreciationperiods.serviceLineSystemID', 'erp_fa_asset_master.depglCodeSystemID')
                            ->first();
                            $gl_data = array();
                            $gl_data['companySystemID'] = $masterData->companySystemID;
                            $gl_data['companyID'] = $masterData->companyID;
                            $gl_data['serviceLineSystemID'] = $accumulate_Dep->serviceLineSystemID;
                            $gl_data['serviceLineCode'] = $accumulate_Dep->serviceLineCode;
                            $gl_data['masterCompanyID'] = null;
                            $gl_data['documentSystemID'] = $masterData->documentSystemID;
                            $gl_data['documentID'] = $masterData->documentID;
                            $gl_data['documentSystemCode'] = $masterData->depMasterAutoID;
                            $gl_data['documentCode'] = $masterData->depCode;
                            $gl_data['documentDate'] = $masterData->depDate;
                            $gl_data['documentYear'] = \Helper::dateYear($masterData->depDate);
                            $gl_data['documentMonth'] = \Helper::dateMonth($masterData->depDate);

                   

                            $gl_data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $gl_data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $gl_data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;

                            $gl_data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $gl_data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $gl_data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

                            $gl_data['documentNarration'] = null;
                            $gl_data['clientContractID'] = 'X';
                            $gl_data['contractUID'] = 159;
                            $gl_data['supplierCodeSystem'] = 0;


                            
                            $gl_data['documentTransCurrencyID'] = 0;
                            $gl_data['documentTransCurrencyER'] = 0;
                            $gl_data['documentTransAmount'] = 0;
                            $gl_data['documentLocalCurrencyID'] = $masterData->depLocalCur;
                            $gl_data['documentLocalCurrencyER'] = 0;
                           
                            $gl_data['documentRptCurrencyID'] = $masterData->depRptCur;
                            $gl_data['documentRptCurrencyER'] = 0;
                          
                            $gl_data['holdingShareholder'] = null;
                            $gl_data['holdingPercentage'] = 0;
                            $gl_data['nonHoldingPercentage'] = 0;
                            $gl_data['createdDateTime'] = \Helper::currentDateTime();
                            $gl_data['createdUserID'] = 8888;
                            $gl_data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                            $gl_data['createdUserPC'] = gethostname();
                            $gl_data['timestamp'] = \Helper::currentDateTime();

                            if($accumulate_Dep->postToGLYN)
                            {
                                $finalData1 = [1,2];
                                foreach ($finalData1 as $da) {
                                    
                                    if($da == 1)
                                    {
                                        $gl_data['chartOfAccountSystemID'] = $accumulate_Dep->postToGLCodeSystemID;
                                        $gl_data['glCode'] = $accumulate_Dep->postToGLCode;
                                        $gl_data['glAccountType'] = ChartOfAccount::getGlAccountType($gl_data['chartOfAccountSystemID']);
                                        $gl_data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($gl_data['chartOfAccountSystemID']);
        
                                        $gl_data['documentLocalAmount'] = $accumulate_Dep->accumulated_depreciation_amount_lcl;
                                        $gl_data['documentRptAmount'] = $accumulate_Dep->accumulated_depreciation_amount_rpt;
                                    }
                                    else if($da == 2)
                                    {
                                        $gl_data['chartOfAccountSystemID'] = $accumulate_Dep->accdepglCodeSystemID;
                                        $gl_data['glCode'] = $accumulate_Dep->ACCDEPGLCODE;
                                        $gl_data['glAccountType'] = ChartOfAccount::getGlAccountType($gl_data['chartOfAccountSystemID']);
                                        $gl_data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($gl_data['chartOfAccountSystemID']);
        
                                        $gl_data['documentLocalAmount'] = $accumulate_Dep->accumulated_depreciation_amount_lcl*-1;
                                        $gl_data['documentRptAmount'] = $accumulate_Dep->accumulated_depreciation_amount_rpt*-1;
                                    }
                                    
                                    array_push($finalData, $gl_data);
                                   
                                }
                            }
                        
                        }  
                      
                        break;
                    case 41: // FADS - Fixed Asset Disposal
                        $masterData = AssetDisposalMaster::with(['disposal_type' => function ($query) {
                            $query->with('chartofaccount');
                        }])->find($masterModel["autoID"]);

                        $disposal = AssetDisposalDetail::with('disposal_account')->selectRaw('SUM(netBookValueLocal) as netBookValueLocal, SUM(netBookValueRpt) as netBookValueRpt,DISPOGLCODESystemID,DISPOGLCODE,serviceLineSystemID,serviceLineCode')->OfMaster($masterModel["autoID"])->groupBy('DISPOGLCODESystemID', 'serviceLineSystemID')->get();

                        $depreciation = AssetDisposalDetail::with('accumilated_account')->selectRaw('SUM(depAmountLocal) as depAmountLocal, SUM(depAmountRpt) as depAmountRpt,ACCDEPGLCODESystemID,ACCDEPGLCODE,serviceLineSystemID,serviceLineCode')->OfMaster($masterModel["autoID"])->groupBy('ACCDEPGLCODESystemID', 'serviceLineSystemID')->get();

                        $cost = AssetDisposalDetail::with(['cost_account'])->selectRaw('SUM(COSTUNIT) as COSTUNIT, SUM(costUnitRpt) as costUnitRpt,COSTGLCODESystemID,serviceLineSystemID,COSTGLCODE,serviceLineCode')->OfMaster($masterModel["autoID"])->groupBy('COSTGLCODESystemID', 'serviceLineSystemID')->get();
                        $companyCurrency = Company::find($masterModel["companySystemID"]);

                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->disposalDocumentCode;
                            $data['documentDate'] = $masterData->disposalDocumentDate;
                            $data['documentYear'] = \Helper::dateYear($masterData->disposalDocumentDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterData->disposalDocumentDate);
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confimedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confimedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->narration;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = 0;
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['contraYN'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            $depRptAmountTotal = 0;
                            $depLocalAmountTotal = 0;

                            $costRptAmountTotal = 0;
                            $costLocalAmountTotal = 0;

                            if ($depreciation) {
                                foreach ($depreciation as $val) {
                                    $depRptAmountTotal += ABS($val->depAmountRpt);
                                    $depLocalAmountTotal += ABS($val->depAmountLocal);

                                    $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                    $data['serviceLineCode'] = $val->serviceLineCode;
                                    $data['chartOfAccountSystemID'] = $val->ACCDEPGLCODESystemID;
                                    $data['glCode'] = $val->ACCDEPGLCODE;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentLocalCurrencyID'] = $companyCurrency->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = 0;
                                    $data['documentLocalAmount'] = ABS($val->depAmountLocal);
                                    $data['documentRptCurrencyID'] = $companyCurrency->reportingCurrency;
                                    $data['documentRptCurrencyER'] = 0;
                                    $data['documentRptAmount'] = ABS($val->depAmountRpt);
                                    $data['documentTransCurrencyID'] = 0;
                                    $data['documentTransCurrencyER'] = 0;
                                    $data['documentTransAmount'] = 0;
                                    $data['contraYN'] = 0;
                                    array_push($finalData, $data);
                                }
                            }

                            if ($cost) {
                                foreach ($cost as $val) {
                                    $costRptAmountTotal = ABS($val->costUnitRpt);
                                    $costLocalAmountTotal = ABS($val->COSTUNIT);
                                    $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                    $data['serviceLineCode'] = $val->serviceLineCode;
                                    $data['chartOfAccountSystemID'] = $val->COSTGLCODESystemID;
                                    $data['glCode'] = $val->COSTGLCODE;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentLocalCurrencyID'] = $companyCurrency->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = 0;
                                    $data['documentLocalAmount'] = ABS($val->COSTUNIT) * -1;
                                    $data['documentRptCurrencyID'] = $companyCurrency->reportingCurrency;
                                    $data['documentRptCurrencyER'] = 0;
                                    $data['documentRptAmount'] = ABS($val->costUnitRpt) * -1;
                                    $data['documentTransCurrencyID'] = 0;
                                    $data['documentTransCurrencyER'] = 0;
                                    $data['documentTransAmount'] = 0;
                                    $data['contraYN'] = 0;
                                    array_push($finalData, $data);
                                }
                            }

                            //if the asset disposal type is 8 pass an entry to asset capitalization contra account
                            if ($masterData->disposalType == 8) {
                                $diffRpt = $costRptAmountTotal - $depRptAmountTotal;
                                $diffLocal = $costLocalAmountTotal - $depLocalAmountTotal;
                                if ($diffRpt != 0 || $diffLocal != 0) {
                                    $disposalDetail = AssetDisposalDetail::ofMaster($masterModel["autoID"])->first();
                                    if ($disposalDetail) {
                                        $assetCapitalizationMaster = AssetCapitalization::with('contra_account')->where('faID', $disposalDetail->faID)->first();
                                        if ($assetCapitalizationMaster) {
                                            $data['serviceLineSystemID'] = $disposalDetail->serviceLineSystemID;
                                            $data['serviceLineCode'] = $disposalDetail->serviceLineCode;
                                            $data['chartOfAccountSystemID'] = $assetCapitalizationMaster->contraAccountSystemID;
                                            $data['glCode'] = $assetCapitalizationMaster->contraAccountGLCode;
                                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                            $data['documentLocalCurrencyID'] = $companyCurrency->localCurrencyID;
                                            $data['documentLocalCurrencyER'] = 0;
                                            $data['documentLocalAmount'] = $diffLocal;
                                            $data['documentRptCurrencyID'] = $companyCurrency->reportingCurrency;
                                            $data['documentRptCurrencyER'] = 0;
                                            $data['documentRptAmount'] = $diffRpt;
                                            $data['documentTransCurrencyID'] = 0;
                                            $data['documentTransCurrencyER'] = 0;
                                            $data['documentTransAmount'] = 0;
                                            $data['contraYN'] = -1;
                                            array_push($finalData, $data);
                                        }
                                    }
                                }
                            } else {
                                if ($disposal) {
                                    foreach ($disposal as $val) {
                                        $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                        $data['serviceLineCode'] = $val->serviceLineCode;
                                        $data['chartOfAccountSystemID'] = $masterData->disposal_type->chartOfAccountID;
                                        $data['glCode'] = $masterData->disposal_type->glCode;
                                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                        $data['documentLocalCurrencyID'] = $companyCurrency->localCurrencyID;
                                        $data['documentLocalCurrencyER'] = 0;
                                        $data['documentRptCurrencyID'] = $companyCurrency->reportingCurrency;
                                        $data['documentRptCurrencyER'] = 0;
                                        $data['documentTransCurrencyID'] = 0;
                                        $data['documentTransCurrencyER'] = 0;
                                        $data['documentTransAmount'] = 0;
                                        $data['contraYN'] = 0;
                                        if ($val->netBookValueLocal > 0) {
                                            $data['documentLocalAmount'] = ABS($val->netBookValueLocal);
                                            $data['documentRptAmount'] = ABS($val->netBookValueRpt);
                                        } else {
                                            $data['documentLocalAmount'] = $val->netBookValueLocal;
                                            $data['documentRptAmount'] = $val->netBookValueRpt;
                                        }
                                        array_push($finalData, $data);
                                    }
                                }
                            }
                        }
                        break;
                    case 71:
                        /*Delivery Order*/
                        $masterData = DeliveryOrder::with(['finance_period_by'])->find($masterModel["autoID"]);
                        $company = Company::select('masterComapanyID')->where('companySystemID', $masterData->companySystemID)->first();

                        $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->custUnbilledAccountSystemID)->first();
                        $masterDocumentDate = Carbon::now();
                        $time = Carbon::now();

                        $data['companySystemID'] = $masterData->companySystemID;
                        $data['companyID'] = $masterData->companyID;
                        $data['masterCompanyID'] = $company->masterComapanyID;
                        $data['documentID'] = "DEO";
                        $data['documentSystemID'] = $masterData->documentSystemID;
                        $data['documentSystemCode'] = $masterData->deliveryOrderID;
                        $data['documentCode'] = $masterData->deliveryOrderCode;
                        $data['documentDate'] = $masterDocumentDate;
                        $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                        $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);

                        $data['documentConfirmedDate'] = $masterData->confirmedDate;
                        $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                        $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                        $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                        $data['documentFinalApprovedBy'] = $masterData->approvedbyEmpID;
                        $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedEmpSystemID;

                        $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                        $data['serviceLineCode'] = $masterData->serviceLineCode;

                        // from customer invoice master table
                        $data['chartOfAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
                        $data['glCode'] = $chartOfAccount->AccountCode;
                        $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                        $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                        $data['documentNarration'] = $masterData->narration;
                        $data['clientContractID'] = 'X';
                        $data['contractUID'] = 159;
                        $data['supplierCodeSystem'] = $masterData->customerID;

                        $data['documentTransCurrencyID'] = $masterData->transactionCurrencyID;
                        $data['documentTransCurrencyER'] = $masterData->transactionCurrencyER;
                       // $data['documentTransAmount'] = $masterData->transactionAmount + $masterData->VATAmount;;
                        $data['documentTransAmount'] = 0;

                        $data['documentLocalCurrencyID'] = $masterData->companyLocalCurrencyID;
                        $data['documentLocalCurrencyER'] = $masterData->companyLocalCurrencyER;
                        $data['documentLocalAmount'] = $masterData->companyLocalAmount + $masterData->VATAmountLocal;

                        $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                        $data['documentRptCurrencyER'] = $masterData->companyReportingCurrencyER;
                        $data['documentRptAmount'] = $masterData->companyReportingAmount + $masterData->VATAmountRpt;

                        $data['documentType'] = 11;

                        $data['createdUserSystemID'] = $empID->employeeSystemID;
                        $data['createdDateTime'] = $time;
                        $data['createdUserID'] = $empID->empID;
                        $data['createdUserPC'] = getenv('COMPUTERNAME');
                        $data['timestamp'] = $time;
                        array_push($finalData, $data);

                        $bs = DeliveryOrderDetail::selectRaw("0 as transAmount, SUM(qtyIssuedDefaultMeasure * wacValueLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * wacValueReporting) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,companyLocalCurrencyID,companyLocalCurrencyER,companyReportingCurrencyER,companyReportingCurrencyID")->WHERE('deliveryOrderID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->first();
                        //get pnl account
                        $pl = DeliveryOrderDetail::selectRaw("0 as transAmount,SUM(qtyIssuedDefaultMeasure * wacValueLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * wacValueReporting) as rptAmount,financeGLcodePLSystemID,financeGLcodePL,companyLocalCurrencyID,companyLocalCurrencyER,companyReportingCurrencyER,companyReportingCurrencyID")->WHERE('deliveryOrderID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->where('financeGLcodePLSystemID', '>', 0)->groupBy('financeGLcodePLSystemID')->get();

                        $revenue = DeliveryOrderDetail::selectRaw("0 as transAmount,SUM(qtyIssuedDefaultMeasure * (companyLocalAmount - (companyLocalAmount*discountPercentage/100))) as localAmount, SUM(qtyIssuedDefaultMeasure * (companyReportingAmount - (companyReportingAmount*discountPercentage/100))) as rptAmount,financeGLcodeRevenueSystemID,financeGLcodeRevenue,companyLocalCurrencyID,companyLocalCurrencyER,companyReportingCurrencyER,companyReportingCurrencyID")->WHERE('deliveryOrderID', $masterModel["autoID"])->whereNotNull('financeGLcodeRevenueSystemID')->where('financeGLcodeRevenueSystemID', '>', 0)->groupBy('financeGLcodeRevenueSystemID')->get();

                        if ($bs) {

                            $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                            $data['glCode'] = $bs->financeGLcodebBS;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                            $data['documentTransCurrencyID'] = $masterData->transactionCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->transactionCurrencyER;
                            $data['documentTransAmount'] = ABS($bs->transAmount) * -1;

                            $data['documentLocalCurrencyID'] = $bs->companyLocalCurrencyID;
                            $data['documentLocalCurrencyER'] = $bs->companyLocalCurrencyER;
                            $data['documentLocalAmount'] = ABS($bs->localAmount) * -1;

                            $data['documentRptCurrencyID'] = $bs->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $bs->companyReportingCurrencyER;
                            $data['documentRptAmount'] = ABS($bs->rptAmount) * -1;

                            array_push($finalData, $data);
                        }

                        if ($pl) {
                            foreach ($pl as $item) {
                                $data['chartOfAccountSystemID'] = $item->financeGLcodePLSystemID;
                                $data['glCode'] = $item->financeGLcodePL;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                $data['documentTransCurrencyID'] = $masterData->transactionCurrencyID;
                                $data['documentTransCurrencyER'] = $masterData->transactionCurrencyER;
                                $data['documentTransAmount'] = ABS($item->transAmount);

                                $data['documentLocalCurrencyID'] = $item->companyLocalCurrencyID;
                                $data['documentLocalCurrencyER'] = $item->companyLocalCurrencyER;
                                $data['documentLocalAmount'] = ABS($item->localAmount);

                                $data['documentRptCurrencyID'] = $item->companyReportingCurrencyID;
                                $data['documentRptCurrencyER'] = $item->companyReportingCurrencyER;
                                $data['documentRptAmount'] = ABS($item->rptAmount);

                                array_push($finalData, $data);
                            }
                        }

                        if ($revenue) {

                            foreach ($revenue as $item) {

                                $data['chartOfAccountSystemID'] = $item->financeGLcodeRevenueSystemID;
                                $data['glCode'] = $item->financeGLcodeRevenue;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                $data['documentTransCurrencyID'] = $masterData->transactionCurrencyID;
                                $data['documentTransCurrencyER'] = $masterData->transactionCurrencyER;
                                $data['documentTransAmount'] = ABS($item->transAmount) * -1;

                                $data['documentLocalCurrencyID'] = $item->companyLocalCurrencyID;
                                $data['documentLocalCurrencyER'] = $item->companyLocalCurrencyER;
                                $data['documentLocalAmount'] = ABS($item->localAmount) * -1;

                                $data['documentRptCurrencyID'] = $item->companyReportingCurrencyID;
                                $data['documentRptCurrencyER'] = $item->companyReportingCurrencyER;
                                $data['documentRptAmount'] = ABS($item->rptAmount) * -1;

                                array_push($finalData, $data);
                            }

                        }

                        $erp_taxdetail = Taxdetail::where('companySystemID', $masterData->companySystemID)
                                ->where('documentSystemCode', $masterData->deliveryOrderID)
                                ->where('documentSystemID', 71)
                                ->get();

                        if (!empty($erp_taxdetail)) {
                            $taxConfigData = TaxService::getOutputVATTransferGLAccount($masterModel["companySystemID"]);
                            if (!empty($taxConfigData)) {
                                $taxGL = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')
                                    ->where('chartOfAccountSystemID', $taxConfigData->outputVatTransferGLAccountAutoID)
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
                                        $data['documentTransAmount'] = 0;
                                        $data['documentLocalCurrencyID'] = $tax->localCurrencyID;
                                        $data['documentLocalCurrencyER'] = $tax->localCurrencyER;
                                        $data['documentLocalAmount'] = $tax->localAmount * -1;
                                        $data['documentRptCurrencyID'] = $tax->rptCurrencyID;
                                        $data['documentRptCurrencyER'] = $tax->rptCurrencyER;
                                        $data['documentRptAmount'] = $tax->rptAmount * -1;
                                        array_push($finalData, $data);

                                        $taxLedgerData['outputVatTransferGLAccountID'] = $taxGL['chartOfAccountSystemID'];
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


                        break;
                    case 87: // sales return
                        $masterData = SalesReturn::with(['detail' => function ($query) {
                            $query->selectRaw('SUM(companyLocalAmount) as localAmount, SUM(companyReportingAmount) as rptAmount,SUM(transactionAmount) as transAmount,salesReturnID');
                        }], 'finance_period_by')->find($masterModel["autoID"]);

                        //all acoount
                        $allAc = SalesReturnDetail::selectRaw("SUM(wacValueLocal*qtyReturned) as localAmount, SUM(wacValueReporting*qtyReturned) as rptAmount,SUM(transactionAmount) as transAmount, reasonCode, isPostItemLedger, reasonGLCode, financeGLcodebBSSystemID as financeGLcodebBSSystemID,financeGLcodebBS as financeGLcodebBS,companyLocalCurrencyID as localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,transactionCurrencyID as transCurrencyID,companyReportingCurrencyER as reportingCurrencyER,companyLocalCurrencyER as localCurrencyER,transactionCurrencyER as transCurrencyER, financeGLcodeRevenueSystemID")
                            ->WHERE('salesReturnID', $masterModel["autoID"])
                            ->groupBy('financeGLcodebBSSystemID')
                            ->get();

                        //all acoount
                        $COSGAc = SalesReturnDetail::selectRaw("SUM(wacValueLocal*qtyReturned) as localAmount, SUM(wacValueReporting*qtyReturned) as rptAmount,SUM(transactionAmount) as transAmount,financeGLcodebBSSystemID as financeGLcodebBSSystemID,financeGLcodebBS as financeGLcodebBS,companyLocalCurrencyID as localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,transactionCurrencyID as transCurrencyID,companyReportingCurrencyER as reportingCurrencyER,companyLocalCurrencyER as localCurrencyER,transactionCurrencyER as transCurrencyER, financeGLcodeRevenueSystemID, financeGLcodePLSystemID, financeGLcodePL")
                            ->WHERE('salesReturnID', $masterModel["autoID"])
                            ->groupBy('financeGLcodePLSystemID')
                            ->get();

                        //all acoount
                        $revenueAc = SalesReturnDetail::selectRaw("SUM(companyLocalAmount) as localAmount, SUM(companyReportingAmount) as rptAmount,SUM(transactionAmount) as transAmount,financeGLcodebBSSystemID as financeGLcodebBSSystemID,financeGLcodebBS as financeGLcodebBS,companyLocalCurrencyID as localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,transactionCurrencyID as transCurrencyID,companyReportingCurrencyER as reportingCurrencyER,companyLocalCurrencyER as localCurrencyER,transactionCurrencyER as transCurrencyER, financeGLcodeRevenueSystemID, financeGLcodeRevenue")
                            ->WHERE('salesReturnID', $masterModel["autoID"])
                            ->groupBy('financeGLcodeRevenueSystemID')
                            ->get();

                        $masterDocumentDate = date('Y-m-d H:i:s');
                        if ($masterData->finance_period_by->isActive == -1) {
                            $masterDocumentDate = $masterData->salesReturnDate;
                        }

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
                            array_push($finalData, $data);

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

                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['glCode'] = $val->financeGLcodePL;
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
                                    array_push($finalData, $data);
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
                                            array_push($finalData, $data);
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
                        break;
                    case 97: // SA - Stock Count
                        $masterData = StockCount::find($masterModel["autoID"]);
                        //get balansheet account
                        if ($masterData->stockCountType == 2) {

                            // $bs = StockAdjustmentDetails::selectRaw("SUM(currenctStockQty * (wacAdjLocal - currentWaclocal)) as localAmount, SUM(currenctStockQty * (wacAdjRpt - currentWacRpt)) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,currentWacLocalCurrencyID as localCurrencyID,currentWacRptCurrencyID as reportingCurrencyID,wacAdjRptER as reportingCurrencyER,wacAdjLocalER as localCurrencyER")->WHERE('stockAdjustmentAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->first();
                            // //get pnl account
                            // $pl = StockAdjustmentDetails::selectRaw("SUM(currenctStockQty * (wacAdjLocal - currentWaclocal)) as localAmount, SUM(currenctStockQty * (wacAdjRpt - currentWacRpt)) as rptAmount,financeGLcodePLSystemID,financeGLcodePL,currentWacLocalCurrencyID as localCurrencyID,currentWacRptCurrencyID as reportingCurrencyID,wacAdjRptER as reportingCurrencyER,wacAdjLocalER as localCurrencyER")->WHERE('stockAdjustmentAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->where('financeGLcodePLSystemID', '>', 0)->groupBy('financeGLcodePLSystemID')->get();

                        } else {
                            $bs = StockCountDetail::selectRaw("SUM(adjustedQty * wacAdjLocal) as localAmount, SUM(adjustedQty * wacAdjRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,currentWacLocalCurrencyID as localCurrencyID,currentWacRptCurrencyID as reportingCurrencyID,wacAdjRptER as reportingCurrencyER,wacAdjLocalER as localCurrencyER")->WHERE('stockCountAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->first();
                            //get pnl account
                            $pl = StockCountDetail::selectRaw("SUM(adjustedQty * wacAdjLocal) as localAmount, SUM(adjustedQty * wacAdjRpt) as rptAmount,financeGLcodePLSystemID,financeGLcodePL,currentWacLocalCurrencyID as localCurrencyID,currentWacRptCurrencyID as reportingCurrencyID,wacAdjRptER as reportingCurrencyER,wacAdjLocalER as localCurrencyER")->WHERE('stockCountAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->where('financeGLcodePLSystemID', '>', 0)->groupBy('financeGLcodePLSystemID')->get();
                        }

                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                            $data['serviceLineCode'] = $masterData->serviceLineCode;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->stockCountCode;
                            $data['documentDate'] = date('Y-m-d H:i:s');
                            $data['documentYear'] = \Helper::dateYear($masterData->stockCountDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterData->stockCountDate);
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->comment;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = 0;
                            $data['documentTransCurrencyID'] = 0;
                            $data['documentTransCurrencyER'] = 0;
                            $data['documentTransAmount'] = 0;
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = 0;
                            $data['nonHoldingPercentage'] = 0;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            if ($bs) {
                                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                $data['glCode'] = $bs->financeGLcodebBS;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                $data['documentLocalCurrencyID'] = $bs->localCurrencyID;
                                $data['documentLocalCurrencyER'] = $bs->localCurrencyER;
                                $data['documentLocalAmount'] = $bs->localAmount;
                                $data['documentRptCurrencyID'] = $bs->reportingCurrencyID;
                                $data['documentRptCurrencyER'] = $bs->reportingCurrencyER;
                                $data['documentRptAmount'] = $bs->rptAmount;
                                $data['timestamp'] = \Helper::currentDateTime();
                                array_push($finalData, $data);
                            }

                            if ($pl) {
                                foreach ($pl as $val) {
                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['glCode'] = $val->financeGLcodePL;
                                    $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                    $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);
                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                    $data['documentLocalAmount'] = $val->localAmount * -1;
                                    $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                                    $data['documentRptAmount'] = $val->rptAmount * -1;
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        break;
                    default:
                        Log::warning('Document ID not found ' . date('H:i:s'));
                }

                if ($finalData) {
                    Log::info($finalData);
                    //$generalLedgerInsert = GeneralLedger::insert($finalData);
                    foreach ($finalData as $data) {
                        GeneralLedger::create($data);
                    }
                    $generalLedgerInsert = true;
                    Log::info('Successfully inserted to GL table ' . date('H:i:s'));

                    if ($generalLedgerInsert) {
                        // updating posted date in relevant documents

                        // getting general ledger document date
                        $glDocumentDate = GeneralLedger::selectRaw('documentDate')
                            ->where('documentSystemID', $masterModel["documentSystemID"])
                            ->where('companySystemID', $masterModel["companySystemID"])
                            ->where('documentSystemCode', $masterModel["autoID"])
                            ->first();

                        switch ($masterModel["documentSystemID"]) {
                            case 11: // Supplier Invoice
                                $documentUpdateData = BookInvSuppMaster::find($masterModel["autoID"]);

                                if ($glDocumentDate) {
                                    $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                                    $documentUpdateData->save();
                                }
                                break;
                            case 15: // Debit note
                                $documentUpdateData = DebitNote::find($masterModel["autoID"]);

                                if ($glDocumentDate) {
                                    $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                                    $documentUpdateData->save();
                                }
                                break;
                            case 4: // Payment Voucher
                                $documentUpdateData = PaySupplierInvoiceMaster::find($masterModel["autoID"]);

                                if ($glDocumentDate) {
                                    $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                                    $documentUpdateData->save();
                                }
                                break;
                            case 20: // Customer Invoice
                                $documentUpdateData = CustomerInvoiceDirect::find($masterModel["autoID"]);

                                if ($glDocumentDate) {
                                    $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                                    $documentUpdateData->save();
                                }
                                break;
                            case 19: // Credit Note
                                $documentUpdateData = CreditNote::find($masterModel["autoID"]);

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
                            case 17: //  Journal Voucher
                                $documentUpdateData = JvMaster::find($masterModel["autoID"]);

                                if ($glDocumentDate) {
                                    $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                                    $documentUpdateData->save();
                                }
                                break;
                            case 10: //  Stock Receive
                                $documentUpdateData = StockReceive::find($masterModel["autoID"]);

                                if ($glDocumentDate) {
                                    $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                                    $documentUpdateData->save();
                                }
                                break;
                            case 13: //  Stock Transfer
                                $documentUpdateData = StockTransfer::find($masterModel["autoID"]);

                                if ($glDocumentDate) {
                                    $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                                    $documentUpdateData->save();
                                }
                                break;
                            case 22: //  Fixed Asset
                                $documentUpdateData = FixedAssetMaster::find($masterModel["autoID"]);

                                if ($glDocumentDate) {
                                    $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                                    $documentUpdateData->save();
                                }
                                break;

                            case 71: // Delivery Order
                                $documentUpdateData = DeliveryOrder::find($masterModel["autoID"]);

                                if ($glDocumentDate) {
                                    $documentUpdateData->postedDate = $glDocumentDate->documentDate;
                                    $documentUpdateData->save();
                                }
                                break;

                            default:
                                Log::warning('Posted date document id not found ' . date('H:i:s'));
                        }
                        if (in_array($masterModel["documentSystemID"], [15, 11, 4, 24])) {

                            if ($masterModel["documentSystemID"] == 24) {
                                $prData = PurchaseReturn::find($masterModel["autoID"]);
                                if ($prData->isInvoiceCreatedForGrv == 1) {
                                    $apLedgerInsert = \App\Jobs\AccountPayableLedgerInsert::dispatch($masterModel);
                                } else {
                                    $prnDetails = PurchaseReturnDetails::where('purhaseReturnAutoID', $masterModel["autoID"])->first();
                                    $unbilledModel['supplierID'] = $prData->supplierID;
                                    $unbilledModel['forPrn'] = true;
                                    $unbilledModel['purhaseReturnAutoID'] = $prData->purhaseReturnAutoID;
                                    $unbilledModel['autoID'] = $prnDetails->grvAutoID;
                                    $unbilledModel['companySystemID'] = $prData->companySystemID;
                                    $unbilledModel['documentSystemID'] = $masterModel['documentSystemID'];
                                    $jobUGRV = UnbilledGRVInsert::dispatch($unbilledModel);
                                }
                            } else if ($masterModel["documentSystemID"] == 11) {
                                $suppInvData = BookInvSuppMaster::find($masterModel["autoID"]);
                                if ($suppInvData->documentType == 4) {
                                    $apLedgerInsert = \App\Jobs\EmployeeLedgerInsert::dispatch($masterModel);
                                } else {
                                    $apLedgerInsert = \App\Jobs\AccountPayableLedgerInsert::dispatch($masterModel);
                                }
                            } else if ($masterModel["documentSystemID"] == 4) {
                                $suppInvData = PaySupplierInvoiceMaster::find($masterModel["autoID"]);
                                if ($suppInvData->invoiceType == 6) {
                                    $apLedgerInsert = \App\Jobs\EmployeeLedgerInsert::dispatch($masterModel);
                                } else {
                                    if ($suppInvData->invoiceType != 3) {
                                        $apLedgerInsert = \App\Jobs\AccountPayableLedgerInsert::dispatch($masterModel);
                                    }
                                }
                            } else {
                                $apLedgerInsert = \App\Jobs\AccountPayableLedgerInsert::dispatch($masterModel);
                            }
                        }
                        if (in_array($masterModel["documentSystemID"], [19, 20, 21, 87])) {
                            $arLedgerInsert = \App\Jobs\AccountReceivableLedgerInsert::dispatch($masterModel);
                        }


                        if (!empty($taxLedgerData)) {
                            $updateVATLedger = TaxLedgerInsert::dispatch($masterModel, $taxLedgerData);
                        }
                    }
                    DB::commit();
                    Log::info('---- GL End Successfully -----' . date('H:i:s'));
                }

            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
                Log::info('Error Line No: ' . $e->getLine());
                Log::info('Error Line No: ' . $e->getFile());
                Log::info($e->getMessage());
                Log::info('---- GL  End with Error-----' . date('H:i:s'));
            }
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

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
use App\Models\POSGLEntries;
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

class GrvGlService
{
	public static function processEntry($masterModel)
	{
		$data = [];
        $taxLedgerData = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
		
        $masterData = GRVMaster::with(['details' => function ($query) {
            $query->selectRaw("SUM(GRVcostPerUnitLocalCur*noQty) as localAmount, SUM(GRVcostPerUnitComRptCur*noQty) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount ,grvAutoID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER");
        }])->find($masterModel["autoID"]);
        //get balansheet account
        $bs = GRVDetails::selectRaw("SUM(landingCost_LocalCur*noQty) as localAmount, SUM(landingCost_RptCur*noQty) as rptAmount,SUM(landingCost_TransCur*noQty) as transAmount,financeGLcodebBSSystemID,financeGLcodebBS,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER, erp_grvdetails.grvDetailsID")->WHERE('grvAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->get();

        //get pnl account
        $pl = GRVDetails::selectRaw("SUM(landingCost_LocalCur*noQty) as localAmount, SUM(landingCost_RptCur*noQty) as rptAmount,SUM(landingCost_TransCur*noQty) as transAmount,financeGLcodePLSystemID,financeGLcodePL,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER")->WHERE('grvAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->where('financeGLcodePLSystemID', '>', 0)->WHERE('includePLForGRVYN', -1)->groupBy('financeGLcodePLSystemID')->get();

        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

        $postedDateGl = isset($masterModel['documentDateOveride']) ? $masterModel['documentDateOveride'] : $validatePostedDate['postedDate'];

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
            ->groupBy('erp_purchaseorderadvpayment.UnbilledGRVAccountSystemID', 'erp_purchaseorderadvpayment.supplierID', 'erp_purchaseorderadvpayment.currencyID')
            ->get();


        $unbilledGRVVATAddVatOnPO = TaxService::poLogisticVATDistributionForGRV($masterModel["autoID"]);
        $vatDetails = TaxService::processGrvVAT($masterModel["autoID"]);

        Log::info('Total Logistic VAT');
        Log::info($unbilledGRVVATAddVatOnPO);

        if ($masterData) {
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
            $data['documentDate'] = $postedDateGl;
            $data['documentYear'] = \Helper::dateYear($postedDateGl);
            $data['documentMonth'] = \Helper::dateMonth($postedDateGl);
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

            $exemptExpenseDetails = TaxService::processGrvExpense($masterModel["autoID"]);
            $expenseCOA = TaxVatCategories::with(['tax'])->where('subCatgeoryType', 3)->whereHas('tax', function ($query) use ($masterData) {
                $query->where('companySystemID', $masterData->companySystemID);
            })->where('isActive', 1)->first();

            if(!empty($exemptExpenseDetails) && !empty($expenseCOA) && $expenseCOA->expenseGL != null && !$rcmActivated){
                $exemptVatTrans = $exemptExpenseDetails->VATAmount;
                $exemptVATLocal = $exemptExpenseDetails->VATAmountLocal;
                $exemptVatRpt = $exemptExpenseDetails->VATAmountRpt;

                if($exemptVatTrans != 0) {
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
            }

            if ((($valEligible || TaxService::isGRVRCMActivation($masterModel["autoID"])) && ($vatDetails['masterVATTrans'] || $exemptVATTrans))) {
                Log::info('Inside the Vat Entry Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                $taxData = TaxService::getInputVATTransferGLAccount($masterData->companySystemID);

                if ($vatDetails['masterVATTrans'] > 0) {
                    if (!empty($taxData)) {
                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxData->inputVatTransferGLAccountAutoID)
                            ->where('companySystemID', $masterData->companySystemID)
                            ->first();

                        if (!empty($chartOfAccountData)) {
                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                            $data['glCode'] = $chartOfAccountData->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);


                            $data['documentTransAmount'] = \Helper::roundValue($vatDetails['masterVATTrans']);
                            $data['documentLocalAmount'] = \Helper::roundValue($vatDetails['masterVATLocal']);
                            $data['documentRptAmount'] = \Helper::roundValue($vatDetails['masterVATRpt']);

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

                if($unbilledGRV) {
                    foreach ($unbilledGRV as $val) {
                        $unbilledTransVATAmount =  $val['VATAmount'];
                        $unbilledLocalVATAmount =  $val['VATAmountLocal'];
                        $unbilledRptVATAmount   =  $val['VATAmountRpt'];

                        $taxData = TaxService::getInputVATTransferGLAccount($masterData->companySystemID);

                        if (!empty($taxData)) {
                            $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxData->inputVatTransferGLAccountAutoID)
                                ->where('companySystemID', $masterData->companySystemID)
                                ->first();

                            if (!empty($chartOfAccountData)) {
                                $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                                $data['supplierCodeSystem'] = $val['supplierID'];
                                $data['glCode'] = $chartOfAccountData->AccountCode;
                                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                                $data['documentTransCurrencyID'] = $val['supplierTransactionCurrencyID'];
                                $data['documentTransCurrencyER'] = $val['supplierTransactionCurrencyER'];
                                $data['documentTransAmount'] = \Helper::roundValue($unbilledTransVATAmount);

                                $data['documentRptCurrencyID'] = $val['companyReportingCurrencyID'];
                                $data['documentRptCurrencyER'] = $val['companyReportingER'];
                                $data['documentRptAmount'] = \Helper::roundValue($unbilledRptVATAmount);

                                $data['documentLocalCurrencyID'] = $val['localCurrencyID'];
                                $data['documentLocalCurrencyER'] = $val['localCurrencyER'];
                                $data['documentLocalAmount'] = \Helper::roundValue($unbilledLocalVATAmount);

                                array_push($finalData, $data);

                                $taxLedgerData['inputVatTransferAccountID'] = $chartOfAccountData->chartOfAccountSystemID;

                            }
                        }
                    }
                }

                if(TaxService::isGRVRCMActivation($masterModel["autoID"])){

                    $taxDataOutputTransfer = TaxService::getOutputVATTransferGLAccount($masterData->companySystemID);
                    Log::info('Inside the Vat Entry OutputVATTransferGLAccoun Issues Id :' . $masterModel["autoID"] . ', date :' . date('H:i:s'));
                    if (!empty($taxDataOutputTransfer) && ($rcmActivated && $transVATAmount > 0)) {
                        $chartOfAccountData = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $taxDataOutputTransfer->outputVatTransferGLAccountAutoID)
                            ->where('companySystemID', $masterData->companySystemID)
                            ->first();

                        if (!empty($chartOfAccountData)) {
                            $data['chartOfAccountSystemID'] = $chartOfAccountData->chartOfAccountSystemID;
                            $data['glCode'] = $chartOfAccountData->AccountCode;
                            $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                            $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                            $data['documentTransAmount'] = !$rcmActivated ? \Helper::roundValue(($transVATAmount + $exemptVATTrans)) * -1 : \Helper::roundValue(($transVATAmount)) * -1;
                            $data['documentLocalAmount'] = !$rcmActivated ? \Helper::roundValue(($localVATAmount + $exemptVATLocal)) * -1 : \Helper::roundValue(($localVATAmount)) * -1;
                            $data['documentRptAmount'] = !$rcmActivated ? \Helper::roundValue(($rptVATAmount + $exemptVATRpt)) * -1 : \Helper::roundValue(($rptVATAmount )) * -1;
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


                    $data['documentTransAmount'] = $rcmActivated && $expenseCOA->recordType == 2 ? \Helper::roundValue(ABS($val->transAmount) - $exemptVATTrans): \Helper::roundValue(ABS($val->transAmount) + $transBSVAT + $exemptVATTransAmount);

                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                    $data['documentLocalAmount'] =  $rcmActivated && $expenseCOA->recordType == 2 ? \Helper::roundValue(ABS($val->localAmount) - $exemptVATLocal):  \Helper::roundValue(ABS($val->localAmount) + $localBSVAT + $exemptVATLocalAmount);

                    $data['documentRptCurrencyID'] = $val->companyReportingCurrencyID;
                    $data['documentRptCurrencyER'] = $val->companyReportingER;
                    $data['documentRptAmount'] = $rcmActivated && $expenseCOA->recordType == 2 ? \Helper::roundValue(ABS($val->rptAmount) - $exemptVATRpt): \Helper::roundValue(ABS($val->rptAmount) + $rptBSVAT + $exemptVATRptAmount);
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

                    $vatData = TaxService::poLogisticForLineWise($val);
                    //$vatData = TaxService::poLogisticVATDistributionForGRV($masterModel["autoID"],0,$val->supplierID);

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
        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
	}
}

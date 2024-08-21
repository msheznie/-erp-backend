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

class PurchaseReturnGlService
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

        $postedDateGl = isset($masterModel['documentDateOveride']) ? $masterModel['documentDateOveride'] : $validatePostedDate['postedDate'];


        $masterData = PurchaseReturn::with(['details' => function ($query) {
            $query->selectRaw("SUM(noQty * GRVcostPerUnitLocalCur) as localAmount, SUM(noQty * GRVcostPerUnitComRptCur) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,purhaseReturnAutoID, SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount, supplierTransactionCurrencyID, supplierTransactionER, localCurrencyID, localCurrencyER, companyReportingCurrencyID, companyReportingER");
        }])->find($masterModel["autoID"]);
        //get balansheet account
        $bs = PurchaseReturnDetails::selectRaw("SUM(erp_purchasereturndetails.noQty * erp_grvdetails.landingCost_LocalCur) as localAmount, SUM(erp_purchasereturndetails.noQty * erp_grvdetails.landingCost_RptCur) as rptAmount,SUM(erp_grvdetails.landingCost_TransCur * erp_purchasereturndetails.noQty) as transAmount,erp_purchasereturndetails.financeGLcodebBSSystemID,erp_purchasereturndetails.financeGLcodebBS,erp_purchasereturndetails.localCurrencyID,erp_purchasereturndetails.companyReportingCurrencyID as reportingCurrencyID,erp_purchasereturndetails.supplierTransactionCurrencyID,erp_purchasereturndetails.supplierTransactionER,erp_purchasereturndetails.companyReportingER,erp_purchasereturndetails.localCurrencyER")
                                   ->join('erp_grvdetails', 'erp_grvdetails.grvDetailsID', '=', 'erp_purchasereturndetails.grvDetailsID')
                                   ->WHERE('erp_purchasereturndetails.purhaseReturnAutoID', $masterModel["autoID"])
                                   ->first();

        $expenseCOA = TaxVatCategories::with(['tax'])->where('subCatgeoryType', 3)->whereHas('tax', function ($query) use ($masterData) {
            $query->where('companySystemID', $masterData->companySystemID);
        })->where('isActive', 1)->first();

        $exemptVatTotal = PurchaseReturnDetails::selectRaw("SUM(VATAmount * noQty) as vatAmount, SUM(VATAmountLocal * noQty) as VATAmountLocal, SUM(VATAmountRpt * noQty) as VATAmountRpt")->whereHas('vatSubCategories', function ($q) {
            $q->where('subCatgeoryType', 3);
        })->WHERE('purhaseReturnAutoID', $masterModel["autoID"])->first();

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
            $data['documentDate'] = $postedDateGl;
            $data['documentYear'] = \Helper::dateYear($postedDateGl);
            $data['documentMonth'] = \Helper::dateMonth($postedDateGl);
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
                        if($data['documentTransAmount'] != 0) {
                            array_push($finalData, $data);
                        }
                    } else {
                        if ($masterData->isInvoiceCreatedForGrv == 1) {
                            return ['status' => false, 'error' => ['message' => "Input Vat GL Account not assigned to company"]];
                        } else {
                            return ['status' => false, 'error' => ['message' => "Input Vat Transfer GL Account not assigned to company"]];
                        }
                    }
                } else {
                    if ($masterData->isInvoiceCreatedForGrv == 1) {
                        return ['status' => false, 'error' => ['message' => "Input Vat GL Account not configured"]];
                    } else {
                        return ['status' => false, 'error' => ['message' => "Input Vat Transfer GL Account not configured"]];
                    }

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
                        } else {
                            return ['status' => false, 'error' => ['message' => "Output Vat Transfer GL Account not assigned to company"]];
                        }
                    } else {
                        return ['status' => false, 'error' => ['message' => "Output Vat Transfer GL Account not configured"]];
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
                $data['documentTransAmount'] = $exemptVatTrans * -1;
                $data['documentLocalAmount'] = $exemptVATLocal * -1;
                $data['documentRptAmount'] = $exemptVatRpt * -1;
                $data['timestamp'] = \Helper::currentDateTime();
                array_push($finalData, $data);
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

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
    }
}

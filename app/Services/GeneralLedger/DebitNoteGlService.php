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

class DebitNoteGlService
{
	public static function processEntry($masterModel)
	{
        $data = [];
        $taxLedgerData = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
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

        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

        $masterDocumentDate = isset($masterModel['documentDateOveride']) ? $masterModel['documentDateOveride'] : $validatePostedDate['postedDate'];

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
            $data['employeeSystemID'] = $masterData->empID;

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

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
    }
}

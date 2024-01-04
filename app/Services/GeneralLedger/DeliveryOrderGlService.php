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

class DeliveryOrderGlService
{
	public static function processEntry($masterModel)
	{
         $data = [];
        $taxLedgerData = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
        $masterData = DeliveryOrder::with(['finance_period_by'])->find($masterModel["autoID"]);
        $company = Company::select('masterComapanyID')->where('companySystemID', $masterData->companySystemID)->first();

        $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->custUnbilledAccountSystemID)->first();

        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

        $masterDocumentDate = isset($masterModel['documentDateOveride']) ? $masterModel['documentDateOveride'] : $validatePostedDate['postedDate'];
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
        // array_push($finalData, $data);

        $bs = DeliveryOrderDetail::selectRaw("0 as transAmount, SUM(qtyIssuedDefaultMeasure * wacValueLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * wacValueReporting) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,companyLocalCurrencyID,companyLocalCurrencyER,companyReportingCurrencyER,companyReportingCurrencyID")->WHERE('deliveryOrderID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->where('financeGLcodebBSSystemID', '>', 0)->groupBy('financeGLcodebBSSystemID')->get();
        //get pnl account
        $pl = DeliveryOrderDetail::selectRaw("0 as transAmount,SUM(qtyIssuedDefaultMeasure * wacValueLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * wacValueReporting) as rptAmount,financeCogsGLcodePLSystemID,financeCogsGLcodePL,companyLocalCurrencyID,companyLocalCurrencyER,companyReportingCurrencyER,companyReportingCurrencyID")->WHERE('deliveryOrderID', $masterModel["autoID"])->whereNotNull('financeCogsGLcodePLSystemID')->where('financeCogsGLcodePLSystemID', '>', 0)->groupBy('financeCogsGLcodePLSystemID')->get();

        $revenue = DeliveryOrderDetail::selectRaw("0 as transAmount,SUM(qtyIssuedDefaultMeasure * (companyLocalAmount - (companyLocalAmount*discountPercentage/100))) as localAmount, SUM(qtyIssuedDefaultMeasure * (companyReportingAmount - (companyReportingAmount*discountPercentage/100))) as rptAmount,financeGLcodeRevenueSystemID,financeGLcodeRevenue,companyLocalCurrencyID,companyLocalCurrencyER,companyReportingCurrencyER,companyReportingCurrencyID")->WHERE('deliveryOrderID', $masterModel["autoID"])->whereNotNull('financeGLcodeRevenueSystemID')->where('financeGLcodeRevenueSystemID', '>', 0)->groupBy('financeGLcodeRevenueSystemID')->get();

        if ($bs) {
            foreach ($bs as $key => $value) {
                $data['chartOfAccountSystemID'] = $value->financeGLcodebBSSystemID;
                $data['glCode'] = $value->financeGLcodebBS;
                $data['glAccountType'] = ChartOfAccount::getGlAccountType($data['chartOfAccountSystemID']);
                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($data['chartOfAccountSystemID']);

                $data['documentTransCurrencyID'] = $masterData->transactionCurrencyID;
                $data['documentTransCurrencyER'] = $masterData->transactionCurrencyER;
                $data['documentTransAmount'] = ABS($value->transAmount) * -1;

                $data['documentLocalCurrencyID'] = $value->companyLocalCurrencyID;
                $data['documentLocalCurrencyER'] = $value->companyLocalCurrencyER;
                $data['documentLocalAmount'] = ABS($value->localAmount) * -1;

                $data['documentRptCurrencyID'] = $value->companyReportingCurrencyID;
                $data['documentRptCurrencyER'] = $value->companyReportingCurrencyER;
                $data['documentRptAmount'] = ABS($value->rptAmount) * -1;

                array_push($finalData, $data);
            }
        }

        if ($pl) {
            foreach ($pl as $item) {
                $data['chartOfAccountSystemID'] = $item->financeCogsGLcodePLSystemID;
                $data['glCode'] = $item->financeCogsGLcodePL;
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

                // array_push($finalData, $data);
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
                        // array_push($finalData, $data);

                        $taxLedgerData['outputVatTransferGLAccountID'] = $taxGL['chartOfAccountSystemID'];
                    }
                } else {
                    return ['status' => false, 'error' => ['message' => "Output Vat Transfer GL Account not assigned to company"]];
                }
            } else {
                return ['status' => false, 'error' => ['message' => "Output Vat Transfer GL Account not configured"]];
            }
        }

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
    }
}

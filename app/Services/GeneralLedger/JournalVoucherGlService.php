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

class JournalVoucherGlService
{
	public static function processEntry($masterModel)
	{
         $data = [];
        $taxLedgerData = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
        $masterData = JvMaster::with(['detail' => function ($query) {
            $query->selectRaw('SUM(debitAmount) as debitAmountTot, SUM(creditAmount) as creditAmountTot,jvMasterAutoId');
        }], 'financeperiod_by', 'company')->find($masterModel["autoID"]);

        $detailRecords = JvDetail::selectRaw("sum(debitAmount) as debitAmountTot, sum(creditAmount) as creditAmountTot, contractUID, clientContractID, comments, chartOfAccountSystemID, serviceLineSystemID,serviceLineCode,currencyID,currencyER")->WHERE('jvMasterAutoId', $masterModel["autoID"])->groupBy('chartOfAccountSystemID', 'serviceLineSystemID', 'comments', 'contractUID')->get();

        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

        $masterDocumentDate = isset($masterModel['documentDateOveride']) ? $masterModel['documentDateOveride'] : $validatePostedDate['postedDate'];

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
                if($chartOfAccount) {
                    $data['glCode'] = $chartOfAccount->AccountCode;
                }
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

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
    }
}

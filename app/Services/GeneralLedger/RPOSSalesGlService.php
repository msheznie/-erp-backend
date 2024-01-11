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
use App\Models\POSSOURCEShiftDetails;
use App\Models\CurrencyMaster;

class RPOSSalesGlService
{
	public static function processEntry($masterModel)
	{
        Log::useFiles(storage_path() . '/logs/cash_rpos_jobs.log');

        $data = [];
        $taxLedgerData = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);

        $glEntries = DB::table('pos_gl_entries')
            ->selectRaw('pos_gl_entries.*, SUM(amount) as totAmount')
            ->where('pos_gl_entries.shiftID', $masterModel["autoID"])
            ->groupBy('pos_gl_entries.glCode')
            ->groupBy('pos_gl_entries.invoiceID')
            ->get();

        foreach($glEntries as $gl) {
            $invItems = DB::table('pos_source_menusalesmaster')
                ->selectRaw('pos_source_menusalesmaster.*')
                ->where('pos_source_menusalesmaster.shiftID', $masterModel["autoID"])
                ->where('pos_source_menusalesmaster.menuSalesID', $gl->invoiceID)
                ->first();


                $sourceDetails = POSSOURCEShiftDetails::where("shiftId", $masterModel["autoID"])->select('transactionCurrency')->first();
                $currency = CurrencyMaster::where("CurrencyCode", $sourceDetails->transactionCurrency)->first();
                $data['documentTransCurrencyID'] = $currency->currencyID;

                $data['companySystemID'] = $masterModel['companySystemID'];
                $data['companyID'] = $masterModel["companyID"];
                $segments = DB::table('pos_source_shiftdetails')
                    ->selectRaw('pos_source_shiftdetails.segmentID as segmentID, serviceline.ServiceLineCode as serviceLineCode')
                    ->join('serviceline', 'serviceline.serviceLineSystemID', '=', 'pos_source_shiftdetails.segmentID')
                    ->where('pos_source_shiftdetails.shiftID', $masterModel["autoID"])
                    ->first();
                if ($segments) {
                    $data['serviceLineSystemID'] = $segments->segmentID;
                    $data['serviceLineCode'] = $segments->serviceLineCode;
                }
                $data['masterCompanyID'] = null;
                $data['documentSystemID'] = 111;
                $data['documentID'] = 'RPOS';
                $data['documentSystemCode'] = $masterModel["autoID"];
                $data['documentCode'] = $gl->documentCode;
                $data['documentDate'] = date('Y-m-d H:i:s');
                $data['documentYear'] = \Helper::dateYear(date('Y-m-d H:i:s'));
                $data['documentMonth'] = \Helper::dateMonth(date('Y-m-d H:i:s'));
                $data['createdDateTime'] = \Helper::currentDateTime();
                $data['createdUserID'] = $empID->empID;
                $data['createdUserSystemID'] = $empID->employeeSystemID;
                $data['createdUserPC'] = gethostname();
                $data['chartOfAccountSystemID'] = $gl->glCode;
            $glCodes = ChartOfAccount::find($data['chartOfAccountSystemID']);
            if (!empty($glCodes)) {
                    $glCode = $glCodes->AccountCode;
                    $data['glCode'] = $glCode;
            } else {
                return ['status' => false, 'message' => 'error chart of account not found', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
            }
                $data['glAccountType'] = ChartOfAccount::getGlAccountType($gl->glCode);
                $data['glAccountTypeID'] = ChartOfAccount::getGlAccountTypeID($gl->glCode);
            if (!empty($invItems)) {
                $data['documentTransCurrencyID'] = $invItems->companyLocalCurrencyID;
                $data['documentTransCurrencyER'] = $invItems->companyLocalExchangeRate;
                $data['documentTransAmount'] = $gl->totAmount;
                $data['documentLocalCurrencyID'] = $invItems->companyLocalCurrencyID;
                $data['documentLocalCurrencyER'] = $invItems->companyLocalExchangeRate;
                $data['documentLocalAmount'] = $gl->totAmount;
                $data['documentRptCurrencyID'] = $invItems->companyReportingCurrencyID;
                $data['documentRptCurrencyER'] = $invItems->companyReportingExchangeRate;
                $data['documentRptAmount'] = $gl->totAmount / $invItems->companyReportingExchangeRate;
                $data['documentNarration'] = "Bill No: ". $invItems->invoiceCode;
            }
            else {
                return ['status' => false, 'message' => 'error bill was not found', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
            }
                $data['timestamp'] = \Helper::currentDateTime();
                $data['supplierCodeSystem'] = null;
                array_push($finalData, $data);

        }
	
        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
	}
}

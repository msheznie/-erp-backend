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

class InventoryReclassificationGlService
{
	public static function processEntry($masterModel)
	{
        $data = [];
        $taxLedgerData = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
        $masterData = InventoryReclassification::find($masterModel["autoID"]);

        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

        $postedDateGl = isset($masterModel['documentDateOveride']) ? $masterModel['documentDateOveride'] : $validatePostedDate['postedDate'];

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

                $data['chartOfAccountSystemID'] = SystemGlCodeScenarioDetail::getGlByScenario($masterData->companySystemID, $masterData->documentSystemID, "inventory-reclassification-bs-account");
                $data['glCode'] = SystemGlCodeScenarioDetail::getGlCodeByScenario($masterData->companySystemID, $masterData->documentSystemID, "inventory-reclassification-bs-account");
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

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
    }
}

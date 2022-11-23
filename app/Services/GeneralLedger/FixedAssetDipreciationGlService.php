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

class FixedAssetDipreciationGlService
{
	public static function processEntry($masterModel)
	{
         $data = [];
        $taxLedgerData = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
        $masterData = FixedAssetDepreciationMaster::find($masterModel["autoID"]);

        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

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
                    $data['documentDate'] = $validatePostedDate['postedDate'];
                    $data['documentYear'] = \Helper::dateYear($validatePostedDate['postedDate']);
                    $data['documentMonth'] = \Helper::dateMonth($validatePostedDate['postedDate']);
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
                        $data['documentDate'] = $validatePostedDate['postedDate'];
                        $data['documentYear'] = \Helper::dateYear($validatePostedDate['postedDate']);
                        $data['documentMonth'] = \Helper::dateMonth($validatePostedDate['postedDate']);
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

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
    }
}

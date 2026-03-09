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
use App\helper\Helper;

class FixedAssetDisposalGlService
{
	public static function processEntry($masterModel)
	{
        $data = [];
        $taxLedgerData = [];
        $finalData = [];
        $empID = Employee::find($masterModel['employeeSystemID']);
        $masterData = AssetDisposalMaster::with(['disposal_type' => function ($query) {
            $query->with('chartofaccount');
        }])->find($masterModel["autoID"]);

        $validatePostedDate = GlPostedDateService::validatePostedDate($masterModel["autoID"], $masterModel["documentSystemID"]);

        if (!$validatePostedDate['status']) {
            return ['status' => false, 'message' => $validatePostedDate['message']];
        }

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
            $data['documentDate'] = $validatePostedDate['postedDate'];
            $data['documentYear'] = Helper::dateYear($validatePostedDate['postedDate']);
            $data['documentMonth'] = Helper::dateMonth($validatePostedDate['postedDate']);
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
            $data['createdDateTime'] = Helper::currentDateTime();
            $data['createdUserID'] = $empID->empID;
            $data['createdUserSystemID'] = $empID->employeeSystemID;
            $data['createdUserPC'] = gethostname();
            $data['timestamp'] = Helper::currentDateTime();

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

        return ['status' => true, 'message' => 'success', 'data' => ['finalData' => $finalData, 'taxLedgerData' => $taxLedgerData]];
    }
}
<?php

namespace App\Jobs;

use App\Models\BookInvSuppDet;
use App\Models\BookInvSuppMaster;
use App\Models\CreditNote;
use App\Models\CreditNoteDetails;
use App\Models\DebitNote;
use App\Models\DebitNoteDetails;
use App\Models\Employee;
use App\Models\GeneralLedger;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\InventoryReclassification;
use App\Models\InventoryReclassificationDetail;
use App\Models\ItemIssueDetails;
use App\Models\ItemIssueMaster;
use App\Models\ItemReturnDetails;
use App\Models\ItemReturnMaster;
use App\Models\PoAdvancePayment;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetails;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetails;
use App\Models\StockReceive;
use App\Models\StockReceiveDetails;
use App\Models\StockTransfer;
use App\Models\StockTransferDetails;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\Taxdetail;
use App\Models\Company;
use App\Models\chartOfAccount;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $masterModel = $this->masterModel;
        if (!empty($masterModel)) {
            DB::beginTransaction();
            try {
                $data = [];
                $finalData = [];
                $empID = Employee::find($masterModel['employeeSystemID']);
                switch ($masterModel["documentSystemID"]) {
                    case 3: // GRV
                        $masterData = GRVMaster::with(['details' => function ($query) {
                            $query->selectRaw("SUM(GRVcostPerUnitLocalCur*noQty) as localAmount, SUM(GRVcostPerUnitComRptCur*noQty) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,grvAutoID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER");
                        }])->find($masterModel["autoID"]);
                        //get balansheet account
                        $bs = GRVDetails::selectRaw("SUM(landingCost_LocalCur*noQty) as localAmount, SUM(landingCost_RptCur*noQty) as rptAmount,SUM(landingCost_TransCur*noQty) as transAmount,financeGLcodebBSSystemID,financeGLcodebBS,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER")->WHERE('grvAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->groupBy('financeGLcodebBSSystemID')->get();

                        //get pnl account
                        $pl = GRVDetails::selectRaw("SUM(landingCost_LocalCur*noQty) as localAmount, SUM(landingCost_RptCur*noQty) as rptAmount,SUM(landingCost_TransCur*noQty) as transAmount,financeGLcodePLSystemID,financeGLcodePL,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER")->WHERE('grvAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->WHERE('includePLForGRVYN', -1)->groupBy('financeGLcodePLSystemID')->get();

                        //unbilledGRV for logistic
                        $unbilledGRV = PoAdvancePayment::selectRaw("erp_grvmaster.companySystemID,erp_grvmaster.companyID,erp_purchaseorderadvpayment.supplierID,poID as purchaseOrderID,erp_purchaseorderadvpayment.grvAutoID,erp_grvmaster.grvDate,erp_purchaseorderadvpayment.currencyID as supplierTransactionCurrencyID,'1' as supplierTransactionER,erp_purchaseordermaster.companyReportingCurrencyID, ROUND((SUM(reqAmountTransCur_amount)/SUM(reqAmountInPORptCur)),7) as companyReportingER,erp_purchaseordermaster.localCurrencyID,ROUND((SUM(reqAmountTransCur_amount)/SUM(reqAmountInPOLocalCur)),7) as localCurrencyER,SUM(reqAmountTransCur_amount) as transAmount,SUM(reqAmountInPOLocalCur) as localAmount, SUM(reqAmountInPORptCur) as rptAmount,'POG' as grvType,NOW() as timeStamp,erp_purchaseorderadvpayment.UnbilledGRVAccountSystemID,erp_purchaseorderadvpayment.UnbilledGRVAccount")->leftJoin('erp_grvmaster', 'erp_purchaseorderadvpayment.grvAutoID', '=', 'erp_grvmaster.grvAutoID')->leftJoin('erp_purchaseordermaster', 'erp_purchaseorderadvpayment.poID', '=', 'erp_purchaseordermaster.purchaseOrderID')->where('erp_purchaseorderadvpayment.grvAutoID', $masterModel["autoID"])->groupBy('erp_purchaseorderadvpayment.UnbilledGRVAccountSystemID', 'erp_purchaseorderadvpayment.supplierID')->get();

                        if ($masterData) {
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
                            $data['glAccountType'] = 'BS';
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
                            $data['documentTransAmount'] = \Helper::roundValue($masterData->details[0]->transAmount * -1);
                            $data['documentLocalCurrencyID'] = $masterData->details[0]->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->details[0]->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue($masterData->details[0]->localAmount * -1);
                            $data['documentRptCurrencyID'] = $masterData->details[0]->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->details[0]->companyReportingER;
                            $data['documentRptAmount'] = \Helper::roundValue($masterData->details[0]->rptAmount * -1);
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);

                            if ($bs) {
                                foreach ($bs as $val) {
                                    $data['chartOfAccountSystemID'] = $val->financeGLcodebBSSystemID;
                                    $data['glCode'] = $val->financeGLcodebBS;
                                    $data['glAccountType'] = 'BS';
                                    $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                                    $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount));

                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount));

                                    $data['documentRptCurrencyID'] = $val->companyReportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $val->companyReportingER;
                                    $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount));
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }

                            if ($pl) {
                                foreach ($pl as $val) {
                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['glCode'] = $val->financeGLcodePL;
                                    $data['glAccountType'] = 'PL';
                                    $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                                    $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount));

                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount));

                                    $data['documentRptCurrencyID'] = $val->companyReportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $val->companyReportingER;
                                    $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount));
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }

                            if ($unbilledGRV) {
                                foreach ($unbilledGRV as $val) {
                                    $data['documentTransCurrencyID'] = $val->currencyID;
                                    $data['documentTransCurrencyID'] = 1;
                                    $data['supplierCodeSystem'] = $val->supplierID;
                                    $data['chartOfAccountSystemID'] = $val->UnbilledGRVAccountSystemID;
                                    $data['glCode'] = $val->UnbilledGRVAccount;
                                    $data['glAccountType'] = 'BS';
                                    $data['documentTransCurrencyID'] = $val->supplierTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $val->supplierTransactionER;
                                    $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount) * -1);

                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                    $data['documentLocalAmount'] = \Helper::roundValue(ABS($val->localAmount) * -1);

                                    $data['documentRptCurrencyID'] = $val->companyReportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $val->companyReportingER;
                                    $data['documentRptAmount'] = \Helper::roundValue(ABS($val->rptAmount) * -1);
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        break;
                    case 8: // MI - Material issue
                        $masterData = ItemIssueMaster::find($masterModel["autoID"]);
                        //get balansheet account
                        $bs = ItemIssueDetails::selectRaw("SUM(qtyIssuedDefaultMeasure * issueCostLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * issueCostRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,reportingCurrencyID")->WHERE('itemIssueAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->groupBy('financeGLcodebBSSystemID')->first();
                        //get pnl account
                        $pl = ItemIssueDetails::selectRaw("SUM(qtyIssuedDefaultMeasure * issueCostLocal) as localAmount, SUM(qtyIssuedDefaultMeasure * issueCostRpt) as rptAmount,financeGLcodePLSystemID,financeGLcodePL,localCurrencyID,reportingCurrencyID")->WHERE('itemIssueAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->groupBy('financeGLcodePLSystemID')->get();
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
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            if ($bs) {
                                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                $data['glCode'] = $bs->financeGLcodebBS;
                                $data['glAccountType'] = 'BS';
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
                                    $data['glAccountType'] = 'PL';
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
                        $bs = ItemReturnDetails::selectRaw("SUM(qtyIssuedDefaultMeasure* unitCostLocal) as localAmount, SUM(qtyIssuedDefaultMeasure* unitCostRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,reportingCurrencyID")->WHERE('itemReturnAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->groupBy('financeGLcodebBSSystemID')->first();
                        //get pnl account
                        $pl = ItemReturnDetails::selectRaw("SUM(qtyIssuedDefaultMeasure* unitCostLocal) as localAmount, SUM(qtyIssuedDefaultMeasure* unitCostRpt) as rptAmount,financeGLcodePLSystemID,financeGLcodePL,localCurrencyID,reportingCurrencyID")->WHERE('itemReturnAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->groupBy('financeGLcodePLSystemID')->get();
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
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            if ($bs) {
                                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                $data['glCode'] = $bs->financeGLcodebBS;
                                $data['glAccountType'] = 'BS';
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
                                    $data['glAccountType'] = 'PL';
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
                        $bs = StockTransferDetails::selectRaw("SUM(qty* unitCostLocal) as localAmount, SUM(qty* unitCostRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,reportingCurrencyID")->WHERE('stockTransferAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->groupBy('financeGLcodebBSSystemID')->first();
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
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            if ($bs) {
                                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                $data['glCode'] = $bs->financeGLcodebBS;
                                $data['glAccountType'] = 'BS';
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
                                if ($masterData->interCompanyTransferYN == -1) {
                                    $data['chartOfAccountSystemID'] = 747;
                                    $data['glCode'] = '20023';
                                } else {
                                    $data['chartOfAccountSystemID'] = 605;
                                    $data['glCode'] = '9999988';
                                }
                                $data['glAccountType'] = 'BS';
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
                        $bs = StockReceiveDetails::selectRaw("SUM(qty* unitCostLocal) as localAmount, SUM(qty* unitCostRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,reportingCurrencyID")->WHERE('stockReceiveAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->groupBy('financeGLcodebBSSystemID')->first();
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
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            if ($bs) {
                                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                $data['glCode'] = $bs->financeGLcodebBS;
                                $data['glAccountType'] = 'BS';
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
                                if ($masterData->interCompanyTransferYN == -1) {
                                    $data['chartOfAccountSystemID'] = 747;
                                    $data['glCode'] = '20023';
                                } else {
                                    $data['chartOfAccountSystemID'] = 605;
                                    $data['glCode'] = '9999988';
                                }
                                $data['glAccountType'] = 'BS';
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
                        $bs = InventoryReclassificationDetail::selectRaw("SUM(currentStockQty * unitCostLocal) as localAmount, SUM(currentStockQty * unitCostRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,reportingCurrencyID")->WHERE('inventoryreclassificationID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->groupBy('financeGLcodebBSSystemID')->first();
                        //get pnl account
                        $pl = InventoryReclassificationDetail::selectRaw("SUM(currentStockQty * unitCostLocal) as localAmount, SUM(currentStockQty * unitCostRpt) as rptAmount,financeGLcodePLSystemID,financeGLcodePL,localCurrencyID,reportingCurrencyID")->WHERE('inventoryreclassificationID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->groupBy('financeGLcodePLSystemID')->get();
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
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            if ($bs) {
                                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                $data['glCode'] = $bs->financeGLcodebBS;
                                $data['glAccountType'] = 'BS';
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
                                    $data['glAccountType'] = 'PL';
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
                    case 24: // PRN - Purchase Return
                        $masterData = PurchaseReturn::with(['details' => function ($query) {
                            $query->selectRaw("SUM(noQty * GRVcostPerUnitLocalCur) as localAmount, SUM(noQty * GRVcostPerUnitComRptCur) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,purhaseReturnAutoID");
                        }])->find($masterModel["autoID"]);
                        //get balansheet account
                        $bs = PurchaseReturnDetails::selectRaw("SUM(noQty * GRVcostPerUnitLocalCur) as localAmount, SUM(noQty * GRVcostPerUnitComRptCur) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,supplierTransactionCurrencyID,supplierTransactionER,companyReportingER,localCurrencyER")->WHERE('purhaseReturnAutoID', $masterModel["autoID"])->first();
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
                            $data['glAccountType'] = 'BS';
                            $data['supplierCodeSystem'] = $masterData->supplierID;;
                            $data['chartOfAccountSystemID'] = $masterData->liabilityAccountSysemID;
                            $data['glCode'] = $masterData->liabilityAccount;
                            $data['documentTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->supplierTransactionER;
                            $data['documentTransAmount'] = \Helper::roundValue($masterData->details[0]->transAmount);
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue($masterData->details[0]->localAmount);
                            $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                            $data['documentRptAmount'] = \Helper::roundValue($masterData->details[0]->rptAmount);
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);

                            if ($bs) {
                                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                $data['glCode'] = $bs->financeGLcodebBS;
                                $data['glAccountType'] = 'BS';
                                $data['documentTransCurrencyID'] = $bs->supplierTransactionCurrencyID;
                                $data['documentTransCurrencyER'] = $bs->supplierTransactionER;
                                $data['documentTransAmount'] = ABS($bs->transAmount) * -1;
                                $data['documentLocalCurrencyID'] = $bs->localCurrencyID;
                                $data['documentLocalCurrencyER'] = $bs->localCurrencyER;
                                $data['documentLocalAmount'] = ABS($bs->localAmount) * -1;
                                $data['documentRptCurrencyID'] = $bs->reportingCurrencyID;
                                $data['documentRptCurrencyER'] = $bs->companyReportingER;
                                $data['documentRptAmount'] = ABS($bs->rptAmount) * -1;
                                $data['timestamp'] = \Helper::currentDateTime();
                                array_push($finalData, $data);
                            }
                        }
                        break;
                    case 20:
                        /*customerInvoice*/
                        $masterData = CustomerInvoiceDirect::find($masterModel["autoID"]);
                        $det=CustomerInvoiceDirectDetail::with(['contract'])->where('custInvoiceDirectID',$masterModel["autoID"]);
                        $detOne = $det->first();
                        $det=CustomerInvoiceDirectDetail::where('custInvoiceDirectID',$masterModel["autoID"])->groupBy('glCode','serviceLineCode');
                        $detail = CustomerInvoiceDirectDetail::selectRaw("sum(comRptAmount) as comRptAmount, comRptCurrency, sum(localAmount) as localAmount , localCurrencyER, localCurrency, sum(invoiceAmount) as invoiceAmount, invoiceAmountCurrencyER, invoiceAmountCurrency,comRptCurrencyER, customerID, clientContractID, comments, glSystemID,   serviceLineSystemID,serviceLineCode")->WHERE('custInvoiceDirectID', $masterModel["autoID"])->groupBy('glCode','serviceLineCode','comments')->get();

                        $company = Company::select('masterComapanyID')->where('companySystemID',$masterData->companySystemID)->first();
                        $chartOfAccount = chartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL','chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->customerGLSystemID)->first();

                        $taxGL = chartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->vatOutputGLCodeSystemID)->first();

                        $date = new Carbon( $masterData->bookingDate );
                        $getEmployeeSystemID = \Helper::getEmployeeSystemID();
                        $time= Carbon::now();
                        $getEmployeeID=\Helper::getEmployeeID();

                        $data['companySystemID'] = $masterData->companySystemID;
                        $data['companyID'] = $masterData->companyID;
                        $data['masterCompanyID'] = $company->masterComapanyID;
                        $data['documentID'] = "INV";
                        $data['documentSystemID'] = $masterData->documentSystemiD;
                        $data['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                        $data['documentCode'] = $masterData->bookingInvCode;
                        $data['documentDate'] = $masterData->bookingDate;
                        $data['documentYear'] = $date->year;
                        $data['documentMonth'] = $date->month;
                        $data['invoiceNumber'] = $masterData->customerInvoiceNo;
                        $data['invoiceDate'] = $masterData->customerInvoiceDate;

                        $data['serviceLineSystemID'] = $detOne->serviceLineSystemID;
                        $data['serviceLineCode'] = $detOne->serviceLineCode;

                        // from customer invoice master table
                        $data['chartOfAccountSystemID'] = $chartOfAccount->chartOfAccountSystemID;
                        $data['glCode'] = $chartOfAccount->chartOfAccountSystemID;
                        $data['glAccountType'] = $chartOfAccount->catogaryBLorPL;
                        $data['documentConfirmedDate'] = $masterData->confirmedDate;
                        $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;


                        $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                        $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                        $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                        $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;

                        $data['documentNarration'] = $masterData->comments;
                        $data['clientContractID'] = $detOne->clientContractID;
                        $data['contractUID']=$detOne->contract->contractUID;
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

                        $data['documentType']=11;

                        $data['createdUserSystemID'] =$getEmployeeSystemID;
                        $data['createdDateTime'] =$time;
                        $data['createdUserID'] =$getEmployeeID;
                        $data['createdUserPC'] =getenv('COMPUTERNAME');
                        $data['timestamp'] =$time;
                        array_push($finalData, $data);

                        if(!empty($detail)){
                            foreach ($detail as $item){
                                $chartOfAccount = chartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL','chartOfAccountSystemID')->where('chartOfAccountSystemID', $item->glSystemID)->first();
                                $data['companySystemID'] = $masterData->companySystemID;
                                $data['companyID'] = $masterData->companyID;
                                $data['serviceLineSystemID'] = $item->serviceLineSystemID;
                                $data['serviceLineCode'] = $item->serviceLineCode;
                                $data['masterCompanyID'] = $company->masterComapanyID;
                                $data['documentSystemID'] = $masterData->documentSystemiD;
                                $data['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                                $data['documentCode'] = $masterData->bookingInvCode;
                                $data['documentDate'] = $masterData->bookingDate;
                                $data['documentYear'] = $date->year;
                                $data['documentMonth'] = $date->month;
                                $data['invoiceNumber'] = $masterData->customerInvoiceNo;
                                $data['invoiceDate'] = $masterData->customerInvoiceDate;

                                // from customer invoice master table
                                $data['chartOfAccountSystemID'] = $item->glSystemID;
                                $data['glCode'] = $chartOfAccount->AccountCode;
                                $data['glAccountType'] = $chartOfAccount->catogaryBLorPL;
                                $data['documentConfirmedDate'] = $masterData->confirmedDate;
                                $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;

                                $data['documentNarration'] = $item->comments;
                                $data['clientContractID'] = $item->clientContractID;
                                $data['supplierCodeSystem'] = $item->customerID;

                                $data['documentTransCurrencyID'] = $item->invoiceAmountCurrency;
                                $data['documentTransCurrencyER'] = $item->invoiceAmountCurrencyER;
                                $data['documentTransAmount'] = $item->invoiceAmount * -1;
                                $data['documentLocalCurrencyID'] = $item->localCurrency;

                                $data['documentLocalCurrencyER'] = $item->localCurrencyER;
                                $data['documentLocalAmount'] = $item->localAmount * -1;
                                $data['documentRptCurrencyID'] = $item->comRptCurrency;
                                $data['documentRptCurrencyER'] = $item->comRptCurrencyER;
                                $data['documentRptAmount'] = $item->comRptAmount * -1;
                                /*  $data['isCustomer'] = 1;*/
                                // $data['documentType'] = 11;
                                $data['createdUserSystemID'] =$getEmployeeSystemID;
                                $data['createdDateTime'] =$time;
                                $data['createdUserID'] =$getEmployeeID;
                                $data['createdUserPC'] =getenv('COMPUTERNAME');
                                array_push($finalData, $data);
                            }
                        }
                        $erp_taxdetail=  Taxdetail::where('companyID',$masterData->companyID)->where('documentSystemCode',$masterData->custInvoiceDirectAutoID)->get();

                        if (!empty($erp_taxdetail)) {
                            foreach ($erp_taxdetail as $tax) {

                                $data['companySystemID'] = $masterData->companySystemID;
                                $data['companyID'] = $masterData->companyID;
                                $data['serviceLineSystemID'] = $item->serviceLineSystemID;
                                $data['serviceLineCode'] = $item->serviceLineCode;
                                $data['masterCompanyID'] = $company->masterComapanyID;
                                $data['documentSystemID'] = $masterData->documentSystemiD;
                                $data['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                                $data['documentCode'] = $masterData->bookingInvCode;
                                $data['documentDate'] = $masterData->bookingDate;
                                $data['documentYear'] = $date->year;
                                $data['documentMonth'] = $date->month;
                                $data['invoiceNumber'] = $masterData->customerInvoiceNo;
                                $data['invoiceDate'] = $masterData->customerInvoiceDate;

                                // from customer invoice master table
                                $data['chartOfAccountSystemID'] = $taxGL['chartOfAccountSystemID'];
                                $data['glCode'] = $taxGL->AccountCode;
                                $data['glAccountType'] = $taxGL->catogaryBLorPL;
                                $data['documentConfirmedDate'] = $masterData->confirmedDate;
                                $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;

                                $data['documentNarration'] = $tax->taxDescription;
                                $data['clientContractID'] =  $detOne->clientContractID;
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
                                /*$data['isCustomer'] = 1;*/
                                // $data['documentType'] = 11;
                                $data['createdUserSystemID'] =$getEmployeeSystemID;
                                $data['createdDateTime'] =$time;
                                $data['createdUserID'] =$getEmployeeID;
                                $data['createdUserPC'] =getenv('COMPUTERNAME');
                                array_push($finalData, $data);
                            }
                        }
                        break;
                    case 7: // SA - Stock Adjustment
                        $masterData = StockAdjustment::find($masterModel["autoID"]);
                        //get balansheet account
                        $bs = StockAdjustmentDetails::selectRaw("SUM(noQty * wacAdjLocal) as localAmount, SUM(noQty * wacAdjRpt) as rptAmount,financeGLcodebBSSystemID,financeGLcodebBS,currentWacLocalCurrencyID as localCurrencyID,currentWacRptCurrencyID as reportingCurrencyID,wacAdjRptER as reportingCurrencyER,wacAdjLocalER as localCurrencyER")->WHERE('stockAdjustmentAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->groupBy('financeGLcodebBSSystemID')->first();
                        //get pnl account
                        $pl = StockAdjustmentDetails::selectRaw("SUM(noQty * wacAdjLocal) as localAmount, SUM(noQty * wacAdjRpt) as rptAmount,financeGLcodePLSystemID,financeGLcodePL,currentWacLocalCurrencyID as localCurrencyID,currentWacRptCurrencyID as reportingCurrencyID,wacAdjRptER as reportingCurrencyER,wacAdjLocalER as localCurrencyER")->WHERE('stockAdjustmentAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->groupBy('financeGLcodePLSystemID')->get();
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
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            if ($bs) {
                                $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                $data['glCode'] = $bs->financeGLcodebBS;
                                $data['glAccountType'] = 'BS';
                                $data['documentLocalCurrencyID'] = $bs->localCurrencyID;
                                $data['documentLocalCurrencyER'] = $bs->localCurrencyER;
                                $data['documentLocalAmount'] = ABS($bs->localAmount);
                                $data['documentRptCurrencyID'] = $bs->reportingCurrencyID;
                                $data['documentRptCurrencyER'] = $bs->reportingCurrencyER;
                                $data['documentRptAmount'] = ABS($bs->rptAmount);
                                $data['timestamp'] = \Helper::currentDateTime();
                                array_push($finalData, $data);
                            }

                            if ($pl) {
                                foreach ($pl as $val) {
                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['glCode'] = $val->financeGLcodePL;
                                    $data['glAccountType'] = 'PL';
                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                    $data['documentLocalAmount'] = ABS($val->localAmount) * -1;
                                    $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                                    $data['documentRptAmount'] = ABS($val->rptAmount) * -1;
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        break;
                    case 11: // SI - Supplier Invoice
                        $masterData = BookInvSuppMaster::with(['details' => function ($query) {
                            $query->selectRaw("SUM(totLocalAmount) as localAmount, SUM(totRptAmount) as rptAmount,SUM(totTransactionAmount) as transAmount,bookingSuppMasInvAutoID");
                        }])->find($masterModel["autoID"]);
                        //get balansheet account
                        $bs = BookInvSuppDet::selectRaw("SUM(totLocalAmount) as localAmount, SUM(totRptAmount) as rptAmount,SUM(totTransactionAmount) as transAmount,financeGLcodebBSSystemID,financeGLcodebBS,localCurrencyID,companyReportingCurrencyID as reportingCurrencyID,supplierTransactionCurrencyID,supplierTransactionCurrencyER as supplierTransactionER,companyReportingER,localCurrencyER")->WHERE('bookingSuppMasInvAutoID', $masterModel["autoID"])->first();
                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                            $data['serviceLineCode'] = $masterData->serviceLineCode;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->bookingInvCode;
                            $data['documentDate'] = date('Y-m-d H:i:s');
                            $data['documentYear'] = \Helper::dateYear($masterData->bookingDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterData->bookingDate);
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->comments;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['glAccountType'] = 'BS';
                            $data['supplierCodeSystem'] = $masterData->supplierID;;
                            $data['chartOfAccountSystemID'] = $masterData->supplierGLCodeSystemID;
                            $data['glCode'] = $masterData->supplierGLCode;
                            $data['documentTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->supplierTransactionER;
                            $data['documentTransAmount'] = \Helper::roundValue($masterData->details[0]->transAmount) * -1;
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue($masterData->details[0]->localAmount) * -1;
                            $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                            $data['documentRptAmount'] = \Helper::roundValue($masterData->details[0]->rptAmount) * -1;
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);

                            if ($masterData->documentType == 0) {
                                $data['chartOfAccountSystemID'] = $masterData->UnbilledGRVAccountSystemID;
                                $data['glCode'] = $masterData->UnbilledGRVAccount;
                                $data['documentTransAmount'] = \Helper::roundValue($masterData->details[0]->transAmount) * -1;
                                $data['documentLocalAmount'] = \Helper::roundValue($masterData->details[0]->localAmount) * -1;
                                $data['documentRptAmount'] = \Helper::roundValue($masterData->details[0]->rptAmount) * -1;
                            } else {
                                if ($bs) {
                                    $data['chartOfAccountSystemID'] = $bs->financeGLcodebBSSystemID;
                                    $data['glCode'] = $bs->financeGLcodebBS;
                                    $data['glAccountType'] = 'BS';
                                    $data['documentTransCurrencyID'] = $bs->supplierTransactionCurrencyID;
                                    $data['documentTransCurrencyER'] = $bs->supplierTransactionER;
                                    $data['documentTransAmount'] = ABS($bs->transAmount) * -1;
                                    $data['documentLocalCurrencyID'] = $bs->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $bs->localCurrencyER;
                                    $data['documentLocalAmount'] = ABS($bs->localAmount) * -1;
                                    $data['documentRptCurrencyID'] = $bs->reportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $bs->companyReportingER;
                                    $data['documentRptAmount'] = ABS($bs->rptAmount) * -1;
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        break;
                    case 15: // DN - Debit Note
                        $masterData = DebitNote::with(['detail' => function ($query) {
                            $query->selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(debitAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,debitAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,debitAmountCurrencyER as transCurrencyER,debitNoteAutoID");
                        }])->find($masterModel["autoID"]);

                        //get pnl account
                        $pl = DebitNoteDetails::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(debitAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,debitAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,debitAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode")->WHERE('debitNoteAutoID', $masterModel["autoID"])->whereNotNull('chartOfAccountSystemID')->groupBy('chartOfAccountSystemID')->get();

                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['serviceLineSystemID'] = null;
                            $data['serviceLineCode'] = null;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemID;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->debitNoteCode;
                            $data['documentDate'] = date('Y-m-d H:i:s');
                            $data['documentYear'] = \Helper::dateYear($masterData->debitNoteDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterData->debitNoteDate);
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
                            $data['chartOfAccountSystemID'] = $masterData->supplierGLCodeSystemID;
                            $data['glCode'] = $masterData->supplierGLCode;
                            $data['glAccountType'] = 'BS';

                            $data['documentTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->supplierTransactionCurrencyER;
                            $data['documentTransAmount'] = \Helper::roundValue($masterData->detail[0]->transAmount);
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue($masterData->detail[0]->localAmount);
                            $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                            $data['documentRptAmount'] = \Helper::roundValue($masterData->detail[0]->rptAmount);

                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['documentType'] = $masterData->documentType;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);

                            if ($pl) {
                                foreach ($pl as $val) {
                                    $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                    $data['serviceLineCode'] = $val->serviceLineCode;
                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['glCode'] = $val->financeGLcodePL;
                                    $data['glAccountType'] = 'PL';
                                    $data['documentTransCurrencyID'] = $val->transCurrencyID;
                                    $data['documentTransCurrencyER'] = $val->transCurrencyER;
                                    $data['documentTransAmount'] = \Helper::roundValue(ABS($val->transAmount)) * -1;
                                    $data['documentLocalCurrencyID'] = $val->localCurrencyID;
                                    $data['documentLocalCurrencyER'] = $val->localCurrencyER;
                                    $data['documentLocalAmount'] =  \Helper::roundValue(ABS($val->localAmount)) * -1;
                                    $data['documentRptCurrencyID'] = $val->reportingCurrencyID;
                                    $data['documentRptCurrencyER'] = $val->reportingCurrencyER;
                                    $data['documentRptAmount'] =  \Helper::roundValue(ABS($val->rptAmount)) * -1;
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }
                        }
                        break;
                    case 19: // CN - Credit Note
                        $masterData = CreditNote::with(['details' => function ($query) {
                            $query->selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(creditAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,creditAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,creditAmountCurrencyER as transCurrencyER,creditNoteAutoID");
                        }])->find($masterModel["autoID"]);

                        //get pnl account
                        $pl = CreditNoteDetails::selectRaw("SUM(localAmount) as localAmount, SUM(comRptAmount) as rptAmount,SUM(creditAmount) as transAmount,chartOfAccountSystemID as financeGLcodePLSystemID,glCode as financeGLcodePL,localCurrency as localCurrencyID,comRptCurrency as reportingCurrencyID,creditAmountCurrency as transCurrencyID,comRptCurrencyER as reportingCurrencyER,localCurrencyER,creditAmountCurrencyER as transCurrencyER,serviceLineSystemID,serviceLineCode")->WHERE('creditNoteAutoID', $masterModel["autoID"])->whereNotNull('chartOfAccountSystemID')->groupBy('chartOfAccountSystemID')->get();


                        if ($masterData) {
                            $data['companySystemID'] = $masterData->companySystemID;
                            $data['companyID'] = $masterData->companyID;
                            $data['serviceLineSystemID'] = null;
                            $data['serviceLineCode'] = null;
                            $data['masterCompanyID'] = null;
                            $data['documentSystemID'] = $masterData->documentSystemiD;
                            $data['documentID'] = $masterData->documentID;
                            $data['documentSystemCode'] = $masterModel["autoID"];
                            $data['documentCode'] = $masterData->creditNoteCode;
                            $data['documentDate'] = date('Y-m-d H:i:s');
                            $data['documentYear'] = \Helper::dateYear($masterData->creditNoteDate);
                            $data['documentMonth'] = \Helper::dateMonth($masterData->creditNoteDate);
                            $data['documentConfirmedDate'] = $masterData->confirmedDate;
                            $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                            $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                            $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                            $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                            $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                            $data['documentNarration'] = $masterData->comments;
                            $data['clientContractID'] = 'X';
                            $data['contractUID'] = 159;
                            $data['supplierCodeSystem'] = $masterData->customerID;
                            $data['chartOfAccountSystemID'] = $masterData->customerGLCodeSystemID;
                            $data['glCode'] = $masterData->customerGLCode;
                            $data['glAccountType'] = 'BS';

                            $data['documentTransCurrencyID'] = $masterData->customerCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->customerCurrencyER;
                            $data['documentTransAmount'] = \Helper::roundValue($masterData->details[0]->transAmount) * -1;
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = \Helper::roundValue($masterData->details[0]->localAmount) * -1;
                            $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                            $data['documentRptAmount'] = \Helper::roundValue($masterData->details[0]->rptAmount) * -1;

                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['documentType'] = $masterData->documentType;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->empID;
                            $data['createdUserSystemID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();

                            array_push($finalData, $data);
                            if ($pl) {
                                foreach ($pl as $val) {
                                    $data['serviceLineSystemID'] = $val->serviceLineSystemID;
                                    $data['serviceLineCode'] = $val->serviceLineCode;
                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['glCode'] = $val->financeGLcodePL;
                                    $data['glAccountType'] = 'PL';
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
                        }
                        break;
                    default:
                        Log::warning('Document ID not found ' . date('H:i:s'));
                }

                if ($finalData) {
                    Log::info($finalData);
                    $generalLedgerInsert = GeneralLedger::insert($finalData);
                    Log::info('Successfully inserted to GL table ' . date('H:i:s'));
                    DB::commit();
                }

            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
            }
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

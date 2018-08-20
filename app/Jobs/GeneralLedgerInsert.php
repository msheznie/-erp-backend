<?php

namespace App\Jobs;

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
                        $unbilledGRV = PoAdvancePayment::selectRaw("erp_grvmaster.companySystemID,erp_grvmaster.companyID,erp_purchaseorderadvpayment.supplierID,poID as purchaseOrderID,erp_purchaseorderadvpayment.grvAutoID,erp_grvmaster.grvDate,erp_purchaseorderadvpayment.currencyID as supplierTransactionCurrencyID,'1' as supplierTransactionER,erp_purchaseordermaster.companyReportingCurrencyID, ROUND((SUM(reqAmountTransCur_amount)/SUM(reqAmountInPORptCur)),7) as companyReportingER,erp_purchaseordermaster.localCurrencyID,ROUND((SUM(reqAmountTransCur_amount)/SUM(reqAmountInPOLocalCur)),7) as localCurrencyER,SUM(reqAmountTransCur_amount) as transAmount,SUM(reqAmountInPOLocalCur) as localAmount, SUM(reqAmountInPORptCur) as rptAmount,'POG' as grvType,NOW() as timeStamp,erp_purchaseorderadvpayment.UnbilledGRVAccountSystemID,erp_purchaseorderadvpayment.UnbilledGRVAccount")->leftJoin('erp_grvmaster', 'erp_purchaseorderadvpayment.grvAutoID', '=', 'erp_grvmaster.grvAutoID')->leftJoin('erp_purchaseordermaster', 'erp_purchaseorderadvpayment.poID', '=', 'erp_purchaseordermaster.purchaseOrderID')->where('erp_purchaseorderadvpayment.grvAutoID',$masterModel["autoID"])->groupBy('erp_purchaseorderadvpayment.UnbilledGRVAccountSystemID','erp_purchaseorderadvpayment.supplierID')->get();

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
                                if($masterData->interCompanyTransferYN == -1){
                                    $data['chartOfAccountSystemID'] = 747;
                                    $data['glCode'] = '20023';
                                }else{
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
                                if($masterData->interCompanyTransferYN == -1){
                                    $data['chartOfAccountSystemID'] = 747;
                                    $data['glCode'] = '20023';
                                }else{
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
                    case 20:
                        /*customerInvoice*/
                        $masterData = CustomerInvoiceDirect::find($masterModel["autoID"]);
                        $det=CustomerInvoiceDirectDetail::where('custInvoiceDirectID',$masterModel["autoID"]);
                        $detOne = $det->first();
                        $detail = $det->get();
                        $company = Company::select('masterComapanyID')->where('companySystemID',$masterData->companySystemID)->first();
                        $chartOfAccount = chartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL','chartOfAccountSystemID')->where('chartOfAccountSystemID', $detOne->glSystemID)->first();
                        $taxGL = chartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL','chartOfAccountSystemID')->where('chartOfAccountSystemID', $masterData->vatOutputGLCodeSystemID)->first();
                        $date = new Carbon( $masterData->bookingDate );
                        $getEmployeeSystemID = \Helper::getEmployeeSystemID();
                       $time= Carbon::now();
                       $getEmployeeID=\Helper::getEmployeeID();

                        $generalfi['companySystemID'] = $masterData->companySystemID;
                        $generalfi['companyID'] = $masterData->companyID;
                        $generalfi['serviceLineSystemID'] = $detOne->serviceLineSystemID;
                        $generalfi['serviceLineCode'] = $detOne->serviceLineCode;
                        $generalfi['masterCompanyID'] = $company->masterComapanyID;
                        $generalfi['documentID'] = "INV";
                        $generalfi['documentSystemID'] = $masterData->documentSystemiD;
                        $generalfi['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                        $generalfi['documentCode'] = $masterData->bookingInvCode;
                        $generalfi['documentDate'] = $masterData->bookingDate;
                        $generalfi['documentYear'] = $date->year;
                        $generalfi['documentMonth'] = $date->month;
                        $generalfi['invoiceNumber'] = $masterData->customerInvoiceNo;
                        $generalfi['invoiceDate'] = $masterData->customerInvoiceDate;

                        // from customer invoice master table
                        $generalfi['chartOfAccountSystemID'] = $detOne->glSystemID;
                        $generalfi['glCode'] = $chartOfAccount->AccountCode;
                        $generalfi['glAccountType'] = $chartOfAccount->catogaryBLorPL;
                        $generalfi['documentConfirmedDate'] = $masterData->confirmedDate;
                        $generalfi['documentConfirmedBy'] = $masterData->confirmedByEmpID;

                        $generalfi['documentNarration'] = $masterData->comments;
                        $generalfi['clientContractID'] = $detOne->clientContractID;
                        $generalfi['supplierCodeSystem'] = $masterData->customerID;

                        $generalfi['documentTransCurrencyID'] = $masterData->custTransactionCurrencyID;
                        $generalfi['documentTransCurrencyER'] = $masterData->custTransactionCurrencyER;
                        $generalfi['documentTransAmount'] = $masterData->bookingAmountTrans + $masterData->VATAmount;
                        $generalfi['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                        $generalfi['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                        $generalfi['documentLocalAmount'] = $masterData->bookingAmountLocal + $masterData->VATAmountLocal;
                        $generalfi['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                        $generalfi['documentRptCurrencyER'] = $masterData->companyReportingER;
                        $generalfi['documentRptAmount'] = $masterData->bookingAmountRpt + $masterData->VATAmountRpt;

                        /*$generalfi['isCustomer'] = 1;*/
                       // $generalfi['documentType'] = 11;
                        $generalfi['createdUserSystemID'] =$getEmployeeSystemID;
                        $generalfi['createdDateTime'] =$time;
                        $generalfi['createdUserID'] =$getEmployeeID;
                        $generalfi['createdUserPC'] =getenv('COMPUTERNAME');

                        array_push($finalData, $generalfi);

                        if(!empty($detail)){
                            foreach ($detail as $item){
                                $generalfi['companySystemID'] = $masterData->companySystemID;
                                $generalfi['companyID'] = $masterData->companyID;
                                $generalfi['serviceLineSystemID'] = $item->serviceLineSystemID;
                                $generalfi['serviceLineCode'] = $item->serviceLineCode;
                                $generalfi['masterCompanyID'] = $company->masterComapanyID;
                                $generalfi['documentSystemID'] = $masterData->documentSystemiD;
                                $generalfi['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                                $generalfi['documentCode'] = $masterData->bookingInvCode;
                                $generalfi['documentDate'] = $masterData->bookingDate;
                                $generalfi['documentYear'] = $date->year;
                                $generalfi['documentMonth'] = $date->month;
                                $generalfi['invoiceNumber'] = $masterData->customerInvoiceNo;
                                $generalfi['invoiceDate'] = $masterData->customerInvoiceDate;

                                // from customer invoice master table
                                $generalfi['chartOfAccountSystemID'] = $chartOfAccount->glSystemID;
                                $generalfi['glCode'] = $chartOfAccount->AccountCode;
                                $generalfi['glAccountType'] = $chartOfAccount->catogaryBLorPL;
                                $generalfi['documentConfirmedDate'] = $masterData->confirmedDate;
                                $generalfi['documentConfirmedBy'] = $masterData->confirmedByEmpID;

                                $generalfi['documentNarration'] = $item->comments;
                                $generalfi['clientContractID'] = $item->clientContractID;
                                $generalfi['supplierCodeSystem'] = $item->customerID;

                                $generalfi['documentTransCurrencyID'] = $item->invoiceAmountCurrency;
                                $generalfi['documentTransCurrencyER'] = $item->invoiceAmountCurrencyER;
                                $generalfi['documentTransAmount'] = $item->invoiceAmount * -1;
                                $generalfi['documentLocalCurrencyID'] = $item->localCurrency;
                                $generalfi['documentLocalCurrencyER'] = $item->localCurrencyER;
                                $generalfi['documentLocalAmount'] = $item->localAmount * -1;
                                $generalfi['documentRptCurrencyID'] = $item->comRptCurrency;
                                $generalfi['documentRptCurrencyER'] = $item->comRptCurrencyER;
                                $generalfi['documentRptAmount'] = $item->comRptAmount * -1;

                              /*  $generalfi['isCustomer'] = 1;*/
                               // $generalfi['documentType'] = 11;
                                $generalfi['createdUserSystemID'] =$getEmployeeSystemID;
                                $generalfi['createdDateTime'] =$time;
                                $generalfi['createdUserID'] =$getEmployeeID;
                                $generalfi['createdUserPC'] =getenv('COMPUTERNAME');
                                array_push($finalData, $generalfi);
                            }
                        }
                        $erp_taxdetail=  Taxdetail::where('companyID',$masterData->companyID)->where('documentSystemCode',$masterData->custInvoiceDirectAutoID)->get();

                        if (!empty($erp_taxdetail)) {
                            foreach ($erp_taxdetail as $tax) {

                                $generalfi['companySystemID'] = $masterData->companySystemID;
                                $generalfi['companyID'] = $masterData->companyID;
                                $generalfi['serviceLineSystemID'] = $item->serviceLineSystemID;
                                $generalfi['serviceLineCode'] = $item->serviceLineCode;
                                $generalfi['masterCompanyID'] = $company->masterComapanyID;
                                $generalfi['documentSystemID'] = $masterData->documentSystemiD;
                                $generalfi['documentSystemCode'] = $masterData->custInvoiceDirectAutoID;
                                $generalfi['documentCode'] = $masterData->bookingInvCode;
                                $generalfi['documentDate'] = $masterData->bookingDate;
                                $generalfi['documentYear'] = $date->year;
                                $generalfi['documentMonth'] = $date->month;
                                $generalfi['invoiceNumber'] = $masterData->customerInvoiceNo;
                                $generalfi['invoiceDate'] = $masterData->customerInvoiceDate;

                                // from customer invoice master table
                                $generalfi['chartOfAccountSystemID'] = $taxGL['chartOfAccountSystemID'];
                                $generalfi['glCode'] = $taxGL->AccountCode;
                                $generalfi['glAccountType'] = $taxGL->catogaryBLorPL;
                                $generalfi['documentConfirmedDate'] = $masterData->confirmedDate;
                                $generalfi['documentConfirmedBy'] = $masterData->confirmedByEmpID;

                                $generalfi['documentNarration'] = $tax->taxDescription;
                                $generalfi['clientContractID'] =  $detOne->clientContractID;
                                $generalfi['supplierCodeSystem'] = $masterData->customerID;

                                $generalfi['documentTransCurrencyID'] = $tax->currency;
                                $generalfi['documentTransCurrencyER'] = $tax->currencyER;
                                $generalfi['documentTransAmount'] = $tax->amount * -1;
                                $generalfi['documentLocalCurrencyID'] = $tax->localCurrencyID;
                                $generalfi['documentLocalCurrencyER'] = $tax->localCurrencyER;
                                $generalfi['documentLocalAmount'] = $tax->localAmount * -1;
                                $generalfi['documentRptCurrencyID'] = $tax->rptCurrencyID;
                                $generalfi['documentRptCurrencyER'] = $tax->rptCurrencyER;
                                $generalfi['documentRptAmount'] = $tax->rptAmount * -1;

                                /*$generalfi['isCustomer'] = 1;*/
                               // $generalfi['documentType'] = 11;
                                $generalfi['createdUserSystemID'] =$getEmployeeSystemID;
                                $generalfi['createdDateTime'] =$time;
                                $generalfi['createdUserID'] =$getEmployeeID;
                                $generalfi['createdUserPC'] =getenv('COMPUTERNAME');
                                array_push($finalData, $generalfi);
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

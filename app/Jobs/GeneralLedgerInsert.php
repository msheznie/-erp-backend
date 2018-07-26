<?php

namespace App\Jobs;

use App\Models\Employee;
use App\Models\GeneralLedger;
use App\Models\GRVDetails;
use App\Models\GRVMaster;
use App\Models\ItemIssueDetails;
use App\Models\ItemIssueMaster;
use App\Models\ItemReturnDetails;
use App\Models\ItemReturnMaster;
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
        Log::useFiles(storage_path().'/logs/general_ledger_jobs.log');
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
                            $query->selectRaw("SUM(landingCost_LocalCur*noQty) as localAmount, SUM(landingCost_RptCur*noQty) as rptAmount,SUM(landingCost_TransCur*noQty) as transAmount,grvAutoID");
                        }])->find($masterModel["autoID"]);
                        //get balansheet account
                        $bs = GRVDetails::selectRaw("SUM(landingCost_LocalCur*noQty) as localAmount, SUM(landingCost_RptCur*noQty) as rptAmount,SUM(landingCost_TransCur*noQty) as transAmount,financeGLcodebBSSystemID,financeGLcodebBS")->WHERE('grvAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodebBSSystemID')->groupBy('financeGLcodebBSSystemID')->get();
                        //get pnl account
                        $pl = GRVDetails::selectRaw("SUM(landingCost_LocalCur*noQty) as localAmount, SUM(landingCost_RptCur*noQty) as rptAmount,SUM(landingCost_TransCur*noQty) as transAmount,financeGLcodePLSystemID,financeGLcodePL")->WHERE('grvAutoID', $masterModel["autoID"])->whereNotNull('financeGLcodePLSystemID')->WHERE('includePLForGRVYN', -1)->groupBy('financeGLcodePLSystemID')->get();
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
                            $data['supplierCodeSystem'] = $masterData->supplierID;
                            $data['documentTransCurrencyID'] = $masterData->supplierTransactionCurrencyID;
                            $data['documentTransCurrencyER'] = $masterData->supplierTransactionER;
                            $data['documentTransAmount'] = $masterData->details[0]->transAmount * -1;
                            $data['documentLocalCurrencyID'] = $masterData->localCurrencyID;
                            $data['documentLocalCurrencyER'] = $masterData->localCurrencyER;
                            $data['documentLocalAmount'] = $masterData->details[0]->localAmount * -1;
                            $data['documentRptCurrencyID'] = $masterData->companyReportingCurrencyID;
                            $data['documentRptCurrencyER'] = $masterData->companyReportingER;
                            $data['documentRptAmount'] = $masterData->details[0]->rptAmount * -1;
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->employeeSystemID;
                            $data['createdUserPC'] = gethostname();
                            $data['timestamp'] = \Helper::currentDateTime();
                            array_push($finalData, $data);

                            if ($bs) {
                                foreach ($bs as $val) {
                                    $data['chartOfAccountSystemID'] = $val->financeGLcodebBSSystemID;
                                    $data['glCode'] = $val->financeGLcodebBS;
                                    $data['glAccountType'] = 'BS';
                                    $data['documentTransAmount'] = ABS($val->transAmount);
                                    $data['documentLocalAmount'] = ABS($val->localAmount);
                                    $data['documentRptAmount'] = ABS($val->rptAmount);
                                    $data['timestamp'] = \Helper::currentDateTime();
                                    array_push($finalData, $data);
                                }
                            }

                            if ($pl) {
                                foreach ($pl as $val) {
                                    $data['chartOfAccountSystemID'] = $val->financeGLcodePLSystemID;
                                    $data['glCode'] = $val->financeGLcodePL;
                                    $data['glAccountType'] = 'PL';
                                    $data['documentTransAmount'] = ABS($val->transAmount);
                                    $data['documentLocalAmount'] = ABS($val->localAmount);
                                    $data['documentRptAmount'] = ABS($val->rptAmount);
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
                            $data['clientContractID'] = $masterData->contractID;
                            $data['supplierCodeSystem'] = null;
                            $data['documentTransCurrencyID'] = 0;
                            $data['documentTransCurrencyER'] = 0;
                            $data['documentTransAmount'] = 0;
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->employeeSystemID;
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
                            $data['supplierCodeSystem'] = null;
                            $data['documentTransCurrencyID'] = 0;
                            $data['documentTransCurrencyER'] = 0;
                            $data['documentTransAmount'] = 0;
                            $data['holdingShareholder'] = null;
                            $data['holdingPercentage'] = null;
                            $data['nonHoldingPercentage'] = null;
                            $data['createdDateTime'] = \Helper::currentDateTime();
                            $data['createdUserID'] = $empID->employeeSystemID;
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
                    default:
                        Log::warning('Document ID not found ' . date('H:i:s'));
                }

                if ($data) {
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

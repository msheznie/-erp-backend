<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;
use App\Models\ChartOfAccount;
use App\Models\ConsoleJVDetail;
use App\Models\EliminationLedger;
use App\Models\ConsoleJVMaster;
use Carbon\Carbon;


class EliminationLedgerInsert implements ShouldQueue
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
        Log::useFiles(storage_path() . '/logs/elimination_ledger_jobs.log');
        $masterModel = $this->masterModel;

        if (!empty($masterModel)) {
            DB::beginTransaction();
            try {
                $data = [];
                $taxLedgerData = [];
                $finalData = [];
                $empID = Employee::find($masterModel['employeeSystemID']);

                $masterData = ConsoleJVMaster::with(['details' => function ($query) {
                            $query->selectRaw('SUM(debitAmount) as debitAmountTot, SUM(creditAmount) as creditAmountTot,consoleJvMasterAutoId');
                        }], 'company')->find($masterModel["autoID"]);

                $detailRecords = ConsoleJVDetail::selectRaw("sum(debitAmount) as debitAmountTot, sum(creditAmount) as creditAmountTot, comments, glAccountSystemID, serviceLineSystemID,serviceLineCode,currencyID,currencyER, companySystemID, companyID")->WHERE('consoleJvMasterAutoId', $masterModel["autoID"])->groupBy('glAccountSystemID', 'serviceLineSystemID', 'comments', 'companySystemID')->get();

                $masterDocumentDate = date('Y-m-d H:i:s');
                if ($masterData) {
                    $masterDocumentDate = $masterData->consoleJVdate;
                }

                $time = Carbon::now();

                if (!empty($detailRecords)) {
                    foreach ($detailRecords as $item) {
                        $chartOfAccount = ChartOfAccount::select('AccountCode', 'AccountDescription', 'catogaryBLorPL', 'catogaryBLorPLID', 'chartOfAccountSystemID')->where('chartOfAccountSystemID', $item->glAccountSystemID)->first();

                        $data['companySystemID'] = $item->companySystemID;
                        $data['companyID'] = $item->companyID;
                        $data['serviceLineSystemID'] = $item->serviceLineSystemID;
                        $data['serviceLineCode'] = $item->serviceLineCode;
                        $data['masterCompanyID'] = $masterData->companyID;
                        $data['documentSystemID'] = $masterData->documentSystemID;
                        $data['documentID'] = $masterData->documentID;
                        $data['documentSystemCode'] = $masterData->consoleJvMasterAutoId;
                        $data['documentCode'] = $masterData->consoleJVcode;
                        $data['documentDate'] = $masterDocumentDate;
                        $data['documentYear'] = \Helper::dateYear($masterDocumentDate);
                        $data['documentMonth'] = \Helper::dateMonth($masterDocumentDate);

                        // from customer invoice master table
                        $data['chartOfAccountSystemID'] = $item->glAccountSystemID;
                        $data['glCode'] = $chartOfAccount->AccountCode;
                        $data['glAccountType'] = $chartOfAccount->catogaryBLorPL;
                        $data['glAccountTypeID'] = $chartOfAccount->catogaryBLorPLID;
                        $data['documentConfirmedDate'] = $masterData->confirmedDate;
                        $data['documentConfirmedBy'] = $masterData->confirmedByEmpID;
                        $data['documentConfirmedByEmpSystemID'] = $masterData->confirmedByEmpSystemID;
                        $data['documentFinalApprovedDate'] = $masterData->approvedDate;
                        $data['documentFinalApprovedBy'] = $masterData->approvedByUserID;
                        $data['documentFinalApprovedByEmpSystemID'] = $masterData->approvedByUserSystemID;
                        $data['documentNarration'] = $item->comments;
                        $data['clientContractID'] = 'X';
                        $data['contractUID'] = 159;

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


                    if ($finalData) {
                        foreach ($finalData as $data) {
                            EliminationLedger::create($data);
                        }

                        DB::commit();
                    }
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

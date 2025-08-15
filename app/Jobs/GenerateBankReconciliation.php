<?php

namespace App\Jobs;

use App\Models\BankReconciliation;
use App\Models\BankReconciliationDocuments;
use App\Services\UserTypeService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Http\Controllers\API\BankReconciliationAPIController;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BankStatementMaster;
use App\Http\Requests\API\CreateBankReconciliationAPIRequest;
use App\Models\BankStatementDetail;
use App\Models\BankLedger;
use App\Models\GeneralLedger;



class GenerateBankReconciliation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $db;
    protected $data;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($db, $data)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
        $this->db = $db;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;
        $db = $this->db;
        CommonJobService::db_switch($db);
        Log::useFiles(storage_path().'/logs/generate_bank_reconciliation.log');

        DB::beginTransaction();
        try {
            $statementId = $data['statementId'];
            $includePartialMatch = $data['includePartialMatch'];
            $employee = UserTypeService::getSystemEmployee();

            $bankStatement = BankStatementMaster::where('statementId', $statementId)->first();
            if(!$bankStatement){
                DB::rollBack();
                Log::error('Bank statement not found');
                BankStatementMaster::where('statementId', $statementId)
                    ->update([
                        'generateBankRec' => 0
                    ]);
                return;
            }
            /** creation of bank reconciliation master */
            $recMaster['bankAccountAutoID'] = $bankStatement->bankAccountAutoID;
            $recMaster['bankRecAsOf'] = $bankStatement->statementEndDate;
            $recMaster['description'] = 'Auto bank reconciliation';
            $recMaster['isAutoCreateDocument'] = 1;

            
            $request = new CreateBankReconciliationAPIRequest($recMaster);
            $controller = app(BankReconciliationAPIController::class);
            $response = $controller->store($request);
            $responseData = json_decode($response->getContent(), true);
            if(!$responseData['success']){
                DB::rollBack();
                Log::error($responseData['message']);
                BankStatementMaster::where('statementId', $statementId)
                    ->update([
                        'generateBankRec' => 0
                    ]);
                return;
            }
            $bankRecAutoID = $responseData['data']['bankRecAutoID'];
            $bankReconciliation = BankReconciliation::where('bankRecAutoID', $bankRecAutoID)->first();

             $statementData['bankRecAutoID'] = $responseData['data']['bankRecAutoID'];
             $statementData['bankRecCode'] = $responseData['data']['bankRecPrimaryCode'];

             $ledgerIds = BankStatementDetail::with('bankLedger')
                                             ->where('statementId', $statementId)
                                             ->whereNotNull('bankLedgerAutoID')
                                             ->when(!$includePartialMatch, function ($query) {
                                                 $query->where('matchType', 1);
                                             })
                                             ->whereHas('bankLedger')
                                             ->pluck('bankLedgerAutoID');

             if($ledgerIds->count() > 0) {
                 foreach($ledgerIds as $ledgerId) {
                     $bankLedger = BankLedger::with(['bank_account', 'reporting_currency'])->where('bankLedgerAutoID', $ledgerId)->first();

                     $checkGLAmount = GeneralLedger::selectRaw('SUM(documentRptAmount) as documentRptAmount, reportingCurrency.DecimalPlaces as DecimalPlaces')
                         ->join('currencymaster as reportingCurrency', 'reportingCurrency.currencyID', '=', 'documentRptCurrencyID')
                         ->where('companySystemID', $bankLedger->companySystemID)
                         ->where('documentSystemID', $bankLedger->documentSystemID)
                         ->where('documentSystemCode', $bankLedger->documentSystemCode)
                         ->when($bankLedger->pdcID > 0, function($query) use ($bankLedger) {
                             $query->where('pdcID', $bankLedger->pdcID);
                         })
                         ->where('chartOfAccountSystemID', $bankLedger->bank_account->chartOfAccountSystemID)
                         ->first();

                     if (!empty($checkGLAmount)) {
                         $glAmount = $checkGLAmount->documentRptAmount;
                         $a = abs(round($bankLedger->payAmountCompRpt, $bankLedger->reporting_currency->DecimalPlaces));
                         $b = abs(round($glAmount,$checkGLAmount->DecimalPlaces));
                         $epsilon = 0.00001;
                         if ((abs($a-$b) > $epsilon)) {
                             DB::rollBack();
                             Log::error('Bank amount is not matching with GL amount for document ' . $bankLedger->documentSystemCode);
                             BankStatementMaster::where('statementId', $statementId)
                                 ->update([
                                     'generateBankRec' => 0
                                 ]);
                             return;
                         }
                     } else {
                         DB::rollBack();
                         Log::error('GL data cannot be found for document ' . $bankLedger->documentSystemCode);
                         BankStatementMaster::where('statementId', $statementId)
                             ->update([
                                 'generateBankRec' => 0
                             ]);
                         return;
                     }

                     $updateArray['bankClearedYN'] = -1;
                     $updateArray['bankClearedAmount'] = $bankLedger->payAmountBank;
                     $updateArray['bankClearedByEmpName'] = $employee->empName;
                     $updateArray['bankClearedByEmpID'] = $employee->empID;
                     $updateArray['bankClearedByEmpSystemID'] = $employee->employeeSystemID;
                     $updateArray['bankClearedDate'] = now();
                     $updateArray['bankRecAutoID'] = $bankRecAutoID;
                     $updateArray['bankreconciliationDate'] = $bankReconciliation->bankRecAsOf;
                     $updateArray['bankRecYear'] = $bankReconciliation->year;
                     $updateArray['bankrecMonth'] = $bankReconciliation->month;

                     BankLedger::where('bankLedgerAutoID', $ledgerId)->update($updateArray);
                 }

                 $bankRecReceiptAmount = BankLedger::where('bankRecAutoID', $bankRecAutoID)
                     ->where('bankClearedYN', -1)
                     ->where('payAmountBank', '<', 0)
                     ->sum('bankClearedAmount');

                 $bankRecPaymentAmount = BankLedger::where('bankRecAutoID', $bankRecAutoID)
                     ->where('bankClearedYN', -1)
                     ->where('payAmountBank', '>', 0)
                     ->sum('bankClearedAmount');

                 $closingAmount = $bankReconciliation->openingBalance + ($bankRecReceiptAmount * -1) - $bankRecPaymentAmount;
                 $inputNew = array('closingBalance' => $closingAmount);
                 BankReconciliation::where('bankRecAutoID', $bankRecAutoID)->update($inputNew);
             }
             BankReconciliationDocuments::where('statementId', $statementId)->update(['bankRecAutoID' => $bankRecAutoID]);

             $statementData['generateBankRec'] = 2;
             $statementData['documentStatus'] = 2;
             BankStatementMaster::where('statementId', $statementId)->update($statementData);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error($exception->getMessage());
            BankStatementMaster::where('statementId', $statementId)
                ->update([
                    'generateBankRec' => 0
                ]);
        }        
    }
}

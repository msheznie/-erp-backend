<?php

namespace App\Jobs\Report;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use File;
use App\Jobs\Report\GenerateBankLedgerPdf;
use App\Services\BankLedger\BankLedgerService;

class BankLedgerPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $requestData;
    public $userIds;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $request, $userId)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->requestData = $request;
        $this->userIds = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('max_execution_time', config('app.report_max_execution_limit'));
        ini_set('memory_limit', -1);
        Log::useFiles(storage_path() . '/logs/bank-ledger-pdf.log'); 
        $request = $this->requestData;
        $db = $this->dispatch_db;
        CommonJobService::db_switch($db);

        $currentDate = strtotime(date("Y-m-d H:i:s"));
        $root = "bank-ledger-pdf/".$currentDate;

        $output = BankLedgerService::getBankLedgerData($request);


        $outputChunkData = collect($output)->chunk(300);

        $reportCount = 1;

        foreach ($outputChunkData as $key1 => $output1) {

            if (count($request->accounts) == 1) {
                foreach ($output1 as $key => $value) {
                    $value->accountBalance = $this->calculateAccountBalance($output1, $key, $request->currencyID);
                }
            }
            
            GenerateBankLedgerPdf::dispatch($db, $request, $reportCount, $this->userIds, $output1, count($outputChunkData), $root)->onQueue('single');
            $reportCount++;
        }
    }


    public function calculateAccountBalance($data, $index, $currencyID)
    {
        $balance = 0;

        foreach ($data as $key => $value) {
            if ($key <= $index) {
                if ($currencyID == 1) {
                    $balance += $value->bankDebit - $value->bankCredit;
                } else if ($currencyID == 2) {
                    $balance += $value->rptDebit - $value->rptCredit;
                } else if ($currencyID == 3) {
                    $balance += $value->localDebit - $value->localCredit;
                }
            }
        }

        return $balance;

    }
}

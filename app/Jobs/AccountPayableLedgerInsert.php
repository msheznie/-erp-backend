<?php

namespace App\Jobs;

use App\Services\AccountPayableLedgerService;
use App\Services\JobErrorLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\helper\CommonJobService;

class AccountPayableLedgerInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $masterModel;
    protected $dataBase;
    private $tag = "account-payable-ledger";
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($masterModel, $dataBase)
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
        $this->masterModel = $masterModel;
        $this->dataBase = $dataBase;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CommonJobService::db_switch($this->dataBase);
        Log::useFiles(storage_path() . '/logs/accounts_payable_ledger_jobs.log');
        $masterModel = $this->masterModel;
        if (!empty($masterModel)) {
            DB::beginTransaction();
            try {
               $res = AccountPayableLedgerService::postLedgerEntry($masterModel);
               if (!$res['status']) {
                    DB::rollback();
                    Log::error($res['error']['message']);

                    JobErrorLogService::storeError($this->dataBase, $masterModel['documentSystemID'], $masterModel['autoID'], $this->tag, 1, $res['error']['message']);
               } else {
                    DB::commit();
               }
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
                JobErrorLogService::storeError($this->dataBase, $masterModel['documentSystemID'], $masterModel['autoID'], $this->tag, 2, $this->failed($e), "-****----Line No----:".$e->getLine()."-****----File Name----:".$e->getFile());
            }
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

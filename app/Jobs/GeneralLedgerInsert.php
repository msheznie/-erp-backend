<?php

namespace App\Jobs;

use App\Services\GeneralLedgerService;
use App\Services\JobErrorLogService;
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
use App\helper\CommonJobService;


class GeneralLedgerInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $masterModel;
    protected $otherData;
    protected $dataBase;
    private $tag = "general-ledger";
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($masterModel, $dataBase, $otherData = null)
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

        $this->dataBase = $dataBase;
        $this->masterModel = $masterModel;
        $this->otherData = $otherData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CommonJobService::db_switch($this->dataBase);
        Log::useFiles(storage_path() . '/logs/general_ledger_jobs.log');
        $masterModel = $this->masterModel;

        if (!empty($masterModel)) {
            DB::beginTransaction();
            try {
                $res = GeneralLedgerService::postGlEntry($masterModel, $this->dataBase, $this->otherData);
                if (!$res['status']) {
                    DB::rollback();
                    Log::error('Error');
                    Log::error($res['error']['message']);

                    JobErrorLogService::storeError($this->dataBase, $masterModel['documentSystemID'], $masterModel['autoID'], $this->tag, 1, $res['error']['message']);
                } else {
                    $checkBalance = GeneralLedgerService::validateDebitCredit($masterModel['documentSystemID'], $masterModel['autoID']);

                    if (!$checkBalance['status']) {
                        DB::rollback();
                        Log::error($checkBalance['error']['message']);

                        JobErrorLogService::storeError($this->dataBase, $masterModel['documentSystemID'], $masterModel['autoID'], $this->tag, 2, $checkBalance['error']['message']);
                    } else {
                        DB::commit();
                    }
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

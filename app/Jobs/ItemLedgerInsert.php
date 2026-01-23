<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\helper\CommonJobService;
use App\Services\ItemLedgerService;
use App\Services\JobErrorLogService;

class ItemLedgerInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $masterModel;
    protected $dataBase;
    protected $tag = 'item-ledger';

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

    /**
     * A common function to inster entry to item ledger
     * @param $params : accept parameters as an object
     * $param 1-documentSystemID : document id
     * no return values
     */
    public function handle()
    {
        CommonJobService::db_switch($this->dataBase);
        $masterModel = $this->masterModel;
        if (!empty($masterModel)) {
            if (!isset($masterModel['documentSystemID'])) {
                Log::channel('item_ledger_jobs')->warning('Parameter document id is missing' . date('H:i:s'));
            }
            DB::beginTransaction();
            try {
               $res = ItemLedgerService::postLedgerEntry($masterModel);
               if (!$res['status']) {
                    DB::rollback();
                    Log::channel('item_ledger_jobs')->error($res['error']['message']);

                    JobErrorLogService::storeError($this->dataBase, $masterModel['documentSystemID'], $masterModel['autoID'], $this->tag, 1, $res['error']['message']);
               } else {
                    DB::commit();
               }
            } catch (\Exception $e) {
                DB::rollback();
                Log::channel('item_ledger_jobs')->error($this->failed($e));
                JobErrorLogService::storeError($this->dataBase, $masterModel['documentSystemID'], $masterModel['autoID'], $this->tag, 2, $this->failed($e), "-****----Line No----:".$e->getLine()."-****----File Name----:".$e->getFile());
            }
        } else {
            Log::channel('item_ledger_jobs')->error('Parameter not exist' . date('H:i:s'));
        }

    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

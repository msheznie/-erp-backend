<?php

namespace App\Jobs;

use App\Services\UnbilledGRVService;
use App\Services\JobErrorLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\helper\CommonJobService;

class UnbilledGRVInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $masterModel;
    protected $dataBase;
    private $tag = 'unbilled-grv';
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

        //
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
        $masterModel = $this->masterModel;
        if (!empty($masterModel)) {
            if (!isset($masterModel['documentSystemID'])) {
                Log::channel('unbilled_grv_jobs')->warning('Parameter document id is missing' . date('H:i:s'));
            }
            DB::beginTransaction();
            try {
                $res = UnbilledGRVService::postLedgerEntry($masterModel);

                if (!$res['status']) {
                    DB::rollback();
                    Log::channel('unbilled_grv_jobs')->error($res['error']['message']);

                    JobErrorLogService::storeError($this->dataBase, $masterModel['documentSystemID'], $masterModel['autoID'], $this->tag, 1, $res['error']['message']);
                } else {
                    DB::commit();
                }
                
            } catch (\Exception $e) {
                DB::rollback();
                Log::channel('unbilled_grv_jobs')->error('Error occurred when updating to unbilled grv table' . date('H:i:s'));
                JobErrorLogService::storeError($this->dataBase, $masterModel['documentSystemID'], $masterModel['autoID'], $this->tag, 2, $e->getMessage(), "-****----Line No----:".$e->getLine()."-****----File Name----:".$e->getFile());
            }
        }
    }
}

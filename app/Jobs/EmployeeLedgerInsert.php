<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\JobErrorLogService;
use App\Services\EmployeeLedgerService;
use App\helper\CommonJobService;

class EmployeeLedgerInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

     protected $masterModel;
     protected $dataBase;
     private $tag = 'employee-ledger';

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

        $this->dataBase = $dataBase;
        $this->masterModel = $masterModel;
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
            DB::beginTransaction();
            try {
                $res = EmployeeLedgerService::postLedgerEntry($masterModel);
                if (!$res['status']) {
                    DB::rollback();
                    Log::channel('employee_ledger_jobs')->error($res['error']['message']);

                    JobErrorLogService::storeError($this->dataBase, $masterModel['documentSystemID'], $masterModel['autoID'], $this->tag, 1, $res['error']['message']);
                } else {
                    DB::commit();
                }
            } catch
            (\Exception $e) {
                DB::rollback();
                Log::channel('employee_ledger_jobs')->error($this->failed($e));
                JobErrorLogService::storeError($this->dataBase, $masterModel['documentSystemID'], $masterModel['autoID'], $this->tag, 2, $e->getMessage(), "-****----Line No----:".$e->getLine()."-****----File Name----:".$e->getFile());
            }
        }
    }

    public
    function failed($exception)
    {
        return $exception->getMessage();
    }
}

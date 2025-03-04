<?php

namespace App\Jobs\B2B;

use App\helper\CommonJobService;
use App\Services\B2B\CheckBankStatusService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BankStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $tenantDb;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenantDb)
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

        $this->tenantDb = $tenantDb;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CommonJobService::db_switch($this->tenantDb);
        $batchConfigService = new CheckBankStatusService($this->tenantDb);
        $batchConfigService->updateStatusOfFilesFromSuccessPath();
        $batchConfigService->updateStatusOfFilesFromfailurePath();
    }
}

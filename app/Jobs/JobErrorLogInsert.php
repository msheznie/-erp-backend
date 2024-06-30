<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\JobErrorLog;
use App\helper\CommonJobService;

class JobErrorLogInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $errorData;
    protected $dataBase;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($errorData, $dataBase)
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
        $this->errorData = $errorData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CommonJobService::db_switch($this->dataBase);
        Log::useFiles(storage_path().'/logs/job_error_log.log');
        Log::error($this->errorData);
        JobErrorLog::create($this->errorData);        
    }
}

<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\helper\CommonJobService;
use App\Services\hrms\employee\EmpProfileCreateNotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CheckRegisterNotificationService;

class CheckRegisterNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $params; 
    public $dbName;

    public function __construct($dbName, $params)
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
        
        $this->dbName = $dbName;
        $this->params = $params;
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    
        if (empty($this->dbName)) {
            Log::error("db details not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
           
        } else {            

            CommonJobService::db_switch($this->dbName);
            $obj = new CheckRegisterNotificationService($this->params);
            $obj->proceed();
        }
    }
}

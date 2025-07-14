<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\hrms\attendance\AbsentNotificationNonCrossDayService;


class AbsentNotificationNonCrossDayCompany implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tenantDb;
    public $companyId;
    public $companyName;
    public $debug;

    public function __construct($tenantDb, $companyId, $companyName, $debug = false)
    {        
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->tenantDb = $tenantDb;
        $this->companyId = $companyId;
        $this->companyName = $companyName;
        $debug = $this->$debug;
    }

    public function handle()
    {
        Log::useFiles( CommonJobService::get_specific_log_file('absent-notification') );
             
        CommonJobService::db_switch( $this->tenantDb );

        $msg = "Company id {$this->companyId} started to execute the non cross day notification";
        $msg .= "{$this->tenantDb} DB";

        if($this->debug){ 
            Log::info( $msg ); 
        }

        $now = Carbon::now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');        
        
        $job = new AbsentNotificationNonCrossDayService($this->companyId, $date, $time);
        
        $job->run();
    }
}

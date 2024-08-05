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
use App\Services\hrms\attendance\AttendanceDailySummaryService;
use App\Services\hrms\attendance\AttendanceWeeklySummaryService;

class AttendanceSummaryNotificationCompany implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tenantDb;
    public $companyId;
    public $companyName;
    public $isDailyBasis;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenantDb, $companyId, $companyName, $isDailyBasis)
    {        
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->tenantDb = $tenantDb;
        $this->companyId = $companyId;
        $this->companyName = $companyName;
        $this->isDailyBasis = $isDailyBasis;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles( CommonJobService::get_specific_log_file('attendance-notification') );
             
        CommonJobService::db_switch( $this->tenantDb );

        $logSlug = ($this->isDailyBasis)? "Daily": "Weekly";

        $now = Carbon::now();
        $date = $now->format('Y-m-d');    
        
        if(!$this->isDailyBasis){
            $job = new AttendanceWeeklySummaryService($this->companyId, $date);
        }
        else{            
            $date = Carbon::parse($date)->subDay(1)->format('Y-m-d');
            $job = new AttendanceDailySummaryService($this->companyId, $date);
        }
        
        $job->run();
    }
}

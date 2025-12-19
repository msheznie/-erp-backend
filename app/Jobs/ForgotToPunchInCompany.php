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
use App\Services\hrms\attendance\ForgotToPunchInService;
use App\Services\hrms\attendance\ForgotToPunchOutService;

class ForgotToPunchInCompany implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tenantDb;
    public $companyId;
    public $companyName;
    public $isPunchOut;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenantDb, $companyId, $companyName, $isPunchOut=false)
    {        
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->tenantDb = $tenantDb;
        $this->companyId = $companyId;
        $this->companyName = $companyName;
        $this->isPunchOut = $isPunchOut;
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

        $logSlug = ($this->isPunchOut)? "punch out": "";

        
        $now = Carbon::now();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');        
        
        if($this->isPunchOut){
            $date = Carbon::parse($date)->subDay(1)->format('Y-m-d');
            $job = new ForgotToPunchOutService($this->companyId, $date);
        }
        else{
            $job = new ForgotToPunchInService($this->companyId, $date, $time);
        }
        
        $job->run();
    }
}

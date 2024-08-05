<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\LeaveCarryForwardComputationService;

class LeaveCarryForwardComputationInitiate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $dispatchDb;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatchDb)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->dispatchDb = $dispatchDb;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $path = CommonJobService::get_specific_log_file('leave-carry-forward');
        Log::useFiles($path); 
        
        $db = $this->dispatchDb;
        $mainDb = DB::connection()->getDatabaseName();

        
        /* Switch to client DB */
        CommonJobService::db_switch($db);

        $companyList = CommonJobService::company_list();

        if ($companyList->count() == 0) {
            Log::error("Company details not found on $db ( DB ) \t on file: " . __CLASS__ . " \tline no :" . __LINE__);
            return;
        }

        $companyList = $companyList->toArray();


        
        /* Switch back to main DB */
        CommonJobService::db_switch($mainDb);

        foreach ($companyList as $company) {
            
            LeaveCarryForwardCompany::dispatch($db, $company);

        }

        
    }

}

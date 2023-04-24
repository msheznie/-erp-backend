<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\helper\BirthdayWishService;
use App\helper\LeaveCarryForwardComputationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

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
        
        Log::info('DB switched'.$db); 
        CommonJobService::db_switch($db);

        $companyList = CommonJobService::company_list();

        if ($companyList->count() == 0) {
            Log::error("Company details not found on $db ( DB ) \t on file: " . __CLASS__ . " \tline no :" . __LINE__);
            return;
        }

        $companyList = $companyList->toArray();

        Log::info("Leave carry forward computation initiated on {$db} \t on file: " . __CLASS__ . " \tline no :" . __LINE__);

        foreach ($companyList as $company) {
            $ser = new LeaveCarryForwardComputationService($company);
            $ser->execute();
        }
    }

}

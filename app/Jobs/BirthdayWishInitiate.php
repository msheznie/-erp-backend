<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\helper\BirthdayWishService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class BirthdayWishInitiate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    protected $company_code = '';
    protected $company_name = '';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->dispatch_db = $dispatch_db;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $path = CommonJobService::get_specific_log_file('birthday-wishes');
        Log::useFiles($path);

        $db = $this->dispatch_db;

        CommonJobService::db_switch($db);

        $company_list = CommonJobService::company_list();

        if ($company_list->count() == 0) {
            Log::error("Company details not found on $db ( DB ) \t on file: " . __CLASS__ . " \tline no :" . __LINE__);
            return;
        }

        $company_list = $company_list->toArray();


        foreach ($company_list as $company) {
            $ser = new BirthdayWishService($company);
            $ser->execute();
        }
    }

}

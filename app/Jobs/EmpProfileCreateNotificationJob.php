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


class EmpProfileCreateNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $companyId; 
    public $id; 
    public $dbName;
    public $masterDetails;

    public function __construct($dbName, $companyId, $id, $masterDetails)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        
        $this->dbName = $dbName;
        $this->companyId = $companyId;
        $this->id = $id; 
        $this->masterDetails = $masterDetails; 
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles( CommonJobService::get_specific_log_file('emp_create_profile') );
    
        if (empty($this->dbName)) {
            Log::error("db details not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
           
        } else {            

            CommonJobService::db_switch($this->dbName);
            $obj = new EmpProfileCreateNotificationService($this->companyId, $this->id, $this->masterDetails);
            $obj->execute();
        }
    }
}

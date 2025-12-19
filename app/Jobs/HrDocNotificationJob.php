<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\helper\CommonJobService;
use App\Services\hrms\hrDocument\HrDocNotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;


class HrDocNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $tenantId;
    public $companyId; 
    public $id; 
    public $employees; 
    public $visibility;
    public $portalUrl;
    public $dbName;

    public function __construct($dbName, $companyId, $id, $visibility, $employees, $portalUrl)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        
        $this->dbName = $dbName;
        $this->companyId = $companyId;
        $this->id = $id; 
        $this->visibility = $visibility;
        $this->employees = $employees; 
        $this->portalUrl = $portalUrl; 
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        Log::useFiles( CommonJobService::get_specific_log_file('hr-document') );
        
        if (empty($this->dbName)) {
            Log::error("db details not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
           
        } else {            

            CommonJobService::db_switch($this->dbName);
            $obj = new HrDocNotificationService($this->companyId, $this->id ,$this->visibility,$this->employees ,$this->portalUrl);
            $obj->execute();
        }
    }
}

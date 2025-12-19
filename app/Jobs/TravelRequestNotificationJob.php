<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\helper\CommonJobService;
use App\Services\hrms\travelRequest\TravelRequestNotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TravelRequestNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $dbName;
    public $companyId; 
    public $id; 
    public $tripMaster; 
    public $tripRequestBookings; 

    public function __construct($dbName, $companyId, $id,$tripMaster,$tripRequestBookings)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        
        $this->dbName = $dbName;
        $this->companyId = $companyId;
        $this->id = $id; 
        $this->tripMaster = $tripMaster; 
        $this->tripRequestBookings = $tripRequestBookings; 
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles( CommonJobService::get_specific_log_file('travel-request') );
        if (empty($this->dbName)) {
            Log::error("db details not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);            
        } else {
            CommonJobService::db_switch($this->dbName);
            $obj = new TravelRequestNotificationService($this->companyId, $this->id,$this->tripMaster,$this->tripRequestBookings);
            $obj->execute();
        }
    }
}

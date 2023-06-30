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

    public $db_name;
    public $companyId; 
    public $id; 
    public $tripMaster; 
    public $tripRequestBookings; 

    public function __construct($db_name, $companyId, $id,$tripMaster,$tripRequestBookings)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        
        $this->db_name = $db_name;
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
        if (empty($this->db_name)) {
            Log::error("db details not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);            
        } else {
            Log::info("Job triggered");
            CommonJobService::db_switch($this->db_name);
            $obj = new TravelRequestNotificationService($this->companyId, $this->id,$this->tripMaster,$this->tripRequestBookings);
            $obj->execute();
        }
    }
}

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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    public function __construct($tenantId, $companyId, $id, $visibility, $employees)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        
        $this->tenantId = $tenantId;
        $this->companyId = $companyId;
        $this->id = $id; 
        $this->visibility = $visibility;
        $this->employees = $employees; 
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        
        $db_name = CommonJobService::get_tenant_db($this->tenantId);
        
        if (empty($db_name)) {
            $message = "db details not found. \t on file: " . __CLASS__ . " \tline no :" . __LINE__;
            $data = [
                'company_id' => $this->companyId,
                'module' => 'HRMS',
                'description' => 'HR Document Notification Scenario Error',
                'scenario_id' => 0,
                'processed_for' => Carbon::now()->format('Y-m-d H:i:s'),
                'logged_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'log_type' => 'error',
                'log_data' => json_encode($message)
            ];
            DB::table('job_logs')->insert($data);
        } else {            
            CommonJobService::db_switch($db_name);
            $obj = new HrDocNotificationService($this->companyId, $this->tenantId, $this->id ,$this->visibility,$this->employees);
            $obj->execute();
        }
    }
}

<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\LeaveCarryForwardComputationService;

class LeaveCarryForwardCompany implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dispatchDb;
    public $company;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatchDb, $company)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->dispatchDb = $dispatchDb;
        $this->company = $company;
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

        CommonJobService::db_switch($this->dispatchDb);

        try {

            $ser = new LeaveCarryForwardComputationService($this->company);
            $ser->execute();

        } catch (\Exception $exception) {

            $logData = json_encode($exception);
            $dateTime = Carbon::now();

            $data = [
                'company_id' => $this->company['id'],
                'module' => 'HRMS',
                'description' => 'Leave Maximum Carry Forward Adjustment',
                'scenario_id' => 0,
                'processed_for' => $dateTime,
                'logged_at' => $dateTime,
                'log_type' => 'error',
                'log_data' => $logData
            ];
            
            DB::table('job_logs')->insert($data);
        }
    }
}

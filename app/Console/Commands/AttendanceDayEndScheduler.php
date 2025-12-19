<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use App\Jobs\AttendanceDayEndPullingInitiate;
use Carbon\Carbon;

class AttendanceDayEndScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull-attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull attendance in the day end';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::useFiles( CommonJobService::get_specific_log_file('attendance-clockOut') );
        
        $attDate = Carbon::now()->timezone('Asia/Muscat')->subDays(1)->format('Y-m-d');

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            $msg = "Tenant details not found ( {$attDate} ).";
            Log::error("{$msg} \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            return;
        }
        

        foreach ($tenants as $tenant){
            $dispatchDb = $tenant->database;

            $msg = "{$dispatchDb} DB added to the queue for attendance day end pulling initiate";
            $msg .= " ( {$attDate} ).";

            AttendanceDayEndPullingInitiate::dispatch($dispatchDb, $this->signature, $attDate);
        }
    }
}

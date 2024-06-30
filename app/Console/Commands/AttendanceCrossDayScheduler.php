<?php

namespace App\Console\Commands;

use App\Jobs\AttendanceCrossDayPulling;
use Illuminate\Console\Command;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AttendanceCrossDayScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull-cross-day-attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull cross day attendance';

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
        Log::useFiles( CommonJobService::get_specific_log_file('attendance-cross-day-clockOut') );

        $attDate = Carbon::now()->timezone('Asia/Muscat');

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            $msg = "Tenant details not found ( {$attDate} ).";
            Log::error("{$msg} \t on file: " . __CLASS__ ." \tline no :".__LINE__);
            return;
        }


        foreach ($tenants as $tenant){
            $dispatchDb = $tenant->database;

            $msg = "{$dispatchDb} DB added to the queue for cross day attendance pulling initiate";
            $msg .= " ( {$attDate} ).";

            AttendanceCrossDayPulling::dispatch($dispatchDb, $this->signature, $attDate);
        }
    }
}

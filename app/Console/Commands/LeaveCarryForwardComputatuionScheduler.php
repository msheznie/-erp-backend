<?php

namespace App\Console\Commands;

use App\helper\CommonJobService;
use Illuminate\Console\Command;
use App\Jobs\LeaveCarryForwardComputationInitiate;
use Illuminate\Support\Facades\Log;


class LeaveCarryForwardComputatuionScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:leaveCarryForwardComputationSchedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Leave Carry Forward Computation Year End Scheduler';

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
        Log::useFiles(CommonJobService::get_specific_log_file('leave-carry-forward'));
        $tenants = CommonJobService::tenant_list();
        if (count($tenants) == 0) {
            return;
        }

        foreach ($tenants as $tenant) {
            $tenant_database = $tenant->database;

                . __CLASS__ . " \tline no :" . __LINE__);

            LeaveCarryForwardComputationInitiate::dispatch($tenant_database);
        }
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\helper\CommonJobService;
use App\Jobs\ForgotToPunchInTenant;
use Illuminate\Support\Facades\Log;

class ForgotToPunchOutScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:forgotToPunchOut';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attendance employee forgot to punch-out scheduler';

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
        Log::useFiles( CommonJobService::get_specific_log_file('attendance-notification') );                    


        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            return;
        }


        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;

            ForgotToPunchInTenant::dispatch($tenantDb, true);            
        }
    }
}

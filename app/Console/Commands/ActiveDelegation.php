<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use App\Jobs\DelegationActivation;

class ActiveDelegation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:delegationActive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        Log::useFiles( CommonJobService::get_specific_log_file('delegation') );                    


        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            return;
        }


        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;

            DelegationActivation::dispatch($tenantDb);            
        }
    }
}

<?php

namespace App\Console\Commands;

use App\helper\CommonJobService;
use App\Jobs\B2B\BankStatus;
use App\Models\BankConfig;
use Illuminate\Console\Command;

class CheckB2BStatusWithBank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:checkb2bstatus';

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
        $tenants = CommonJobService::tenant_list();

        if(count($tenants) == 0){
            return;
        }

        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;
            CommonJobService::db_switch($tenantDb);
            $getConfigDetails = BankConfig::where('slug', 'ahlibank')->exists();
            if($getConfigDetails)
                 BankStatus::dispatch($tenantDb)->onQueue("bankStatus");
        }
    }

}

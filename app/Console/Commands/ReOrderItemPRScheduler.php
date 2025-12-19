<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ReOrderItemPR;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;

class ReOrderItemPRScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:newPR';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reorder level PR';

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
        }


        foreach ($tenants as $tenant){
            $tenant_database = $tenant->database;


            ReOrderItemPR::dispatch($tenant_database);
        }        
    }
}

<?php

namespace App\Console\Commands;

use App\helper\CommonJobService;
use App\Jobs\ItemWACTriggerJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ItemWACAmountPost extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'itemWACAmountPost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Item WAC Amount Post';

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
        Log::useFiles( CommonJobService::get_specific_log_file('item-wac-amount') );

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            return;
        }


        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;

            ItemWACTriggerJob::dispatch($tenantDb, true);
        }
    }
}

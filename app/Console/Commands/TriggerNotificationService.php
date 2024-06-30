<?php

namespace App\Console\Commands;

use App\helper\CommonJobService;
use App\helper\NotificationService;
use App\Jobs\NotificationInitiate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TriggerNotificationService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification_service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger the notification service';

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
        Log::useFiles( NotificationService::log_file() );

        //$tenants = NotificationService::get_tenant_details();
        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
        }


        foreach ($tenants as $tenant){
            $tenant_database = $tenant->database;


            NotificationInitiate::dispatch($tenant_database);
        }
    }
}

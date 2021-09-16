<?php

namespace App\Console\Commands;

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

        $tenants = NotificationService::get_tenant_details();
        if(count($tenants) == 0){
            Log::info("Tenant details not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
        }


        foreach ($tenants as $tenant){
            $tenant_database = $tenant->database;

            Log::info("{$tenant_database} DB added to queue for notification initiate . \t on file: " . __CLASS__ ." \tline no :".__LINE__);

            NotificationInitiate::dispatch($tenant_database);
        }
    }
}

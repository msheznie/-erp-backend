<?php

namespace App\Console\Commands;

use App\helper\CommonJobService;
use App\Jobs\SendNotificationReminderJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendTenderBidOpeningReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendTenderBidOpeningReminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Tender Bid Opening Reminders';

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
        Log::useFiles( CommonJobService::get_specific_log_file('tender-bid') );


        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            return false;
        }
        Log::info($tenants);
        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;
            SendNotificationReminderJob::dispatch($tenantDb);
        }
        return true;
    }
}

<?php

namespace App\Console\Commands;

use App\Jobs\NotificationInitiate;
use Illuminate\Console\Command;

class TriggerNotificationService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trigger:notification_service';

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
        NotificationInitiate::dispatch();
    }
}

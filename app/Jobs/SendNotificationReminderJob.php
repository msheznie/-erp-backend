<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\SendTenderNotificationService;

class SendNotificationReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $dispatchDB;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatchDB)
    {
        if (env('IS_MULTI_TENANCY', false))
        {
            self::onConnection('database_main');
        } else
        {
            self::onConnection('database');
        }
        $this->dispatchDB = $dispatchDB;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->dispatchDB;
        CommonJobService::db_switch($db);
        $service = new SendTenderNotificationService();
        $service->tenderNotificationScenarioBased();
    }
}

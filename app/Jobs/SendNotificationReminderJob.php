<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
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
        if (env('QUEUE_DRIVER_CHANGE','database') == 'database') {
            if (env('IS_MULTI_TENANCY',false)) {
                self::onConnection('database_main');
            }
            else {
                self::onConnection('database');
            }
        }
        else {
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
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
        try {
            $db = $this->dispatchDB;
            CommonJobService::db_switch($db);
            $service = new SendTenderNotificationService();
            $service->tenderNotificationScenarioBased();
        } catch (\Exception $e) {
            Log::error('SendNotificationReminderJob failed', [
                'database' => $this->dispatchDB,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}

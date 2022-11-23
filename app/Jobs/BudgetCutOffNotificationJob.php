<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\Helper;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\NotificationCompanyScenario;
use App\Services\BudgetCutOffNotificationService;
use Illuminate\Support\Facades\Auth;
use App\helper\CommonJobService;
use App\helper\NotificationService;

class BudgetCutOffNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        Log::info('Budget cutoff JOB construct'.$this->dispatch_db);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Budget cutoff JOB started');
        $db = $this->dispatch_db;
        CommonJobService::db_switch($db);

        BudgetCutOffNotificationService::sendBudgetCutOffNotification($db);
    }
}

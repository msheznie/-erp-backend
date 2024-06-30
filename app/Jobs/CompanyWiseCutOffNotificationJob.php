<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use App\Services\BudgetCutOffNotificationService;
use App\helper\Helper;
use App\Models\ProcumentOrder;
use App\Models\CompanyPolicyMaster;
use App\Models\BudgetMaster;
use App\Models\GRVDetails;
use App\helper\BudgetConsumptionService;
use App\helper\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyWiseCutOffNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $compAssignScenarioData;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $compAssignScenarioData)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->compAssignScenarioData = $compAssignScenarioData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/budget-cutoff-po.log');  
        $db = $this->dispatch_db;
        $compAssignScenario = $this->compAssignScenarioData;
        CommonJobService::db_switch($db);

        $companyIDFromScenario = $compAssignScenario['companyID'];
        $partiallyRecivedPos = ProcumentOrder::with(['currency'])
                                             ->where('grvRecieved', '!=', 2)
                                             ->where('approved', -1)
                                             ->whereHas('budget_consumed_data', function($query) {
                                                $query->whereHas('budget_master');
                                             })
                                             ->where('companySystemID', $companyIDFromScenario)
                                             ->get();
        

        $partiallyRecivedPos = $partiallyRecivedPos->toArray();
        if (count($compAssignScenario['notification_day_setup']) == 0) {
            Log::error('Notification day setup not exist in '.$db);
        } else {
            foreach ($compAssignScenario['notification_day_setup'] as $notDaySetup) {
                $beforeAfter = $notDaySetup['beforeAfter'];
                $days = $notDaySetup['days'];

                $notificationUserSettings = NotificationService::notificationUserSettings($notDaySetup['id']);
                if (count($notificationUserSettings['email']) == 0) {
                    Log::error("User setup not found for scenario");
                    continue;
                }


                BudgetCutOffNotificationService::getCutOffPurchaseOrders($db, $partiallyRecivedPos, $beforeAfter, $days, $notificationUserSettings['email'], $companyIDFromScenario);
            }   
        }
    }
}

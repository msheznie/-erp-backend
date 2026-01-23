<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use App\Services\BudgetNotificationService;
use App\Models\DepartmentBudgetPlanning;
use App\Models\BudgetNotificationDetail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BudgetSubmissionDeadlineReachedNotificationJob implements ShouldQueue
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
        if (env('IS_MULTI_TENANCY', false)) {
            self::onConnection('database_main');
        } else {
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $db = $this->dispatch_db;
        CommonJobService::db_switch($db);

        Log::channel('budget_submission_deadline_reached_notification')->info('Budget submission deadline reached notification job started for database: ' . $db);

        try {
            $this->sendDeadlineReachedNotifications();
            Log::channel('budget_submission_deadline_reached_notification')->info('Budget submission deadline reached notification job completed successfully for database: ' . $db);
        } catch (\Exception $e) {
            Log::channel('budget_submission_deadline_reached_notification')->error('Error in budget submission deadline reached notification job for database ' . $db . ': ' . $e->getMessage());
            throw $e;
        }
    }

    private function sendDeadlineReachedNotifications()
    {
        $today = Carbon::today();

        // Find budget plannings with submission date that has passed (deadline reached)
        // Only for non-submitted budget plannings
        $departmentBudgetPlannings = DepartmentBudgetPlanning::with([
            'department.hod.employee',
            'masterBudgetPlannings.company',
            'financeYear'
        ])
        ->whereDate('submissionDate', '=', $today)
        ->get();


        if ($departmentBudgetPlannings->isEmpty()) {
            Log::info('No budget plannings found with submission date that has passed');
            return;
        }

        Log::info('Found ' . $departmentBudgetPlannings->count() . ' budget planning(s) with submission date that has passed');


        foreach ($departmentBudgetPlannings as $budgetPlanning) {
            try {
                // Get company system ID
                $companySystemID = $budgetPlanning->masterBudgetPlannings->companySystemID ?? null;
                
                if (!$companySystemID) {
                    Log::warning('Budget planning ID ' . $budgetPlanning->id . ' has no company system ID');
                    continue;
                }

                // Find active notification details for this company
                $notificationDetail = BudgetNotificationDetail::with('notification')
                    ->where('isActive', 1)
                    ->where('companySystemID', $companySystemID)
                    ->first();

                if (!$notificationDetail || !$notificationDetail->notification) {
                    Log::info('No active notification found for company: ' . $companySystemID);
                    continue;
                }

                $notification = $notificationDetail->notification;
                $scenario = 'submission-deadline-reached'; // Scenario for deadline reached

                $budgetNotificationService = new BudgetNotificationService();
                // Send notification
                $budgetNotificationService->sendNotification(
                    $budgetPlanning->id,
                    $scenario,
                    $companySystemID
                );

                Log::info('Deadline reached notification sent for budget planning ID: ' . $budgetPlanning->id . ', Company: ' . $companySystemID);

            } catch (\Exception $e) {
                Log::error('Error sending deadline reached notification for budget planning ID ' . $budgetPlanning->id . ': ' . $e->getMessage());
                // Continue with next budget planning
            }
        }
    }
}


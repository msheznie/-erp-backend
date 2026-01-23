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

class BudgetDeadlineNotificationJob implements ShouldQueue
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

        Log::channel('budget_deadline_notification')->info('Budget deadline notification job started for database: ' . $db);

        try {
            $this->sendDeadlineNotifications();
            Log::channel('budget_deadline_notification')->info('Budget deadline notification job completed successfully for database: ' . $db);
        } catch (\Exception $e) {
            Log::channel('budget_deadline_notification')->error('Error in budget deadline notification job for database ' . $db . ': ' . $e->getMessage());
            throw $e;
        }
    }

    private function sendDeadlineNotifications()
    {
        $today = Carbon::today();

        $budgetNotificationDetails = BudgetNotificationDetail::where('isActive', 1)->where('notification_id', 4)->get();

        if ($budgetNotificationDetails->isEmpty()) {
            $targetDate = $today->copy()->addDays(2)->startOfDay();
        }else {
            $reminderTime = $budgetNotificationDetails->reminderTime;
            // Convert hours to days (reminderTime is in hours)
            // Since submissionDate is date-only, we need to round up to get the target date
            $reminderTimeInDays = ceil($reminderTime / 24);
            $targetDate = $today->copy()->addDays($reminderTimeInDays)->startOfDay();
        }

        // Find budget plannings with submission date within the reminder time
        // Since submissionDate is a date field (YYYY-mm-dd), we compare dates only
        $departmentBudgetPlannings = DepartmentBudgetPlanning::with([
            'department.hod.employee',
            'masterBudgetPlannings.company',
            'financeYear'
        ])
        ->where(function($query) use ($today, $targetDate) {
            $query->where('submissionDate','>', $today->toDateString())
                  ->where('submissionDate', '<=', $targetDate->toDateString());
        })
        ->where('workStatus', '!=', 3) // Only for non-submitted
        ->get();

        if ($departmentBudgetPlannings->isEmpty()) {
            Log::info('No budget plannings found with submission date less than 48 hours away');
            return;
        }

        Log::info('Found ' . $departmentBudgetPlannings->count() . ' budget planning(s) with submission date less than 48 hours away');


        foreach ($departmentBudgetPlannings as $budgetPlanning) {
            try {
                // Get company system ID
                $companySystemID = $budgetPlanning->masterBudgetPlannings->companySystemID ?? null;
                
                if (!$companySystemID) {
                    Log::warning('Budget planning ID ' . $budgetPlanning->id . ' has no company system ID');
                    continue;
                }

                // Find active notification details for this company
                // We'll use a default scenario or find the first active notification
                // You may want to add a specific slug for deadline notifications
                $notificationDetail = BudgetNotificationDetail::with('notification')
                    ->where('isActive', 1)
                    ->where('companySystemID', $companySystemID)
                    ->first();

                if (!$notificationDetail || !$notificationDetail->notification) {
                    Log::info('No active notification found for company: ' . $companySystemID);
                    continue;
                }

                $notification = $notificationDetail->notification;
                $scenario = 'deadline-warning'; // Default scenario if slug not set

                $budgetNotificationService = new BudgetNotificationService();
                // Send notification
                $budgetNotificationService->sendNotification(
                    $budgetPlanning->id,
                    $scenario,
                    $companySystemID
                );

                Log::info('Deadline notification sent for budget planning ID: ' . $budgetPlanning->id . ', Company: ' . $companySystemID);

            } catch (\Exception $e) {
                Log::error('Error sending deadline notification for budget planning ID ' . $budgetPlanning->id . ': ' . $e->getMessage());
                // Continue with next budget planning
            }
        }
    }
}


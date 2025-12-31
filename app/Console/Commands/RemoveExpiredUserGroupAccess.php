<?php

namespace App\Console\Commands;

use App\Traits\AuditLogsTrait;
use Illuminate\Console\Command;
use App\Models\EmployeeNavigation;
use App\Models\EmployeeNavigationAccess;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RemoveExpiredUserGroupAccess extends Command
{
    use AuditLogsTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:removeExpiredUserGroupAccess';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove users from user groups when their time-based access end date is reached';

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
        Log::useFiles(CommonJobService::get_specific_log_file('user_group_access'));

        $tenants = CommonJobService::tenant_list();
        if (count($tenants) == 0) {
            return;
        }

        foreach ($tenants as $tenant) {
            $tenantDb = $tenant->database;
            CommonJobService::db_switch($tenantDb);

            $currentDate = Carbon::today()->format('Y-m-d');
            $uuid = $tenant->uuid ?? 'local';

            // Find all time-based access records where end date has passed and isActive is still 1
            $expiredAccess = EmployeeNavigationAccess::with(['employee', 'userGroup'])
                ->where('accessType', 'time_based')
                ->whereNotNull('endDate')
                ->whereDate('endDate', '<', $currentDate)
                ->where('isActive', 1)
                ->get();

            if ($expiredAccess->count() > 0) {
                $expiredIds = $expiredAccess->pluck('id')->toArray();
                $expiredEmpNavIds = $expiredAccess->pluck('employeeNavigationID')->toArray();

                foreach ($expiredAccess as $access) {
                    // Get employee and user group information for audit log
                    $employee = $access->employee;
                    $userGroup = $access->userGroup;
                    $employeeName = $employee ? $employee->empName : '';
                    $userGroupName = $userGroup ? $userGroup->description : '';

                    // Prepare previous value for audit log
                    $previousValue = [
                        'employee' => $employee ? $employee->toArray() : null,
                        'userGroup' => $userGroup ? $userGroup->toArray() : null,
                        'access' => $access->toArray()
                    ];
                    $employeeNavigationId = $access->employeeNavigationID;
                    // Add audit log for system removal
                    $narrationVariables = $employeeName . ' - ' . $userGroupName . ' (System Remove - End Date Reached)';
                    self::auditLog($tenantDb, $employeeNavigationId, $uuid, "srp_erp_employeenavigation", $narrationVariables, "D", [], $previousValue);
                }
                // Mark as inactive instead of deleting to keep records for history
                EmployeeNavigationAccess::whereIn('id', $expiredIds)->update(['isActive' => 0]);
                // Delete from srp_erp_employeenavigation
                EmployeeNavigation::whereIn('id', $expiredEmpNavIds)->delete();

                Log::info('Marked ' . count($expiredIds) . ' expired user group access records as inactive for tenant');
            } else {
                Log::info('No expired user group access records found for tenant');
            }
        }
    }
}

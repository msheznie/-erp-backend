<?php

namespace App\Traits;

use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use App\Jobs\AuditLog\AuditLogJob;
use App\Jobs\AuditLog\AuthAuditLogJob;
use App\Jobs\AuditLog\NavigationAccessAuditLogJob;

trait AuditLogsTrait
{
    public static function auditLog($dataBase, $transactionID, $tenant_uuid, $table, $narration, $crudType, $newValue = [], $previosValue = [], $parentID = null, $parentTable = null, $empID = null)
    {
        if(empty($empID))
        {
            $user = \Helper::getEmployeeName();
        }else {
            $user = Employee::where('employeeSystemID',$empID)->value('empName') ?? 0;
        }

        AuditLogJob::dispatch($dataBase, $transactionID, $tenant_uuid, $table, $narration, $crudType, $newValue, $previosValue, $parentID, $parentTable, $user);
    }


    public static function log($type, $parameters)
    {
        switch ($type) {
            case 'auth':
                AuthAuditLogJob::dispatch($parameters);
                break;
            case 'navigationAccess':
                NavigationAccessAuditLogJob::dispatch($parameters);
                break;
            default:
                return false;
        }
    }

    public static function logNavigationAccess($parameters)
    {
    }
}

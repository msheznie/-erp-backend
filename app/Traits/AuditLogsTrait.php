<?php

namespace App\Traits;

use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use App\Jobs\AuditLog\AuditLogJob;
use App\Jobs\AuditLog\AuthAuditLogJob;
use App\Jobs\AuditLog\NavigationAccessAuditLogJob;
use Illuminate\Support\Facades\Auth;

trait AuditLogsTrait
{
    public static function auditLog($dataBase, $transactionID, $tenant_uuid, $table, $narration, $crudType, $newValue = [], $previosValue = [], $parentID = null, $parentTable = null, $empID = null)
    {
        $authEmploeeId = Auth::user() ? Auth::user()->employee_id : null;

        $user = !is_null($empID) ? $empID : $authEmploeeId;

        //get token id
        $tokenId = Auth::user() && Auth::user()->token() ? Auth::user()->token()->id : null;

        // Pass narration as docCode (original narration for translation key lookup)
        // Variables will be extracted in the job from the narration itself
        AuditLogJob::dispatch($dataBase, $transactionID, $tenant_uuid, $table, $narration, $crudType, $newValue, $previosValue, $parentID, $parentTable, $user, $tokenId);
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

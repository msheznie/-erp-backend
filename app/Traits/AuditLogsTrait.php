<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use App\Jobs\AuditLog\AuditLogJob;

trait AuditLogsTrait
{
    public static function auditLog($dataBase, $transactionID, $tenant_uuid, $table, $narration, $crudType, $newValue = [], $previosValue = [], $parentID = null, $parentTable = null)
    {
        $user = \Helper::getEmployeeName();
        AuditLogJob::dispatch($dataBase, $transactionID, $tenant_uuid, $table, $narration, $crudType, $newValue, $previosValue, $parentID, $parentTable, $user);
    }
}

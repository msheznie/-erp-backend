<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait AuditLogsTrait
{
    public function createAuditLogs($transactionID, $amendedField, $previousValue, $newValue, $table, $uuid){
        Log::useFiles(storage_path() . '/logs/audit.log');

        $user = \Helper::getEmployeeName();
        Log::info('data:',[
            'transaction_id' => $transactionID,
            'user_name' => $user,
            'date_time' => date('Y-m-d H:i:s'),
            'amended_field' => $amendedField,
            'previous_value' => $previousValue,
            'new_value' => $newValue,
            'table' => $table,
            'channel' => 'audit',
            'tenant_uuid' => $uuid
        ]);
    }

}

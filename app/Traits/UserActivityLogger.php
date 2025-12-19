<?php

namespace App\Traits;
use App\Models\UserActivityLog;
use App\Scopes\ActiveScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
trait UserActivityLogger
{

    /**
     * @param int $user_id
     * @param int $document_id
     * @param string $description
     * @param string $previous_value
     * @param string $current_value
     * @return array
     */
    public static function createUserActivityLogArray($user_id = null, $document_id, $company_id, $module_id, $description = '', $current_value = '', $previous_value = '', $column = '')
    {
        $log_array =  [
            'user_id' => isset($user_id) ? $user_id : Auth::id(),
            'document_id' => $document_id,
            'company_id' => $company_id,
            'module_id' => $module_id,
            'description' => $description,
            'previous_value' => $previous_value,
            'current_value' => $current_value,
            'column_name' => $column,
            'activity_at' => new \DateTime(),
            'user_pc' => gethostname()
        ];

        return UserActivityLog::create($log_array);
    }

}

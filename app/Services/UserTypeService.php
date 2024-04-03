<?php

namespace App\Services;

use App\Models\Employee;

class UserTypeService
{
    /**
     * Get System User
     * @return mixed
     */
    public static function getSystemEmployee(){
        return Employee::whereHas('user_data', function ($query) {
            $query->whereHas('user_type', function ($query) {
                $query->where('isSystemUser', 1);
            });
        })->first();
    }
}

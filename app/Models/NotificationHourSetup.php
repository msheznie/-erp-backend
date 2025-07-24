<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationHourSetup extends Model
{
    public $table = 'notification_hour_setup';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'company_scenario_id',
        'beforeAfter',
        'hours',
        'is_active',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'company_scenario_id' => 'integer',
        'beforeAfter' => 'integer',
        'hours' => 'integer',
        'is_active' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];
}

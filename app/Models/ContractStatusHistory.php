<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractStatusHistory extends Model
{
    public $table = 'cm_contract_status_history';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'contract_id',
        'status',
        'company_id',
        'contract_history_id',
        'created_by',
        'updated_by',
        'system_user'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'contract_id' => 'integer',
        'contract_history_id' => 'integer',
        'status' => 'integer',
        'company_id' => 'integer',
        'system_user' => 'boolean',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];
}

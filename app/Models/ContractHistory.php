<?php

namespace App\Models;
use Eloquent as Model;

class ContractHistory extends Model
{
    public $table = 'cm_contract_history';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'category',
        'date',
        'end_date',
        'uuid',
        'contract_id',
        'cloning_contract_id',
        'company_id',
        'created_by',
        'comment',
        'confirmed_yn',
        'confirmed_date',
        'confirm_by',
        'confirmed_comment',
        'rollLevelOrder',
        'refferedBackYN',
        'approved_yn',
        'approved_by',
        'approved_date',
        'timesReferred',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category' => 'integer',
        'uuid' => 'string',
        'date' => 'date',
        'end_date' => 'date',
        'contract_id' => 'integer',
        'cloning_contract_id' => 'integer',
        'company_id' => 'integer',
        'created_by' => 'integer',
        'comment' => 'string',
        'confirmed_yn' => 'integer',
        'confirmed_date' => 'datetime',
        'confirm_by' => 'integer',
        'confirmed_comment' => 'string',
        'rollLevelOrder' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];

    public function contractMaster()
    {
        return $this->hasOne(ContractMaster::class, 'id', 'contract_id');
    }
}

<?php

namespace App\Models;

use Eloquent as Model;

class ContractSettingDetail extends Model
{
    public $table = 'cm_contract_setting_detail';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'uuid',
        'contractId',
        'settingMasterId',
        'sectionDetailId',
        'isActive'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'contractId' => 'integer',
        'settingMasterId' => 'integer',
        'sectionDetailId' => 'integer',
        'isActive' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'settingMasterId' => 'required|integer',
        'sectionDetailId' => 'nullable|integer',
        'isActive' => 'nullable|boolean',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];
}

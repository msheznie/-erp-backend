<?php

namespace App\Models;
use Eloquent as Model;

class ContractCounterPartyMaster extends Model
{
    public $table = 'cm_counter_parties_master';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $fillable = [
        'cmCounterParty_name',
        'cpt_active',
        'timestamp'
    ];

    protected $casts = [
        'cmCounterParty_id' => 'integer',
        'cmCounterParty_name' => 'string',
        'cpt_active' => 'boolean',
        'timestamp' => 'datetime'
    ];

    public static $rules = [
        'cmCounterParty_name' => 'nullable|string|max:200',
        'cpt_active' => 'required|boolean',
        'timestamp' => 'nullable'
    ];
}

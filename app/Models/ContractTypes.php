<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractTypes extends Model
{
    public $table = 'cm_contract_types';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $hidden = ['contract_typeId', 'cmParty_id', 'created_by'];

    public $fillable = [
        'uuid',
        'cm_type_name',
        'cmMaster_id',
        'cmIntent_id',
        'cmPartyA_id',
        'cmPartyB_id',
        'cmCounterParty_id',
        'cm_type_description',
        'ct_active',
        'companySystemID',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'uuid' => 'string',
        'contract_typeId' => 'integer',
        'cm_type_name' => 'string',
        'cmMaster_id' => 'integer',
        'cmIntent_id' => 'integer',
        'cmPartyA_id' => 'integer',
        'cmPartyB_id' => 'integer',
        'cmCounterParty_id' => 'integer',
        'cm_type_description' => 'string',
        'ct_active' => 'boolean',
        'companySystemID' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'cm_type_name' => 'nullable|string|max:100',
        'cmMaster_id' => 'nullable|integer',
        'cmIntent_id' => 'nullable|integer',
        'cmPartyA_id' => 'nullable|integer',
        'cmPartyB_id' => 'nullable|integer',
        'cmCounterParty_id' => 'nullable|integer',
        'cm_type_description' => 'nullable|string',
        'ct_active' => 'required|boolean',
        'companySystemID' => 'nullable|integer',
        'created_by' => 'nullable|integer',
        'created_at' => 'nullable',
        'updated_by' => 'nullable|integer',
        'updated_at' => 'nullable'
    ];

    public static function getContractTypes($companySystemID)
    {
        return ContractTypes::select('uuid', 'cm_type_name', 'ct_active')
            ->where('companySystemID', $companySystemID)
            ->where('ct_active', 1)
            ->get();
    }

    public static function getContractTypeId($contractTypeUuid)
    {
        return ContractTypes::select('contract_typeId', 'cmCounterParty_id')
            ->where('uuid', $contractTypeUuid)
            ->first();
    }


}

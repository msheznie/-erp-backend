<?php

namespace App\Models;

use Eloquent as Model;

class ContractUsers extends Model
{
    public $table = 'cm_contract_users';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $hidden = ['id'];
    protected $primaryKey = 'id';

    public $fillable = [
        'contractUserId',
        'contractUserType',
        'contractUserCode',
        'contractUserName',
        'uuid',
        'isActive',
        'companySystemId',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'id' => 'integer',
        'contractUserId' => 'integer',
        'contractUserType' => 'integer',
        'contractUserCode' => 'string',
        'contractUserName' => 'string',
        'uuid' => 'string',
        'isActive' => 'integer',
        'companySystemId' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer'
    ];

    public static $rules = [
        'contractUserId' => 'required|integer',
        'contractUserCode' => 'nullable|string',
        'contractUserName' => 'nullable|string',
        'uuid' => 'nullable|string|unique:cm_contract_users,uuid',
        'isActive' => 'required|integer',
        'companySystemId' => 'nullable|integer',
        'created_by' => 'nullable|integer',
        'updated_by' => 'nullable|integer',
        'created_at' => 'nullable',
        'updated_at' => 'nullable'
    ];

    public static function checkSupplierExists($supplierId, $contractUserType, $companySystemId, $isActive)
    {
        return ContractUsers::where('contractUserId', $supplierId)
            ->where('contractUserType', $contractUserType)
            ->where('companySystemId', $companySystemId)
            ->where('isActive', $isActive)
            ->exists();
    }
}

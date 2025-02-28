<?php

namespace App\Models;

use Eloquent as Model;

class ContractUserAssign extends Model
{
    public $table = 'cm_contract_user_assign';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at'];
    protected $hidden = ['id', 'userGroupId', 'userId', 'createdBy', 'updatedBy'];

    public $fillable = [
        'uuid',
        'contractId',
        'userGroupId',
        'userId',
        'status',
        'createdBy',
        'updatedBy',
        'created_at',
        'updated_at'
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
        'userGroupId' => 'integer',
        'userId' => 'integer',
        'status' => 'integer',
        'createdBy' => 'integer',
        'updatedBy' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public static function isExistingRecord($contractId, $userGroupId, $userId)
    {
        return ContractUserAssign::select('uuid', 'userGroupId')
            ->where('contractId', $contractId)
            ->where('userGroupId', $userGroupId)
            ->where('userId', $userId)
            ->where('status', 1)
            ->first();
    }
}

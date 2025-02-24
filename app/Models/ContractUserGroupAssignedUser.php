<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractUserGroupAssignedUser extends Model
{
    public $table = 'cm_contract_user_group_assigned_user';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    protected $hidden = ['id', 'contractUserId'];

    public $fillable = [
        'id',
        'uuid',
        'userGroupId',
        'companySystemID',
        'contractUserId',
        'status',
        'giveAccessToExistingContracts',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'userGroupId' => 'integer',
        'companySystemID' => 'integer',
        'contractUserId' => 'integer',
        'status' => 'integer',
        'giveAccessToExistingContracts' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'userGroupId' => 'required',
        'companySystemID' => 'required',
        'contractUserId' => 'required'
    ];

    public static function getUserIdsAssignedUserGroup($defaultUserIds)
    {
        return ContractUserGroupAssignedUser::select('contractUserId', 'userGroupId')
            ->whereIn('userGroupId', $defaultUserIds)
            ->where('status', 1)
            ->get();
    }
}

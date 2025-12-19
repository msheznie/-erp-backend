<?php

namespace App\Models;

use Eloquent as Model;

class ContractUserGroup extends Model
{
    public $table = 'cm_contract_user_group';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];
    protected $hidden = ['id'];



    public $fillable = [
        'uuid',
        'groupName',
        'status',
        'isDefault',
        'companySystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'uuid' => 'string',
        'groupName' => 'string',
        'companySystemID' => 'integer',
        'status' => 'integer',
        'isDefault' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public static function getDefaultUserIds($companySystemID)
    {
        return ContractUserGroup::select('id', 'uuid')
            ->where('companySystemID', $companySystemID)
            ->where('isDefault', 1)
            ->where('status', 1)
            ->pluck('id')
            ->toArray();
    }
}

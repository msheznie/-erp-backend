<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierCategoryMaster
 * @package App\Models
 * @version February 27, 2018, 1:02 pm UTC
 *
 * @property string categoryCode
 * @property string categoryDescription
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 * @property string|\Carbon\Carbon timeStamp
 */
class SupplierCategoryMaster extends Model
{
    //use SoftDeletes;

    public $table = 'suppliercategorymaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'categoryCode',
        'categoryDescription',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supCategoryMasterID' => 'integer',
        'categoryCode' => 'string',
        'categoryDescription' => 'string',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];


    
}

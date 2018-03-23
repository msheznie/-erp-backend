<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierCategorySub
 * @package App\Models
 * @version February 27, 2018, 10:49 am UTC
 *
 * @property integer supMasterCategoryID
 * @property string subCategoryCode
 * @property string categoryDescription
 * @property string|\Carbon\Carbon timeStamp
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 */
class SupplierCategorySub extends Model
{
    // use SoftDeletes;

    public $table = 'suppliercategorysub';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'supMasterCategoryID',
        'subCategoryCode',
        'categoryDescription',
        'timeStamp',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supCategorySubID' => 'integer',
        'supMasterCategoryID' => 'integer',
        'subCategoryCode' => 'string',
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

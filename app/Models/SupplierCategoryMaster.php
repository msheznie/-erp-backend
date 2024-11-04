<?php
/**
 * =============================================
 * -- File Name : SupplierCategoryMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Category Master
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
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
    protected $primaryKey  = 'supCategoryMasterID';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'categoryCode',
        'categoryDescription',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'categoryName',
        'isActive',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supCategoryMasterID' => 'integer',
        'categoryCode' => 'string',
        'categoryName' => 'string',
        'categoryDescription' => 'string',
        'isActive' => 'integer',
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

<?php
/**
 * =============================================
 * -- File Name : SupplierCategorySub.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Category Sub
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
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
    protected $primaryKey  = 'supCategorySubID';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'supMasterCategoryID',
        'subCategoryCode',
        'categoryName',
        'categoryDescription',
        'isActive',
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
        'isActive' => 'integer',
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


    public function scopeIsActive($query)
    {
        return $query->where('isActive',1);
    }
    
}

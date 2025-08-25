<?php
/**
 * =============================================
 * -- File Name : FinanceItemCategoryMaster.php
 * -- Project Name : ERP
 * -- Module Name : Finance Item Category Master
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Class FinanceItemCategoryMaster
 * @package App\Models
 * @version March 8, 2018, 12:18 pm UTC
 *
 * @property string categoryDescription
 * @property string itemCodeDef
 * @property integer numberOfDigits
 * @property integer lastSerialOrder
 * @property string|\Carbon\Carbon timeStamp
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 */
class FinanceItemCategoryMaster extends Model
{
    //use SoftDeletes;

    public $table = 'financeitemcategorymaster';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'itemCategoryID';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'categoryDescription',
        'itemCodeDef',
        'numberOfDigits',
        'lastSerialOrder',
        'exipryYN',
        'attributesYN',
        'trackingYN',
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
        'itemCategoryID' => 'integer',
        'categoryDescription' => 'string',
        'itemCodeDef' => 'string',
        'numberOfDigits' => 'integer',
        'lastSerialOrder' => 'integer',
        'trackingYN' => 'integer',
        'exipryYN' => 'integer',
        'attributesYN' => 'integer',
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

    public function item_sub_category()
    {
        return $this->hasMany('App\Models\FinanceItemCategorySub', 'itemCategoryID', 'itemCategoryID');
    }

    public static function getMainCategory()
    {
        return FinanceItemCategoryMaster::select(DB::raw("itemCategoryID as value, categoryDescription as label"))
            ->get();
    }

    
}

<?php
/**
 * =============================================
 * -- File Name : FinanceItemCategorySub.php
 * -- Project Name : ERP
 * -- Module Name : Finance Item Category Sub
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class FinanceItemCategorySub
 * @package App\Models
 * @version March 8, 2018, 12:02 pm UTC
 *
 * @property string categoryDescription
 * @property integer itemCategoryID
 * @property integer financeGLcodebBSSystemID
 * @property string financeGLcodebBS
 * @property integer financeGLcodePLSystemID
 * @property string financeGLcodePL
 * @property integer includePLForGRVYN
 * @property string|\Carbon\Carbon createdDateTime
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon timeStamp
 */
class FinanceItemCategorySub extends Model
{
    //use SoftDeletes;

    public $table = 'financeitemcategorysub';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'itemCategorySubID';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'categoryDescription',
        'itemCategoryID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'financeCogsGLcodePLSystemID',
        'financeCogsGLcodePL',
        'financeGLcodeRevenueSystemID',
        'financeGLcodeRevenue',
        'includePLForGRVYN',
        'expiryYN',
        'attributesYN',
        'isActive',
        'trackingType',
        'createdDateTime',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'timeStamp',
        'enableSpecification'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'itemCategorySubID' => 'integer',
        'categoryDescription' => 'string',
        'itemCategoryID' => 'integer',
        'financeGLcodebBSSystemID' => 'integer',
        'financeGLcodebBS' => 'string',
        'financeGLcodePLSystemID' => 'integer',
        'financeGLcodePL' => 'string',
        'financeCogsGLcodePLSystemID' => 'integer',
        'financeCogsGLcodePL' => 'string',
        'financeGLcodeRevenueSystemID' => 'integer',
        'financeGLcodeRevenue' => 'string',
        'includePLForGRVYN' => 'integer',
        'expiryYN' => 'integer',
        'attributesYN' => 'integer',
        'isActive' => 'integer',
        'trackingType' => 'integer',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'enableSpecification' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function finance_gl_code_bs()
    {
        return $this->belongsTo(\App\Models\ChartOfAccount::class,'financeGLcodebBSSystemID','chartOfAccountSystemID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function finance_gl_code_pl()
    {
        return $this->belongsTo(\App\Models\ChartOfAccount::class,'financeGLcodePLSystemID','chartOfAccountSystemID');
    }

    public function cogs_gl_code_pl()
    {
        return $this->belongsTo(\App\Models\ChartOfAccount::class,'financeCogsGLcodePLSystemID','chartOfAccountSystemID');
    }

    public function finance_gl_code_revenue()
    {
        return $this->belongsTo(\App\Models\ChartOfAccount::class,'financeGLcodeRevenueSystemID','chartOfAccountSystemID');
    }

    public function finance_item_category_master()
    {
        return $this->belongsTo(\App\Models\FinanceItemCategoryMaster::class,'itemCategoryID','itemCategoryID');
    }

    public function finance_item_category_type() {
        return $this->hasMany('App\Models\FinanceItemCategoryTypes','itemCategorySubID','itemCategorySubID');
    }
    
}

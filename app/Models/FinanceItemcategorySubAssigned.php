<?php
/**
 * =============================================
 * -- File Name : FinanceItemcategorySubAssigned.php
 * -- Project Name : ERP
 * -- Module Name : Finance Item category SubAssigned
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class FinanceItemcategorySubAssigned
 * @package App\Models
 * @version March 8, 2018, 12:03 pm UTC
 *
 * @property integer mainItemCategoryID
 * @property integer itemCategorySubID
 * @property string categoryDescription
 * @property integer financeGLcodebBSSystemID
 * @property string financeGLcodebBS
 * @property integer financeGLcodePLSystemID
 * @property string financeGLcodePL
 * @property integer includePLForGRVYN
 * @property integer companySystemID
 * @property string companyID
 * @property integer isActive
 * @property integer isAssigned
 * @property string|\Carbon\Carbon timeStamp
 */
class FinanceItemcategorySubAssigned extends Model
{
    //use SoftDeletes;

    public $table = 'financeitemcategorysubassigned';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'itemCategoryAssignedID';



    protected $dates = ['deleted_at'];


    public $fillable = [
        'mainItemCategoryID',
        'itemCategorySubID',
        'categoryDescription',
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
        'companySystemID',
        'companyID',
        'isActive',
        'isAssigned',
        'timeStamp',
        'enableSpecification'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'itemCategoryAssignedID' => 'integer',
        'mainItemCategoryID' => 'integer',
        'itemCategorySubID' => 'integer',
        'categoryDescription' => 'string',
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
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'isActive' => 'integer',
        'isAssigned' => 'integer',
        'enableSpecification' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

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

    public function finance_gl_code_revenue()
    {
        return $this->belongsTo(\App\Models\ChartOfAccount::class,'financeGLcodeRevenueSystemID','chartOfAccountSystemID');
    }

    public function finance_item_category_sub(){
        return $this->belongsTo('App\Models\FinanceItemCategorySub','itemCategorySubID','itemCategorySubID');
    }

    public function finance_item_category_type(){
        return $this->hasMany('App\Models\FinanceItemCategoryTypes','itemCategorySubID','itemCategorySubID');
    }

}

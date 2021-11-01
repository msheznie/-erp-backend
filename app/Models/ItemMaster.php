<?php
/**
 * =============================================
 * -- File Name : ItemMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Item Master
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ItemMaster
 * @package App\Models
 * @version March 8, 2018, 10:35 am UTC
 *
 * @property string primaryItemCode
 * @property integer runningSerialOrder
 * @property integer documentSystemID
 * @property string documentID
 * @property integer primaryCompanySystemID
 * @property string primaryCompanyID
 * @property string primaryCode
 * @property string secondaryItemCode
 * @property string barcode
 * @property string itemDescription
 * @property string itemShortDescription
 * @property string itemUrl
 * @property integer unit
 * @property integer financeCategoryMaster
 * @property integer financeCategorySub
 * @property string itemPicture
 * @property integer selectedForAssign
 * @property integer isActive
 * @property integer sentConfirmationEmail
 * @property string confirmationEmailSentByEmpID
 * @property string confirmationEmailSentByEmpName
 * @property integer itemConfirmedYN
 * @property string itemConfirmedByEMPID
 * @property string itemConfirmedByEMPName
 * @property string|\Carbon\Carbon itemConfirmedDate
 * @property string itemApprovedBy
 * @property integer itemApprovedYN
 * @property string|\Carbon\Carbon itemApprovedDate
 * @property string itemApprovedComment
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 * @property string|\Carbon\Carbon timestamp
 */
class ItemMaster extends Model
{
    //use SoftDeletes;

    public $table = 'itemmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'itemCodeSystem';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'primaryItemCode',
        'runningSerialOrder',
        'documentSystemID',
        'documentID',
        'primaryCompanySystemID',
        'primaryCompanyID',
        'primaryCode',
        'secondaryItemCode',
        'barcode',
        'itemDescription',
        'itemShortDescription',
        'itemUrl',
        'unit',
        'financeCategoryMaster',
        'financeCategorySub',
        'faFinanceCatID',
        'itemPicture',
        'selectedForAssign',
        'isActive',
        'RollLevForApp_curr',
        'sentConfirmationEmail',
        'confirmationEmailSentByEmpID',
        'confirmationEmailSentByEmpName',
        'itemConfirmedYN',
        'itemConfirmedByEMPSystemID',
        'itemConfirmedByEMPID',
        'itemConfirmedByEMPName',
        'itemConfirmedDate',
        'itemApprovedBySystemID',
        'itemApprovedBy',
        'itemApprovedYN',
        'itemApprovedDate',
        'itemApprovedComment',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timestamp',
        'createdUserSystemID',
        'modifiedUserSystemID',
        'refferedBackYN',
        'timesReferred',
        'isPOSItem',
        'vatSubCategory',
        'expiryYN'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'itemCodeSystem' => 'integer',
        'primaryItemCode' => 'string',
        'runningSerialOrder' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'primaryCompanySystemID' => 'integer',
        'primaryCompanyID' => 'string',
        'primaryCode' => 'string',
        'secondaryItemCode' => 'string',
        'barcode' => 'string',
        'itemDescription' => 'string',
        'itemShortDescription' => 'string',
        'itemUrl' => 'string',
        'unit' => 'integer',
        'financeCategoryMaster' => 'integer',
        'financeCategorySub' => 'integer',
        'faFinanceCatID' => 'integer',
        'itemPicture' => 'string',
        'selectedForAssign' => 'integer',
        'isActive' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'sentConfirmationEmail' => 'integer',
        'confirmationEmailSentByEmpID' => 'string',
        'confirmationEmailSentByEmpName' => 'string',
        'itemConfirmedYN' => 'integer',
        'itemConfirmedByEMPSystemID'  => 'integer',
        'itemConfirmedByEMPID' => 'string',
        'itemConfirmedByEMPName' => 'string',
        'itemApprovedBySystemID' => 'integer',
        'itemApprovedBy' => 'string',
        'itemApprovedYN' => 'integer',
        'itemApprovedComment' => 'string',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'createdUserSystemID' => 'integer',
        'modifiedUserSystemID' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'isPOSItem' => 'integer',
        'vatSubCategory'=>'integer',
        'expiryYN'=>'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        //'secondaryItemCode'  => 'required',
        //'secondaryItemCode' => 'unique:itemmaster,secondaryItemCode',
    ];

    public function unit(){
        return $this->hasOne('App\Models\Unit','UnitID','unit');
    }

    public function unit_by(){
        return $this->hasOne('App\Models\Unit','UnitID','unit');
    }

    public function financeMainCategory(){
        return $this->hasOne('App\Models\FinanceItemCategoryMaster','itemCategoryID','financeCategoryMaster');
    }

    public function financeSubCategory(){
        return $this->hasOne('App\Models\FinanceItemCategorySub','itemCategorySubID','financeCategorySub');
    }

    public function asset_category(){
        return $this->hasOne('App\Models\AssetFinanceCategory','faFinanceCatID','faFinanceCatID');
    }

    public function documentapproved(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','itemCodeSystem');
    }

    public function finalApprovedBy()
    {
        return $this->belongsTo('App\Models\Employee','itemApprovedBySystemID','employeeSystemID');
    }

    public function specification()
    {
        return $this->belongsTo('App\Models\ItemSpecification','itemCodeSystem','item_id');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'itemConfirmedByEMPSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','itemCodeSystem');
    }

    public function vat_sub_category()
    {
        return $this->belongsTo('App\Models\TaxVatCategories', 'vatSubCategory', 'taxVatSubCategoriesAutoID');
    }
}

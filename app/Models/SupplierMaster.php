<?php
/**
 * =============================================
 * -- File Name : SupplierMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Master
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use App\helper\Helper;
use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class SupplierMaster
 * @package App\Models
 * @version February 21, 2018, 11:27 am UTC
 *
 * @property string uniqueTextcode
 * @property integer primaryCompanySystemID
 * @property string primaryCompanyID
 * @property string primarySupplierCode
 * @property string secondarySupplierCode
 * @property string supplierName
 * @property integer liabilityAccountSysemID
 * @property string liabilityAccount
 * @property integer UnbilledGRVAccountSystemID
 * @property string UnbilledGRVAccount
 * @property string address
 * @property integer countryID
 * @property string supplierCountryID
 * @property string telephone
 * @property string fax
 * @property string supEmail
 * @property string webAddress
 * @property integer currency
 * @property string nameOnPaymentCheque
 * @property float creditLimit
 * @property float creditPeriod
 * @property string registrationNumber
 * @property string registrationExprity
 * @property string approvedby
 * @property integer approvedYN
 * @property string|\Carbon\Carbon approvedDate
 * @property string approvedComment
 * @property integer isActive
 * @property integer isSupplierForiegn
 * @property integer supplierConfirmedYN
 * @property integer supplierConfirmedEmpSystemID
 * @property string supplierConfirmedEmpID
 * @property string supplierConfirmedEmpName
 * @property string|\Carbon\Carbon supplierConfirmedDate
 * @property integer isCriticalYN
 * @property string companyLinkedTo
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 * @property integer isDirect
 * @property integer supplierImportanceID
 * @property integer supplierNatureID
 * @property integer supplierTypeID
 * @property integer WHTApplicable
 * @property string|\Carbon\Carbon timestamp
 * @property string|\Carbon\Carbon last_activity
 */
class SupplierMaster extends Model
{
    //use SoftDeletes;

    public $table = 'suppliermaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'supplierCodeSystem';
    protected $appends = ['isSUPDAmendAccess'];

    protected $dates = ['deleted_at'];


    public $fillable = [
        'uniqueTextcode',
        'primaryCompanySystemID',
        'primaryCompanyID',
        'primarySupplierCode',
        'secondarySupplierCode',
        'supplierName',
        'liabilityAccountSysemID',
        'liabilityAccount',
        'UnbilledGRVAccountSystemID',
        'UnbilledGRVAccount',
        'address',
        'countryID',
        'supplierCountryID',
        'telephone',
        'fax',
        'supEmail',
        'webAddress',
        'currency',
        'nameOnPaymentCheque',
        'creditLimit',
        'creditPeriod',
        'interCompanyYN',
        'registrationNumber',
        'registrationExprity',
        'approvedby',
        'approvedYN',
        'approvedDate',
        'approvedComment',
        'approvedEmpSystemID',
        'isActive',
        'isSupplierForiegn',
        'supplierConfirmedYN',
        'supplierConfirmedEmpID',
        'supplierConfirmedEmpName',
        'supplierConfirmedEmpSystemID',
        'supplierConfirmedDate',
        'RollLevForApp_curr',
        'isCriticalYN',
        'companyLinkedToSystemID',
        'companyLinkedTo',
        'linkCustomerID',
        'linkCustomerYN',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'isDirect',
        'supplierImportanceID',
        'supplierNatureID',
        'supplierTypeID',
        'WHTApplicable',
        'timestamp',
        'documentSystemID',
        'documentID',
        'vatEligible',
        'vatNumber',
        'vatPercentage',
        'retentionPercentage',
        'createdUserSystemID',
        'modifiedUserSystemID',
        'supCategoryICVMasterID',
        'supCategorySubICVID',
        'isLCCYN',
        'isSMEYN',
        'isMarkupPercentage',
        'markupPercentage',
        'refferedBackYN',
        'timesReferred',
        'jsrsNo',
        'isBlocked',
        'blockedBy',
        'blockedDate',
        'blockedReason',
        'jsrsExpiry',
        'createdFrom',
        'supplier_category_id',
        'supplier_group_id',
        'last_activity',
        'advanceAccountSystemID',
        'AdvanceAccount',
        'blockType',
        'blockFrom',
        'blockTo',
        'blockReason',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supplierCodeSystem' => 'integer',
        'uniqueTextcode' => 'string',
        'primaryCompanySystemID' => 'integer',
        'primaryCompanyID' => 'string',
        'primarySupplierCode' => 'string',
        'secondarySupplierCode' => 'string',
        'supplierName' => 'string',
        'liabilityAccountSysemID' => 'integer',
        'liabilityAccount' => 'string',
        'UnbilledGRVAccountSystemID' => 'integer',
        'UnbilledGRVAccount' => 'string',
        'address' => 'string',
        'countryID' => 'integer',
        'supplierCountryID' => 'string',
        'telephone' => 'string',
        'fax' => 'string',
        'supEmail' => 'string',
        'webAddress' => 'string',
        'currency' => 'integer',
        'nameOnPaymentCheque' => 'string',
        'creditLimit' => 'float',
        'creditPeriod' => 'float',
        'registrationNumber' => 'string',
        'registrationExprity' => 'string',
        'approvedby' => 'string',
        'approvedYN' => 'integer',
        'approvedComment' => 'string',
        'approvedEmpSystemID' => 'integer',
        'isActive' => 'integer',
        'isSupplierForiegn' => 'integer',
        'supplierConfirmedYN' => 'integer',
        'supplierConfirmedEmpID' => 'string',
        'interCompanyYN' => 'integer',
        'supplierConfirmedEmpSystemID' => 'integer',
        'supplierConfirmedEmpName' => 'string',
        'RollLevForApp_curr' => 'integer',
        'isCriticalYN' => 'integer',
        'companyLinkedToSystemID' => 'integer',
        'companyLinkedTo' => 'string',
        'createdUserGroup' => 'string',
        'createdUserSystemID' => 'integer',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'modifiedUserSystemID' => 'integer',
        'isDirect' => 'integer',
        'supplierImportanceID' => 'integer',
        'supplierNatureID' => 'integer',
        'supplierTypeID' => 'integer',
        'WHTApplicable' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'vatEligible' => 'integer',
        'vatNumber' => 'string',
        'vatPercentage' => 'integer',
        'supCategoryICVMasterID' => 'integer',
        'supCategorySubICVID' => 'integer',
        'isLCCYN' => 'integer',
        'isSMEYN' => 'integer',
        'isMarkupPercentage' => 'integer',
        'markupPercentage' => 'float',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'jsrsNo' => 'string',
        'isBlocked' => 'integer',
        'blockedBy' => 'integer',
        'blockedDate' => 'datetime',
        'blockedReason' => 'string',
        'jsrsExpiry' => 'string',
        'createdFrom' => 'integer',
        'supplier_category_id'  => 'integer',
        'supplier_group_id'  => 'integer'
    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfCompany($query, $type)
    {
        return $query->whereIN('primaryCompanySystemID',  $type);
    }

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'currency', 'currencyID');
    }

    public function company() {
        return $this->belongsTo('App\Models\Company','primaryCompanySystemID','companySystemID');
    }

    public function categoryMaster(){
        return $this->hasOne('App\Models\SupplierCategoryMaster', 'supCategoryMasterID','supCategoryMasterID');
    }

    public function employee(){
        return $this->hasOne('App\Models\Employee','empID','createdUserID');
    }

    public function supplierCurrency(){
            return $this->hasMany('App\Models\SupplierCurrency','supplierCodeSystem','supplierCodeSystem');
    }

    public function subCategories(){
        return $this->belongsToMany('App\Models\SupplierCategorySub', 'suppliersubcategoryassign','supSubCategoryID','supplierID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function finalApprovedBy()
    {
        return $this->belongsTo('App\Models\Employee','approvedEmpSystemID','employeeSystemID');
    }

    public function blocked_by()
    {
        return $this->belongsTo('App\Models\Employee','blockedBy','employeeSystemID');
    }
    
    public function country()
    {
        return $this->belongsTo('App\Models\CountryMaster','supplierCountryID','countryID');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'supplierConfirmedEmpSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','supplierCodeSystem');
    }

    public function critical()
    {
        return $this->belongsTo('App\Models\SupplierCritical', 'isCriticalYN', 'suppliercriticalID');
    }

    public function getIsSUPDAmendAccessAttribute()
    {
        return true;
    }

    public function supplierICVCategories(){

        return $this->belongsTo('App\Models\SupplierCategoryICVMaster','supCategoryICVMasterID','supCategoryICVMasterID');

    }

    public function supplierICVSubCategories(){

        return $this->belongsTo('App\Models\SupplierCategoryICVSub','supCategorySubICVID','supCategorySubICVID');

    }

     public function liablity_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'liabilityAccountSysemID', 'chartOfAccountSystemID');
    }

     public function unbilled_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'UnbilledGRVAccountSystemID', 'chartOfAccountSystemID');
    }

    public function supplier_group(){
        return $this->belongsTo('App\Models\SupplierGroup','supplier_group_id','id');
    }

    public function supplier_category(){
        return $this->belongsTo('App\Models\SupplierCategory','supplier_category_id','id');
    }

    public function importance(){
        return $this->belongsTo('App\Models\SupplierImportance','supplierImportanceID','supplierImportanceID');
    }

    public function nature(){
        return $this->belongsTo('App\Models\suppliernature','supplierNatureID','supplierNatureID');
    }

    public function type(){
        return $this->belongsTo('App\Models\SupplierType','supplierTypeID','supplierTypeID');
    }
}

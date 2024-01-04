<?php
/**
 * =============================================
 * -- File Name : SupplierAssigned.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Assigned
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Awobaz\Compoships\Compoships;
/**
 * Class SupplierAssigned
 * @package App\Models
 * @version March 2, 2018, 12:33 pm UTC
 *
 * @property integer supplierCodeSytem
 * @property integer companySystemID
 * @property string companyID
 * @property string uniqueTextcode
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
 * @property integer supCategoryMasterID
 * @property integer supCategorySubID
 * @property string registrationNumber
 * @property string registrationExprity
 * @property integer supplierImportanceID
 * @property integer supplierNatureID
 * @property integer supplierTypeID
 * @property integer WHTApplicable
 * @property integer isRelatedPartyYN
 * @property integer isCriticalYN
 * @property integer isActive
 * @property integer isAssigned
 * @property string|\Carbon\Carbon timestamp
 */
class SupplierAssigned extends Model
{
    //use SoftDeletes;
    use Compoships;
    public $table = 'supplierassigned';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'supplierAssignedID';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'supplierCodeSytem',
        'companySystemID',
        'companyID',
        'uniqueTextcode',
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
        'supCategoryMasterID',
        'supCategorySubID',
        'registrationNumber',
        'registrationExprity',
        'supplierImportanceID',
        'supplierNatureID',
        'supplierTypeID',
        'WHTApplicable',
        'isRelatedPartyYN',
        'isCriticalYN',
        'isActive',
        'isAssigned',
        'timestamp',
        'supCategoryICVMasterID',
        'supCategorySubICVID',
        'isLCCYN',
        'isMarkupPercentage',
        'markupPercentage',
        'jsrsNo',
        'isBlocked',
        'blockedBy',
        'blockedDate',
        'blockedReason',
        'jsrsExpiry',
        'vatEligible',
        'vatNumber',
        'vatPercentage',
        'advanceAccountSystemID',
        'AdvanceAccount',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supplierAssignedID' => 'integer',
        'supplierCodeSytem' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'uniqueTextcode' => 'string',
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
        'supCategoryMasterID' => 'integer',
        'supCategorySubID' => 'integer',
        'registrationNumber' => 'string',
        'registrationExprity' => 'string',
        'supplierImportanceID' => 'integer',
        'supplierNatureID' => 'integer',
        'supplierTypeID' => 'integer',
        'WHTApplicable' => 'integer',
        'isRelatedPartyYN' => 'integer',
        'isCriticalYN' => 'integer',
        'isActive' => 'integer',
        'isAssigned' => 'integer',
        'supCategoryICVMasterID' => 'integer',
        'supCategorySubICVID' => 'integer',
        'isLCCYN' => 'integer',
        'isMarkupPercentage' => 'integer',
        'isBlocked' => 'integer',
        'blockedBy' => 'integer',
        'blockedDate' => 'datetime',
        'blockedReason' => 'string',
        'markupPercentage' => 'float',
        'jsrsNo' => 'string',
        'jsrsExpiry' => 'string',
        'vatEligible' => 'integer',
        'vatNumber' => 'string',
        'vatPercentage' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function categoryMaster(){
        return $this->hasOne('App\Models\SupplierCategoryMaster', 'supCategoryMasterID','supCategoryMasterID');
    }

    public function supplierCurrency(){
        return $this->hasMany('App\Models\SupplierCurrency','supplierCodeSystem','supplierCodeSytem');
    }

    public function critical()
    {
        return $this->belongsTo('App\Models\SupplierCritical', 'isCriticalYN', 'suppliercriticalID');
    }

    public function country()
    {
        return $this->belongsTo('App\Models\CountryMaster','supplierCountryID','countryID');
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

    public function master()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'supplierCodeSytem','supplierCodeSystem');
    }
    public function tenderSupplierAssigned(){ 
        return $this->hasOne('App\Models\TenderSupplierAssignee', 'supplier_assigned_id','supplierAssignedID');
        
    }
    public function businessCategoryAssigned(){
        return $this->hasOne('App\Models\SupplierBusinessCategoryAssign', 'supplierID','supplierCodeSytem');
    }
}

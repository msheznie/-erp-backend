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
 * @property integer supCategoryMasterID
 * @property integer supCategorySubID
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
 */
class SupplierMaster extends Model
{
    //use SoftDeletes;

    public $table = 'suppliermaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'supplierCodeSystem';


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
        'supCategoryMasterID',
        'supCategorySubID',
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
        'companyLinkedTo',
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
        'vatPercentage'
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
        'supCategoryMasterID' => 'integer',
        'supCategorySubID' => 'integer',
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
        'supplierConfirmedEmpSystemID' => 'integer',
        'supplierConfirmedEmpName' => 'string',
        'RollLevForApp_curr' => 'integer',
        'isCriticalYN' => 'integer',
        'companyLinkedTo' => 'string',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'isDirect' => 'integer',
        'supplierImportanceID' => 'integer',
        'supplierNatureID' => 'integer',
        'supplierTypeID' => 'integer',
        'WHTApplicable' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'vatEligible' => 'integer',
        'vatNumber' => 'string',
        'vatPercentage' => 'integer'
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

    public function employee(){
        return $this->hasOne('App\Models\Employee','empID','createdUserID');
    }

    public function supplierCurrency(){
            return $this->hasMany('App\Models\SupplierCurrency','supplierCodeSystem','currency');
    }

    public function subCategories(){
        return $this->belongsToMany('App\Models\SupplierCategorySub', 'suppliersubcategoryassign','supSubCategoryID','supplierID');
    }

}

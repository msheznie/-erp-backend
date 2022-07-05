<?php
/**
 * =============================================
 * -- File Name : SupplierMasterRefferedBack.php
 * -- Project Name : ERP
 * -- Module Name :  Supplier Master Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 15 - December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SupplierMasterRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="supplierCodeSystemRefferedBack",
 *          description="supplierCodeSystemRefferedBack",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierCodeSystem",
 *          description="supplierCodeSystem",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="uniqueTextcode",
 *          description="uniqueTextcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="primaryCompanySystemID",
 *          description="primaryCompanySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="primaryCompanyID",
 *          description="primaryCompanyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="primarySupplierCode",
 *          description="primarySupplierCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="secondarySupplierCode",
 *          description="secondarySupplierCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierName",
 *          description="supplierName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="liabilityAccountSysemID",
 *          description="liabilityAccountSysemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="liabilityAccount",
 *          description="liabilityAccount",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="UnbilledGRVAccountSystemID",
 *          description="UnbilledGRVAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="UnbilledGRVAccount",
 *          description="UnbilledGRVAccount",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="address",
 *          description="address",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="countryID",
 *          description="countryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierCountryID",
 *          description="supplierCountryID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="telephone",
 *          description="telephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="fax",
 *          description="fax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supEmail",
 *          description="supEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="webAddress",
 *          description="webAddress",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="currency",
 *          description="currency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="nameOnPaymentCheque",
 *          description="nameOnPaymentCheque",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="creditLimit",
 *          description="creditLimit",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="creditPeriod",
 *          description="creditPeriod",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="supCategoryMasterID",
 *          description="supCategoryMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supCategorySubID",
 *          description="supCategorySubID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="registrationNumber",
 *          description="registrationNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="registrationExprity",
 *          description="registrationExprity",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedEmpSystemID",
 *          description="approvedEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedby",
 *          description="approvedby",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedComment",
 *          description="approvedComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isActive",
 *          description="isActive",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isSupplierForiegn",
 *          description="isSupplierForiegn",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierConfirmedYN",
 *          description="supplierConfirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierConfirmedEmpID",
 *          description="supplierConfirmedEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierConfirmedEmpSystemID",
 *          description="supplierConfirmedEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierConfirmedEmpName",
 *          description="supplierConfirmedEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isCriticalYN",
 *          description="isCriticalYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyLinkedToSystemID",
 *          description="companyLinkedToSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyLinkedTo",
 *          description="companyLinkedTo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isDirect",
 *          description="isDirect",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierImportanceID",
 *          description="supplierImportanceID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierNatureID",
 *          description="supplierNatureID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTypeID",
 *          description="supplierTypeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="WHTApplicable",
 *          description="WHTApplicable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatEligible",
 *          description="vatEligible",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatNumber",
 *          description="vatNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="vatPercentage",
 *          description="vatPercentage",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supCategoryICVMasterID",
 *          description="supCategoryICVMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supCategorySubICVID",
 *          description="supCategorySubICVID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isLCCYN",
 *          description="isLCCYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="refferedBackYN",
 *          description="refferedBackYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class SupplierMasterRefferedBack extends Model
{

    public $table = 'suppliermaster_refferedback';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'supplierCodeSystemRefferedBack';



    public $fillable = [
        'supplierCodeSystem',
        'uniqueTextcode',
        'primaryCompanySystemID',
        'primaryCompanyID',
        'documentSystemID',
        'documentID',
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
        'approvedYN',
        'approvedEmpSystemID',
        'approvedby',
        'approvedDate',
        'approvedComment',
        'isActive',
        'isSupplierForiegn',
        'supplierConfirmedYN',
        'supplierConfirmedEmpID',
        'supplierConfirmedEmpSystemID',
        'supplierConfirmedEmpName',
        'supplierConfirmedDate',
        'isCriticalYN',
        'companyLinkedToSystemID',
        'companyLinkedTo',
        'linkCustomerID',
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
        'vatEligible',
        'vatNumber',
        'vatPercentage',
        'supCategoryICVMasterID',
        'supCategorySubICVID',
        'isLCCYN',
        'RollLevForApp_curr',
        'refferedBackYN',
        'timesReferred',
        'timestamp',
        'createdUserSystemID',
        'isBlocked',
        'blockedBy',
        'blockedDate',
        'blockedReason',
        'modifiedUserSystemID',
        'advanceAccountSystemID',
        'AdvanceAccount',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'supplierCodeSystemRefferedBack' => 'integer',
        'supplierCodeSystem' => 'integer',
        'uniqueTextcode' => 'string',
        'primaryCompanySystemID' => 'integer',
        'primaryCompanyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
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
        'approvedYN' => 'integer',
        'approvedEmpSystemID' => 'integer',
        'approvedby' => 'string',
        'approvedComment' => 'string',
        'isActive' => 'integer',
        'isSupplierForiegn' => 'integer',
        'supplierConfirmedYN' => 'integer',
        'supplierConfirmedEmpID' => 'string',
        'supplierConfirmedEmpSystemID' => 'integer',
        'supplierConfirmedEmpName' => 'string',
        'isCriticalYN' => 'integer',
        'companyLinkedToSystemID' => 'integer',
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
        'vatEligible' => 'integer',
        'vatNumber' => 'string',
        'vatPercentage' => 'integer',
        'supCategoryICVMasterID' => 'integer',
        'supCategorySubICVID' => 'integer',
        'isLCCYN' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'createdUserSystemID' => 'integer',
        'modifiedUserSystemID' => 'integer',
        'jsrsNo' => 'string',
        'jsrsExpiry' => 'string',
        'isBlocked' => 'integer',
        'blockedBy' => 'integer',
        'blockedDate' => 'datetime',
        'blockedReason' => 'string',
        'jsrsNo',
        'jsrsExpiry'
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
        return $this->hasMany('App\Models\SupplierCurrency','supplierCodeSystem','supplierCodeSystem');
    }

    public function subCategories(){
        return $this->belongsToMany('App\Models\SupplierCategorySub', 'suppliersubcategoryassign','supSubCategoryID','supplierID');
    }

    public function finalApprovedBy()
    {
        return $this->belongsTo('App\Models\Employee','approvedEmpSystemID','employeeSystemID');
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
    
}

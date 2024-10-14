<?php
/**
 * =============================================
 * -- File Name : Company.php
 * -- Project Name : ERP
 * -- Module Name : Company
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\helper\Helper;

/**
 * Class Company
 * @package App\Models
 * @version February 16, 2018, 6:23 am UTC
 *
 * @property string CompanyID
 * @property string CompanyName
 * @property string CompanyNameLocalized
 * @property string LocalName
 * @property integer MasterLevel
 * @property integer CompanyLevel
 * @property string masterComapanyID
 * @property string masterComapanyIDReporting
 * @property string companyShortCode
 * @property integer orgListOrder
 * @property integer orgListSordOrder
 * @property integer sortOrder
 * @property integer listOrder
 * @property string CompanyAddress
 * @property string companyCountry
 * @property integer CompanyTelephone
 * @property integer CompanyFax
 * @property string CompanyEmail
 * @property string CompanyURL
 * @property string|\Carbon\Carbon SubscriptionStarted
 * @property string|\Carbon\Carbon SubscriptionUpTo
 * @property string ContactPerson
 * @property integer ContactPersonTelephone
 * @property integer ContactPersonFax
 * @property string ContactPersonEmail
 * @property string registrationNumber
 * @property string companyLogo
 * @property integer reportingCurrency
 * @property integer localCurrencyID
 * @property string mainFormName
 * @property string menuInitialImage
 * @property string menuInitialSelectedImage
 * @property float policyItemIssueTollerence
 * @property float policyAddonPercentage
 * @property integer policyPOAppDayDiff
 * @property integer policyStockAdjWacCurrentYN
 * @property integer policyDepreciationRunDate
 * @property integer isGroup
 * @property integer isAttachementYN
 * @property string reportingCriteria
 * @property string reportingCriteriaFormQuery
 * @property string supplierReportingCriteria
 * @property string supplierReportingCriteriaFormQuery
 * @property string supplierPOSavReportingCriteria
 * @property string supplierPOSavReportingCriteriaFormQuery
 * @property string supplierPOSpentReportingCriteriaFormQuery
 * @property string exchangeGainLossGLCode
 * @property string exchangeLossGLCode
 * @property string exchangeGainGLCode
 * @property string exchangeProvisionGLCode
 * @property string exchangeProvisionGLCodeAR
 * @property integer isApprovalByServiceLine
 * @property integer isApprovalByServiceLineFinance
 * @property integer isTaxYN
 * @property integer isActive
 * @property integer isActiveGroup
 * @property integer showInCombo
 * @property integer allowBackDatedGRV
 * @property integer allowCustomerInvWithoutContractID
 * @property integer checkMaxQty
 * @property integer itemCodeMustInPR
 * @property integer op_OnOpenPopUpYN
 * @property integer showInNewRILRQHSE
 * @property string createdUserGroup
 * @property string createdPcID
 * @property string createdUserID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon createdDateTime
 * @property string|\Carbon\Carbon timeStamp
 */
class Company extends Model
{
    //use SoftDeletes;

    public $table = 'companymaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'companySystemID';

    protected $appends = ['logo_url'];
    protected $dates = ['deleted_at'];

    public $fillable = [
        'CompanyID',
        'CompanyName',
        'CompanyNameLocalized',
        'LocalName',
        'MasterLevel',
        'CompanyLevel',
        'masterComapanyID',
        'masterComapanyIDReporting',
        'masterCompanySystemIDReorting',
        'companyShortCode',
        'orgListOrder',
        'orgListSordOrder',
        'sortOrder',
        'listOrder',
        'CompanyAddress',
        'companyCountry',
        'CompanyTelephone',
        'CompanyFax',
        'CompanyEmail',
        'CompanyURL',
        'SubscriptionStarted',
        'SubscriptionUpTo',
        'ContactPerson',
        'ContactPersonTelephone',
        'ContactPersonFax',
        'ContactPersonEmail',
        'registrationNumber',
        'companyLogo',
        'reportingCurrency',
        'localCurrencyID',
        'mainFormName',
        'menuInitialImage',
        'menuInitialSelectedImage',
        'policyItemIssueTollerence',
        'policyAddonPercentage',
        'policyPOAppDayDiff',
        'policyStockAdjWacCurrentYN',
        'policyDepreciationRunDate',
        'isGroup',
        'isAttachementYN',
        'reportingCriteria',
        'reportingCriteriaFormQuery',
        'supplierReportingCriteria',
        'supplierReportingCriteriaFormQuery',
        'supplierPOSavReportingCriteria',
        'supplierPOSavReportingCriteriaFormQuery',
        'supplierPOSpentReportingCriteriaFormQuery',
        'exchangeGainLossGLCodeSystemID',
        'exchangeGainLossGLCode',
        'exchangeLossGLCode',
        'exchangeGainGLCode',
        'exchangeProvisionGLCode',
        'exchangeProvisionGLCodeAR',
        'isApprovalByServiceLine',
        'isApprovalByServiceLineFinance',
        'isTaxYN',
        'isActive',
        'isActiveGroup',
        'showInCombo',
        'allowBackDatedGRV',
        'allowCustomerInvWithoutContractID',
        'checkMaxQty',
        'itemCodeMustInPR',
        'op_OnOpenPopUpYN',
        'showInNewRILRQHSE',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'vatRegisteredYN',
        'jsrsNumber',
        'jsrsExpiryDate',
        'vatRegistratonNumber',
        'isHrmsIntergrated',
        'revenuePercentageForInterCompanyInventoryTransfer',
        'revenuePercentageForInterCompanyAssetTransfer',
        'isHrmsIntergrated',
        'logoPath',
        'qhseApiKey',
        'taxCardNo',
        'timeStamp',
        'group_two',
        'group_type',
        'helpDeskApiKey'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'companySystemID' => 'integer',
        'CompanyID' => 'string',
        'CompanyName' => 'string',
        'CompanyNameLocalized' => 'string',
        'jsrsExpiryDate' => 'datetime',
        'jsrsNumber' => 'string',
        'LocalName' => 'string',
        'MasterLevel' => 'integer',
        'CompanyLevel' => 'integer',
        'masterComapanyID' => 'string',
        'masterComapanyIDReporting' => 'string',
        'masterCompanySystemIDReorting' => 'integer',
        'companyShortCode' => 'string',
        'orgListOrder' => 'integer',
        'orgListSordOrder' => 'integer',
        'sortOrder' => 'integer',
        'listOrder' => 'integer',
        'CompanyAddress' => 'string',
        'companyCountry' => 'string',
        'CompanyTelephone' => 'integer',
        'CompanyFax' => 'integer',
        'CompanyEmail' => 'string',
        'CompanyURL' => 'string',
        'ContactPerson' => 'string',
        'ContactPersonTelephone' => 'integer',
        'ContactPersonFax' => 'integer',
        'ContactPersonEmail' => 'string',
        'registrationNumber' => 'string',
        'companyLogo' => 'string',
        'reportingCurrency' => 'integer',
        'localCurrencyID' => 'integer',
        'mainFormName' => 'string',
        'menuInitialImage' => 'string',
        'menuInitialSelectedImage' => 'string',
        'policyItemIssueTollerence' => 'float',
        'policyAddonPercentage' => 'float',
        'revenuePercentageForInterCompanyInventoryTransfer' => 'float',
        'revenuePercentageForInterCompanyAssetTransfer' => 'float',
        'policyPOAppDayDiff' => 'integer',
        'policyStockAdjWacCurrentYN' => 'integer',
        'policyDepreciationRunDate' => 'integer',
        'isGroup' => 'integer',
        'isAttachementYN' => 'integer',
        'reportingCriteria' => 'string',
        'reportingCriteriaFormQuery' => 'string',
        'supplierReportingCriteria' => 'string',
        'supplierReportingCriteriaFormQuery' => 'string',
        'supplierPOSavReportingCriteria' => 'string',
        'supplierPOSavReportingCriteriaFormQuery' => 'string',
        'supplierPOSpentReportingCriteriaFormQuery' => 'string',
        'exchangeGainLossGLCodeSystemID' => 'integer',
        'exchangeGainLossGLCode' => 'string',
        'exchangeLossGLCode' => 'string',
        'exchangeGainGLCode' => 'string',
        'exchangeProvisionGLCode' => 'string',
        'exchangeProvisionGLCodeAR' => 'string',
        'isApprovalByServiceLine' => 'integer',
        'isApprovalByServiceLineFinance' => 'integer',
        'isTaxYN' => 'integer',
        'isActive' => 'integer',
        'isActiveGroup' => 'integer',
        'showInCombo' => 'integer',
        'allowBackDatedGRV' => 'integer',
        'allowCustomerInvWithoutContractID' => 'integer',
        'checkMaxQty' => 'integer',
        'itemCodeMustInPR' => 'integer',
        'op_OnOpenPopUpYN' => 'integer',
        'showInNewRILRQHSE' => 'integer',
        'group_two' => 'integer',
        'group_type' => 'integer',
        'createdUserGroup' => 'string',
        'createdPcID' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'vatRegisteredYN' => 'integer',
        'vatRegistratonNumber' => 'string',
        'taxCardNo' => 'string',
        'logoPath' => 'string',
        'isHrmsIntergrated' => 'boolean',
        'qhseApiKey' => 'string',
        'helpDeskApiKey' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function getLogoUrlAttribute(){

        $awsPolicy = Helper::checkPolicy($this->masterCompanySystemIDReorting, 50);

        if ($awsPolicy) {
            return Helper::getFileUrlFromS3($this->logoPath);    
        } else {
            return $this->logoPath;
        }
    }

    public function employees(){
        return $this->belongsToMany('App\Models\Employee', 'employeesdepartments','CompanyID','companyId');
    }

    public function subCategory(){
        return $this->hasMany('App\Models\FinanceItemCategorySub', 'financeitemcategorysubassigned', 'companySystemID','companySystemID');
    }

    public function scopeOfSubCategory($query, $type)
    {
        return $query->where('type', $type);
    }

    public function customerAssigned()
    {
        return $this->hasMany('App\Models\CustomerAssigned', 'companySystemID','companySystemID');
    }

    public function child()
    {
        return $this->hasMany(Company::class,'masterCompanySystemIDReorting','companySystemID');
    }

    public function bank(){
        return $this->belongsToMany('App\Models\BankMaster', 'erp_bankassigned','CompanyID','companyID');
    }

    public function chartOfAccountAssigned()
    {
        return $this->hasMany('App\Models\ChartOfAccountsAssigned', 'companySystemID','companySystemID');
    }

    public function localcurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID','currencyID');
    }

    public function reportingcurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'reportingCurrency','currencyID');
    }

    public function country()
    {
        return $this->hasOne('App\Models\CountryMaster', 'countryID', 'companyCountry');
    }

    public function employee_departments(){
        return $this->hasMany('App\Models\EmployeesDepartment', 'companySystemID','companySystemID');
    }

    public function exchange_gl(){
        return $this->belongsTo('App\Models\ChartOfAccount', 'exchangeGainLossGLCodeSystemID','chartOfAccountSystemID');
    }

    public function vat_input_gl(){
        return $this->belongsTo('App\Models\ChartOfAccount', 'vatInputGLCodeSystemID','chartOfAccountSystemID');
    }

    public function vat_output_gl(){
        return $this->belongsTo('App\Models\ChartOfAccount', 'vatOutputGLCodeSystemID','chartOfAccountSystemID');
    }

     public function bank_assigned()
    {
        return $this->hasMany('App\Models\BankAssign', 'companySystemID','companySystemID');
    }

    public function segments()
    {
        return $this->hasMany('App\Models\SegmentMaster', 'companySystemID','companySystemID');
    }

    public function customerCategoryAssigned()
    {
        return $this->hasMany('App\Models\CustomerMasterCategoryAssigned', 'companySystemID','companySystemID');
    }

    public function subsidiary_companies()
    {
        return $this->hasMany('App\Models\Company', 'group_two','companySystemID')->where('group_type', 1);
    }

    public function accosiate_jv_companies()
    {
        return $this->hasMany('App\Models\Company', 'group_two','companySystemID')->whereIn('group_type', [2,3]);
    }

    public function allSubAssociateJVCompanies(){
        return $this->hasMany('App\Models\Company', 'group_two','companySystemID')->whereIn('group_type', [1, 2, 3]);
    }


    public static function getComanyCode($companySystemID)
    {
        $company = Company::find($companySystemID);

        if ($company) {
            return $company->CompanyID;
        } else {
            return null;
        }
    }
}

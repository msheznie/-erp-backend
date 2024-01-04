<?php
/**
 * =============================================
 * -- File Name : CustomerMaster.php
 * -- Project Name : ERP
 * -- Module Name : Customer Master
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * -- Date: 29-January 2020 By: Zakeeul Description: Added new coloumns called customerSecondLanguage, reportTitleSecondLanguage, addressOneSecondLanguage, addressTwoSecondLanguage 
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CustomerMaster
 * @package App\Models
 * @version March 19, 2018, 12:17 pm UTC
 *
 * @property integer primaryCompanySystemID
 * @property string primaryCompanyID
 * @property integer documentSystemID
 * @property string documentID
 * @property integer lastSerialOrder
 * @property string CutomerCode
 * @property string customerShortCode
 * @property integer custGLAccountSystemID
 * @property string custGLaccount
 * @property string CustomerName
 * @property string ReportTitle
 * @property string customerAddress1
 * @property string customerAddress2
 * @property string customerCity
 * @property string customerCountry
 * @property string CustWebsite
 * @property float creditLimit
 * @property integer creditDays
 * @property string customerLogo
 * @property string companyLinkedTo
 * @property integer isCustomerActive
 * @property integer isAllowedQHSE
 * @property integer vatEligible
 * @property string vatNumber
 * @property integer vatPercentage
 * @property integer isSupplierForiegn
 * @property integer approvedYN
 * @property string|\Carbon\Carbon approvedDate
 * @property string approvedComment
 * @property integer confirmedYN
 * @property integer confirmedEmpSystemID
 * @property string confirmedEmpID
 * @property string confirmedEmpName
 * @property string|\Carbon\Carbon confirmedDate
 * @property string createdUserGroup
 * @property string createdUserID
 * @property string|\Carbon\Carbon createdDateTime
 * @property string createdPcID
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon timeStamp
 */
class CustomerMaster extends Model
{
    //use SoftDeletes;

    public $table = 'customermaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'customerCodeSystem';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'primaryCompanySystemID',
        'primaryCompanyID',
        'documentSystemID',
        'documentID',
        'lastSerialOrder',
        'CutomerCode',
        'customerShortCode',
        'custGLAccountSystemID',
        'custGLaccount',
        'custUnbilledAccountSystemID',
        'custUnbilledAccount',
        'CustomerName',
        'ReportTitle',
        'customerAddress1',
        'customerAddress2',
        'customerCity',
        'customerCountry',
        'interCompanyYN',
        'customerCategoryID',
        'CustWebsite',
        'creditLimit',
        'creditDays',
        'customerLogo',
        'companyLinkedTo',
        'companyLinkedToSystemID',
        'isCustomerActive',
        'isAllowedQHSE',
        'vatEligible',
        'vatNumber',
        'vendorCode',
        'vatPercentage',
        'isSupplierForiegn',
        'RollLevForApp_curr',
        'approvedYN',
        'approvedDate',
        'approvedComment',
        'approvedEmpSystemID',
        'approvedEmpID',
        'confirmedYN',
        'confirmedEmpSystemID',
        'confirmedEmpID',
        'confirmedEmpName',
        'confirmedDate',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'createdPcID',
        'modifiedPc',
        'modifiedUser',
        'timeStamp',
        'refferedBackYN',
        'timesReferred',
        'customerSecondLanguage',
        'reportTitleSecondLanguage',
        'addressOneSecondLanguage',
        'addressTwoSecondLanguage',
        'consignee_name',
        'consignee_address',
        'payment_terms',
        'customer_registration_no',
        'customer_registration_expiry_date',
        'consignee_contact_no'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'customerCodeSystem' => 'integer',
        'primaryCompanySystemID' => 'integer',
        'primaryCompanyID' => 'string',
        'documentSystemID' => 'integer',
        'customerCategoryID' => 'integer',
        'documentID' => 'string',
        'lastSerialOrder' => 'integer',
        'CutomerCode' => 'string',
        'customerShortCode' => 'string',
        'custGLAccountSystemID' => 'integer',
        'custGLaccount' => 'string',
        'custUnbilledAccountSystemID' => 'integer',
        'custUnbilledAccount' => 'string',
        'CustomerName' => 'string',
        'ReportTitle' => 'string',
        'customerAddress1' => 'string',
        'customerAddress2' => 'string',
        'customerCity' => 'string',
        'customerCountry' => 'string',
        'CustWebsite' => 'string',
        'creditLimit' => 'float',
        'creditDays' => 'integer',
        'customerLogo' => 'string',
        'companyLinkedTo' => 'string',
        'companyLinkedToSystemID' => 'integer',
        'interCompanyYN' => 'integer',
        'isCustomerActive' => 'integer',
        'isAllowedQHSE' => 'integer',
        'vatEligible' => 'integer',
        'vatNumber' => 'string',
        'vendorCode' => 'string',
        'vatPercentage' => 'integer',
        'isSupplierForiegn' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'approvedYN' => 'integer',
        'approvedComment' => 'string',
        'approvedEmpSystemID' => 'integer',
        'approvedEmpID' => 'string',
        'confirmedYN' => 'integer',
        'confirmedEmpSystemID' => 'integer',
        'confirmedEmpID' => 'string',
        'confirmedEmpName' => 'string',
        'createdUserGroup' => 'string',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'customerSecondLanguage' => 'string',
        'reportTitleSecondLanguage' => 'string',
        'addressOneSecondLanguage' => 'string',
        'addressTwoSecondLanguage' => 'string',
        'consignee_name' => 'string',
        'consignee_address' => 'string',
        'payment_terms' => 'string',
        'customer_registration_no' => 'string',
        'customer_registration_expiry_date' => 'string',
        'consignee_contact_no' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function company() {
        return $this->belongsTo('App\Models\Company','primaryCompanySystemID','companySystemID');
    }

    public function country(){
        return $this->belongsTo('App\Models\CountryMaster','customerCountry','countryID');
    }

    public function finalApprovedBy()
    {
        return $this->belongsTo('App\Models\Employee','approvedEmpSystemID','employeeSystemID');
    }

    public function gl_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount','custGLAccountSystemID','chartOfAccountSystemID');
    }

    public function unbilled_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount','custUnbilledAccountSystemID','chartOfAccountSystemID');
    }


    public function customerCurrency(){
        return $this->hasMany('App\Models\CustomerCurrency','customerCodeSystem','customerCodeSystem');
    }

    public function customer_default_currency(){
        return $this->hasOne('App\Models\CustomerCurrency','customerCodeSystem','customerCodeSystem');
    }

    public function customer_default_contacts(){
        return $this->hasOne('App\Models\CustomerContactDetails','customerID','customerCodeSystem');
    }

    public function customer_contacts(){
        return $this->hasMany('App\Models\CustomerContactDetails','customerID','customerCodeSystem');
    }

}

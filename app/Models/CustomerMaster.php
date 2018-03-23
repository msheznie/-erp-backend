<?php

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
        'CustomerName',
        'ReportTitle',
        'customerAddress1',
        'customerAddress2',
        'customerCity',
        'customerCountry',
        'CustWebsite',
        'creditLimit',
        'creditDays',
        'customerLogo',
        'companyLinkedTo',
        'isCustomerActive',
        'isAllowedQHSE',
        'vatEligible',
        'vatNumber',
        'vatPercentage',
        'isSupplierForiegn',
        'approvedYN',
        'approvedDate',
        'approvedComment',
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
        'timeStamp'
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
        'documentID' => 'string',
        'lastSerialOrder' => 'integer',
        'CutomerCode' => 'string',
        'customerShortCode' => 'string',
        'custGLAccountSystemID' => 'integer',
        'custGLaccount' => 'string',
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
        'isCustomerActive' => 'integer',
        'isAllowedQHSE' => 'integer',
        'vatEligible' => 'integer',
        'vatNumber' => 'string',
        'vatPercentage' => 'integer',
        'isSupplierForiegn' => 'integer',
        'approvedYN' => 'integer',
        'approvedComment' => 'string',
        'confirmedYN' => 'integer',
        'confirmedEmpSystemID' => 'integer',
        'confirmedEmpID' => 'string',
        'confirmedEmpName' => 'string',
        'createdUserGroup' => 'string',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
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

    public function country(){
        return $this->belongsTo('App\Models\CountryMaster','customerCountry','countryID');
    }
}

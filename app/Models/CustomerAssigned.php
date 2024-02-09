<?php
/**
 * =============================================
 * -- File Name : CustomerAssigned.php
 * -- Project Name : ERP
 * -- Module Name : Customer Assigned
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CustomerAssigned
 * @package App\Models
 * @version March 20, 2018, 11:55 am UTC
 *
 * @property integer companySystemID
 * @property string companyID
 * @property integer customerCodeSystem
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
 * @property integer isRelatedPartyYN
 * @property integer isActive
 * @property integer isAssigned
 * @property integer vatEligible
 * @property string vatNumber
 * @property integer vatPercentage
 * @property string|\Carbon\Carbon timeStamp
 */
class CustomerAssigned extends Model
{
    // use SoftDeletes;

    public $table = 'customerassigned';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey  = 'customerAssignedID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'companySystemID',
        'companyID',
        'customerCodeSystem',
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
        'customerCategoryID',
        'customerCity',
        'customerCountry',
        'CustWebsite',
        'creditLimit',
        'creditDays',
        'consignee_name',
        'consignee_address',
        'payment_terms',
        'consignee_contact_no',
        'isRelatedPartyYN',
        'isActive',
        'isAssigned',
        'vatEligible',
        'vatNumber',
        'vendorCode',
        'vatPercentage',
        'timeStamp',
        'custAdvanceAccountSystemID',
        'custAdvanceAccount',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'customerAssignedID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'customerCodeSystem' => 'integer',
        'CutomerCode' => 'string',
        'customerShortCode' => 'string',
        'custGLAccountSystemID' => 'integer',
        'customerCategoryID' => 'integer',
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
        'isRelatedPartyYN' => 'integer',
        'isActive' => 'integer',
        'isAssigned' => 'integer',
        'vatEligible' => 'integer',
        'vatNumber' => 'string',
        'vendorCode' => 'string',
        'vatPercentage' => 'integer'
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
        return $query->where('companySystemID',  $type);
    }

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
    public function country(){
        return $this->belongsTo('App\Models\CountryMaster','customerCountry','countryID');
    }
    public function customer_master(){
        return $this->belongsTo('App\Models\CustomerMaster','customerCodeSystem','customerCodeSystem');
    }


}

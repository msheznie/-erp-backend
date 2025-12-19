<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BankAccount
 * @package App\Models
 * @version March 30, 2018, 9:40 am UTC
 *
 * @property integer bankAssignedAutoID
 * @property integer bankmasterAutoID
 * @property string companyID
 * @property string bankShortCode
 * @property string bankName
 * @property string bankBranch
 * @property string BranchCode
 * @property string BranchAddress
 * @property string BranchContactPerson
 * @property string BranchTel
 * @property string BranchFax
 * @property string BranchEmail
 * @property string AccountNo
 * @property integer accountCurrencyID
 * @property string accountSwiftCode
 * @property string accountIBAN#
 * @property integer chqueManualStartingNo
 * @property integer isManualActive
 * @property integer chquePrintedStartingNo
 * @property integer isPrintedActive
 * @property string glCodeLinked
 * @property string extraNote
 * @property integer isAccountActive
 * @property integer isDefault
 * @property integer approvedYN
 * @property string approvedByEmpID
 * @property string approvedEmpName
 * @property string|\Carbon\Carbon approvedDate
 * @property string approvedComments
 * @property string|\Carbon\Carbon createdDateTime
 * @property string createdEmpID
 * @property string createdPCID
 * @property string modifedDateTime
 * @property string modifiedByEmpID
 * @property string modifiedPCID
 * @property string|\Carbon\Carbon timeStamp
 */
class BankAccount extends Model
{
    //use SoftDeletes;

    public $table = 'erp_bankaccount';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';


    protected $dates = ['deleted_at'];

    protected $primaryKey = 'bankAccountAutoID';


    public $fillable = [
        'bankAssignedAutoID',
        'bankmasterAutoID',
        'companySystemID',
        'companyID',
        'bankShortCode',
        'bankName',
        'bankBranch',
        'BranchCode',
        'BranchAddress',
        'BranchContactPerson',
        'BranchTel',
        'BranchFax',
        'BranchEmail',
        'AccountNo',
        'AccountName',
        'accountCurrencyID',
        'accountSwiftCode',
        'accountIBAN#',
        'chqueManualStartingNo',
        'isManualActive',
        'chquePrintedStartingNo',
        'isPrintedActive',
        'chartOfAccountSystemID',
        'glCodeLinked',
        'extraNote',
        'isAccountActive',
        'isDefault',
        'approvedYN',
        'approvedByEmpID',
        'approvedEmpName',
        'approvedDate',
        'approvedComments',
        'createdDateTime',
        'createdEmpID',
        'createdPCID',
        'modifedDateTime',
        'modifiedByEmpID',
        'modifiedPCID',
        'timeStamp',
        'approvedByUserSystemID',
        'createdUserSystemID' ,
        'modifiedUserSystemID',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'RollLevForApp_curr',
        'documentSystemID',
        'documentID',
        'isTempBank',
        'refferedBackYN',
        'timesReferred'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'bankAccountAutoID' => 'integer',
        'bankAssignedAutoID' => 'integer',
        'bankmasterAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'bankShortCode' => 'string',
        'bankName' => 'string',
        'bankBranch' => 'string',
        'BranchCode' => 'string',
        'BranchAddress' => 'string',
        'BranchContactPerson' => 'string',
        'BranchTel' => 'string',
        'BranchFax' => 'string',
        'BranchEmail' => 'string',
        'AccountNo' => 'string',
        'AccountName' => 'string',
        'accountCurrencyID' => 'integer',
        'accountSwiftCode' => 'string',
        'accountIBAN#' => 'string',
        'chqueManualStartingNo' => 'integer',
        'isManualActive' => 'integer',
        'chquePrintedStartingNo' => 'integer',
        'isPrintedActive' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'glCodeLinked' => 'string',
        'extraNote' => 'string',
        'isAccountActive' => 'integer',
        'isDefault' => 'integer',
        'approvedYN' => 'integer',
        'approvedByEmpID' => 'string',
        'approvedEmpName' => 'string',
        'approvedComments' => 'string',
        'createdEmpID' => 'string',
        'createdPCID' => 'string',
        'modifedDateTime' => 'string',
        'modifiedByEmpID' => 'string',
        'modifiedPCID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'createdUserSystemID' => 'integer',
        'modifiedUserSystemID' => 'integer',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName'  => 'string',
        'confirmedDate'    => 'string',
        'RollLevForApp_curr' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'refferedBackYN' => 'integer',
        'isTempBank' => 'integer',
        'timesReferred' => 'integer'

    ];

    /**
     * Scope a query to only include active bankaccount.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeIsActive($query)
    {
        return $query->where('isAccountActive',  1);
    }

    public function scopeIsApprove($query)
    {
        return $query->where('approvedYN',  1);
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
        return $this->belongsTo('App\Models\CurrencyMaster', 'accountCurrencyID','currencyID');
    }

     public function bank()
    {
        return $this->belongsTo('App\Models\BankMaster', 'bankmasterAutoID','bankmasterAutoID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function chart_of_account()
    {
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','bankAccountAutoID');
    }
    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }
}

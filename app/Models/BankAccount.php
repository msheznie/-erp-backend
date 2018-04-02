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
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'bankAssignedAutoID',
        'bankmasterAutoID',
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
        'accountCurrencyID',
        'accountSwiftCode',
        'accountIBAN#',
        'chqueManualStartingNo',
        'isManualActive',
        'chquePrintedStartingNo',
        'isPrintedActive',
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
        'timeStamp'
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
        'accountCurrencyID' => 'integer',
        'accountSwiftCode' => 'string',
        'accountIBAN#' => 'string',
        'chqueManualStartingNo' => 'integer',
        'isManualActive' => 'integer',
        'chquePrintedStartingNo' => 'integer',
        'isPrintedActive' => 'integer',
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
        'modifiedPCID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}

<?php
/**
 * =============================================
 * -- File Name : ChartOfAccount.php
 * -- Project Name : ERP
 * -- Module Name : Chart Of Account
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ChartOfAccount
 * @package App\Models
 * @version February 27, 2018, 9:57 am UTC
 *
 * @property integer documentSystemID
 * @property string documentID
 * @property string AccountCode
 * @property string AccountDescription
 * @property string masterAccount
 * @property string catogaryBLorPL
 * @property integer controllAccountYN
 * @property string controlAccounts
 * @property integer isApproved
 * @property string approvedBy
 * @property string|\Carbon\Carbon approvedDate
 * @property string approvedComment
 * @property integer isActive
 * @property integer isBank
 * @property integer AllocationID
 * @property integer relatedPartyYN
 * @property string interCompanyID
 * @property string createdPcID
 * @property string createdUserGroup
 * @property string createdUserID
 * @property string|\Carbon\Carbon createdDateTime
 * @property string modifiedPc
 * @property string modifiedUser
 * @property string|\Carbon\Carbon timestamp
 */
class ChartOfAccount extends Model
{
    //use SoftDeletes;

    public $table = 'chartofaccounts';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'chartOfAccountSystemID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'documentSystemID',
        'primaryCompanySystemID',
        'primaryCompanyID',
        'documentID',
        'AccountCode',
        'AccountDescription',
        'masterAccount',
        'catogaryBLorPLID',
        'catogaryBLorPL',
        'controllAccountYN',
        'controlAccountsSystemID',
        'controlAccounts',
        'isApproved',
        'approvedBy',
        'approvedDate',
        'approvedComment',
        'approvedBySystemID',
        'isActive',
        'RollLevForApp_curr',
        'isBank',
        'AllocationID',
        'relatedPartyYN',
        'interCompanySystemID',
        'interCompanyID',
        'confirmedYN',
        'confirmedEmpSystemID',
        'confirmedEmpID',
        'confirmedEmpName',
        'confirmedEmpDate',
        'createdPcID',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'modifiedPc',
        'modifiedUser',
        'timestamp',
        'refferedBackYN',
        'reportTemplateCategory',
        'isMasterAccount',
        'timesReferred',
         'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'chartOfAccountSystemID' => 'integer',
        'primaryCompanySystemID' => 'integer',
        'reportTemplateCategory' => 'integer',
        'primaryCompanyID' => 'string',
        'documentSystemID' => 'integer',
        'isMasterAccount' => 'integer',
        'documentID' => 'string',
        'AccountCode' => 'string',
        'AccountDescription' => 'string',
        'masterAccount' => 'string',
        'catogaryBLorPLID' => 'integer',
        'catogaryBLorPL' => 'string',
        'controllAccountYN' => 'integer',
        'controlAccountsSystemID' => 'integer',
        'controlAccounts' => 'string',
        'isApproved' => 'integer',
        'approvedBy' => 'string',
        'approvedBySystemID' => 'integer',
        'approvedComment' => 'string',
        'RollLevForApp_curr' => 'integer',
        'isActive' => 'integer',
        'isBank' => 'integer',
        'AllocationID' => 'integer',
        'relatedPartyYN' => 'integer',
        'interCompanySystemID' => 'integer',
        'interCompanyID' => 'string',
        'confirmedYN' => 'integer',
        'confirmedEmpSystemID' => 'integer',
        'confirmedEmpID' => 'string',
        'confirmedEmpName' => 'string',
        'createdPcID' => 'string',
        'createdUserGroup' => 'string',
        'createdUserID' => 'string',
        'modifiedPc' => 'string',
        'modifiedUser' => 'string',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function controlAccount()
    {
        /** one control account  can have many chart of accounts */
        return $this->belongsTo('App\Models\ControlAccount', 'controlAccountsSystemID', 'controlAccountsSystemID');
    }

    public function accountType()
    {
        /** one Account Type can related to many chart of accounts */
        return $this->belongsTo('App\Models\AccountsType', 'catogaryBLorPLID', 'accountsType');
    }

    public function finalApprovedBy()
    {
        return $this->belongsTo('App\Models\Employee','approvedBySystemID','employeeSystemID');
    }

    public function templatelink()
    {
        return $this->belongsTo('App\Models\ReportTemplateLinks','chartOfAccountSystemID','glAutoID');
    }

    public function allocation()
    {
        return $this->belongsTo('App\Models\AllocationMaster','AllocationID','AutoID');
    }

    public function templateCategoryDetails()
    {
        return $this->belongsTo('App\Models\ReportTemplateDetails','reportTemplateCategory','detID');
    }

    public function template_details()
    {
        return $this->belongsTo('App\Models\ReportTemplateDetails','chartOfAccountSystemID','detID');
    }

    public function chartofaccount_assigned(){
        return $this->hasOne('App\Models\ChartOfAccountsAssigned','chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public function generalLedger(){
        return $this->hasMany('App\Models\GeneralLedger','chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public static function checkAccountCode($AccountCode, $chartOfAccountSystemID)
    {
        $checkAccountCode = ChartOfAccount::where('AccountCode', $AccountCode);

        if (!is_null($chartOfAccountSystemID)) {
            $checkAccountCode = $checkAccountCode->where('chartOfAccountSystemID', '!=', $chartOfAccountSystemID);            
        }

        return $checkAccountCode->first();
    }

    public static function getAccountCode($chartOfAccountSystemID)
    {
        $checkAccountCode = ChartOfAccount::where('chartOfAccountSystemID', $chartOfAccountSystemID)->first();


        return ($checkAccountCode) ? $checkAccountCode->AccountCode : "";
    }

    public static function getAccountDescription($chartOfAccountSystemID)
    {
        $checkAccountCode = ChartOfAccount::where('chartOfAccountSystemID', $chartOfAccountSystemID)->first();


        return ($checkAccountCode) ? $checkAccountCode->AccountDescription : "";
    }

    public static function getGlAccountType($chartOfAccountSystemID) {
        $checkAccountCode = ChartOfAccount::where('chartOfAccountSystemID', $chartOfAccountSystemID)->first();
        return ($checkAccountCode) ? $checkAccountCode->catogaryBLorPL : "";

    }


    public static function getGlAccountTypeID($chartOfAccountSystemID) {
        $checkAccountCode = ChartOfAccount::where('chartOfAccountSystemID', $chartOfAccountSystemID)->first();
        return ($checkAccountCode) ? $checkAccountCode->catogaryBLorPLID : "";

    }

    public static function getGlAccountCode($chartOfAccountSystemID) {
        $checkAccountCode = ChartOfAccount::where('chartOfAccountSystemID', $chartOfAccountSystemID)->first();
        return ($checkAccountCode) ? $checkAccountCode->AccountCode : "";

    }

    public function primaryCompany()
    {
        return $this->belongsTo('App\Models\Company', 'primaryCompanySystemID', 'companySystemID');
    }
}

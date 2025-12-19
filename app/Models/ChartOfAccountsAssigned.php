<?php
/**
 * =============================================
 * -- File Name : ChartOfAccountsAssigned.php
 * -- Project Name : ERP
 * -- Module Name : Chart Of Accounts Assigned
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ChartOfAccountsAssigned
 * @package App\Models
 * @version March 27, 2018, 8:52 am UTC
 *
 * @property integer chartOfAccountSystemID
 * @property string AccountCode
 * @property string AccountDescription
 * @property string masterAccount
 * @property integer catogaryBLorPLID
 * @property string catogaryBLorPL
 * @property integer controllAccountYN
 * @property integer controlAccountsSystemID
 * @property string controlAccounts
 * @property integer companySystemID
 * @property string companyID
 * @property integer isActive
 * @property integer isAssigned
 * @property integer isBank
 * @property integer AllocationID
 * @property integer relatedPartyYN
 * @property string|\Carbon\Carbon timeStamp
 */
class ChartOfAccountsAssigned extends Model
{
    //use SoftDeletes;

    public $table = 'chartofaccountsassigned';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';


    protected $dates = ['deleted_at'];
    protected $primaryKey = 'chartOfAccountsAssignedID';

    public $fillable = [
        'chartOfAccountSystemID',
        'AccountCode',
        'AccountDescription',
        'masterAccount',
        'catogaryBLorPLID',
        'catogaryBLorPL',
        'controllAccountYN',
        'controlAccountsSystemID',
        'controlAccounts',
        'companySystemID',
        'companyID',
        'isActive',
        'isAssigned',
        'isBank',
        'AllocationID',
        'relatedPartyYN',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'chartOfAccountsAssignedID' => 'integer',
        'chartOfAccountSystemID' => 'integer',
        'AccountCode' => 'string',
        'AccountDescription' => 'string',
        'masterAccount' => 'string',
        'catogaryBLorPLID' => 'integer',
        'catogaryBLorPL' => 'string',
        'controllAccountYN' => 'integer',
        'controlAccountsSystemID' => 'integer',
        'controlAccounts' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'isActive' => 'integer',
        'isAssigned' => 'integer',
        'isBank' => 'integer',
        'AllocationID' => 'integer',
        'relatedPartyYN' => 'integer'
    ];

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

    public function chartofaccount(){
        return $this->belongsTo('App\Models\ChartOfAccount','chartOfAccountSystemID','chartOfAccountSystemID');
    }
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

    public function allocation()
    {
        return $this->belongsTo('App\Models\AllocationMaster','AllocationID','AutoID');
    }

     public static function checkCOAAssignedStatus($chartOfAccountSystemID, $companySystemID)
    {
         return ChartOfAccountsAssigned::where('chartOfAccountSystemID', $chartOfAccountSystemID)
                                              ->where('companySystemID', $companySystemID)       
                                              ->where('isAssigned', -1)       
                                              ->where('isActive', 1)
                                              ->first();       
    }


    public function project()
    {
        return $this->hasMany('App\Models\ProjectGlDetail','chartOfAccountSystemID','chartOfAccountSystemID');
    }
}

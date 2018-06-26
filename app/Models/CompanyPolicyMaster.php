<?php
/**
 * =============================================
 * -- File Name : CompanyPolicyMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Approval
 * -- Author : Mubashir
 * -- Create date : 14 - March 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CompanyPolicyMaster
 * @package App\Models
 * @version March 28, 2018, 9:04 am UTC
 *
 * @property integer companyPolicyCategoryID
 * @property integer companySystemID
 * @property string companyID
 * @property string documentID
 * @property integer isYesNO
 * @property integer policyValue
 * @property string createdByUserID
 * @property string createdByUserName
 * @property string createdByPCID
 * @property string modifiedByUserID
 * @property string|\Carbon\Carbon createdDateTime
 * @property string|\Carbon\Carbon timestamp
 */
class CompanyPolicyMaster extends Model
{
    //use SoftDeletes;

    public $table = 'erp_companypolicymaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'companyPolicyMasterAutoID';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'companyPolicyCategoryID',
        'companySystemID',
        'companyID',
        'documentID',
        'isYesNO',
        'policyValue',
        'createdByUserID',
        'createdByUserName',
        'createdByPCID',
        'modifiedByUserID',
        'createdDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'companyPolicyMasterAutoID' => 'integer',
        'companyPolicyCategoryID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentID' => 'string',
        'isYesNO' => 'integer',
        'policyValue' => 'integer',
        'createdByUserID' => 'string',
        'createdByUserName' => 'string',
        'createdByPCID' => 'string',
        'modifiedByUserID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function company(){
        return $this->belongsTo('App\Models\Company', 'companySystemID','companySystemID');
    }

    public function policyCategory(){
        return $this->belongsTo('App\Models\CompanyPolicyCategory', 'companyPolicyCategoryID','companyPolicyCategoryID');
    }

    
}

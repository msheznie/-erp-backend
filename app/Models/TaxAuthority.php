<?php
/**
 * =============================================
 * -- File Name : TaxAuthority.php
 * -- Project Name : ERP
 * -- Module Name :  Tax Setup
 * -- Author : Mubashir
 * -- Create date : 23 - April 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TaxAuthority
 * @package App\Models
 * @version April 19, 2018, 5:02 am UTC
 *
 * @property string authoritySystemCode
 * @property string authoritySecondaryCode
 * @property integer serialNo
 * @property string AuthorityName
 * @property integer currencyID
 * @property string telephone
 * @property string email
 * @property string fax
 * @property string address
 * @property integer taxPayableGLAutoID
 * @property integer companySystemID
 * @property string companyID
 * @property integer createdUserGroup
 * @property string createdPCID
 * @property string createdUserID
 * @property string createdUserName
 * @property string|\Carbon\Carbon createdDateTime
 * @property string modifiedPCID
 * @property string modifiedUserID
 * @property string modifiedUserName
 * @property string|\Carbon\Carbon modifiedDateTime
 * @property string|\Carbon\Carbon timestamp
 */
class TaxAuthority extends Model
{
    //use SoftDeletes;

    public $table = 'erp_taxauthorithymaster';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';

    protected $primaryKey = "taxAuthourityMasterID";


    protected $dates = ['deleted_at'];


    public $fillable = [
        'authoritySystemCode',
        'authoritySecondaryCode',
        'serialNo',
        'AuthorityName',
        'currencyID',
        'telephone',
        'email',
        'fax',
        'address',
        'taxPayableGLAutoID',
        'companySystemID',
        'companyID',
        'isActive',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdUserName',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'modifiedDateTime',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'taxAuthourityMasterID' => 'integer',
        'authoritySystemCode' => 'string',
        'authoritySecondaryCode' => 'string',
        'serialNo' => 'integer',
        'AuthorityName' => 'string',
        'currencyID' => 'integer',
        'telephone' => 'string',
        'email' => 'string',
        'fax' => 'string',
        'address' => 'string',
        'taxPayableGLAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'isActive' => 'integer',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function tax()
    {
        return $this->hasMany('App\Models\Tax', 'authorityAutoID', 'taxAuthourityMasterID');
    }


}

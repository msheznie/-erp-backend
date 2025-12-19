<?php
/**
 * =============================================
 * -- File Name : UserGroupAssign.php
 * -- Project Name : ERP
 * -- Module Name : User Group Assign
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserGroupAssign
 * @package App\Models
 * @version March 20, 2018, 4:57 am UTC
 *
 * @property integer userGroupID
 * @property string companyID
 * @property integer navigationMenuID
 * @property string description
 * @property integer masterID
 * @property string url
 * @property string pageID
 * @property string pageTitle
 * @property string pageIcon
 * @property integer levelNo
 * @property integer sortOrder
 * @property integer isSubExist
 * @property boolean readonly
 * @property boolean create
 * @property boolean update
 * @property boolean delete
 * @property boolean print
 * @property string|\Carbon\Carbon timestamp
 */
class UserGroupAssign extends Model
{
    //use SoftDeletes;

    public $table = 'srp_erp_navigationusergroupsetup';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'userGroupID',
        'companyID',
        'navigationMenuID',
        'description',
        'masterID',
        'url',
        'pageID',
        'pageTitle',
        'pageIcon',
        'levelNo',
        'sortOrder',
        'isSubExist',
        'readonly',
        'create',
        'update',
        'delete',
        'print',
        'export',
        'timestamp',
        'isPortalYN',
        'externalLink',
        'isActive'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'userGroupID' => 'integer',
        'companyID' => 'integer',
        'navigationMenuID' => 'integer',
        'description' => 'string',
        'masterID' => 'integer',
        'url' => 'string',
        'pageID' => 'string',
        'pageTitle' => 'string',
        'pageIcon' => 'string',
        'levelNo' => 'integer',
        'sortOrder' => 'integer',
        'isSubExist' => 'integer',
        'readonly' => 'boolean',
        'export' => 'boolean',
        'create' => 'boolean',
        'update' => 'boolean',
        'delete' => 'boolean',
        'print' => 'boolean',
        'isPortalYN' => 'integer',
        'externalLink' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}

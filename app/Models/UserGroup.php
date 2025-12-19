<?php
/**
 * =============================================
 * -- File Name : User Group.php
 * -- Project Name : ERP
 * -- Module Name : User Group
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserGroup
 * @package App\Models
 * @version March 16, 2018, 10:03 am UTC
 *
 * @property integer companyID
 * @property string description
 * @property integer isActive
 * @property string|\Carbon\Carbon timestamp
 */
class UserGroup extends Model
{
    //use SoftDeletes;

    public $table = 'srp_erp_usergroups';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'userGroupID';

    protected $dates = ['deleted_at'];


    public $fillable = [
        'companyID',
        'description',
        'isActive',
        'isDeleted',
        'timestamp',
        'defaultYN',
        'delegation_id',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'userGroupID' => 'integer',
        'companyID' => 'integer',
        'description' => 'string',
        'isActive' => 'integer',
        'isDeleted' => 'integer',
        'defaultYN' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function company(){
        return $this->belongsTo('App\Models\Company','companyID','companySystemID');
    }

    public function navigationusergroup(){
        return $this->hasMany('App\Models\NavigationUserGroupSetup','userGroupID','userGroupID');
    }

    public function usergroupemployee(){
        return $this->hasMany('App\Models\EmployeeNavigation','userGroupID','userGroupID');
    }

    public function delegation(){
        return $this->belongsTo('App\Models\Deligation','delegation_id','id');
    }

}

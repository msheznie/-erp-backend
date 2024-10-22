<?php
/**
 * =============================================
 * -- File Name : EmployeeNavigation.php
 * -- Project Name : ERP
 * -- Module Name : Employee Navigation
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class EmployeeNavigation
 * @package App\Models
 * @version February 13, 2018, 8:59 am UTC
 *
 * @property string empID
 * @property integer userGroupID
 * @property integer companyID
 * @property string|\Carbon\Carbon timestamp
 */
class EmployeeNavigation extends Model
{
    //use SoftDeletes;

    public $table = 'srp_erp_employeenavigation';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'empID',
        'userGroupID',
        'employeeSystemID',
        'companyID',
        'timestamp',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'employeeNavigationID' => 'integer',
        'empID' => 'string',
        'employeeSystemID' => 'integer',
        'userGroupID' => 'integer',
        'companyID' => 'integer'
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

    public function usergroup(){
        return $this->belongsTo('App\Models\UserGroup','userGroupID','userGroupID');
    }

    public function employee(){
        return $this->belongsTo('App\Models\Employee','employeeSystemID','employeeSystemID');
    }



    
}

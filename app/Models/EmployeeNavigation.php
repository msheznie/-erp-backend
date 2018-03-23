<?php

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
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    protected $dates = ['deleted_at'];


    public $fillable = [
        'empID',
        'userGroupID',
        'companyID',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'employeeNavigationID' => 'integer',
        'empID' => 'string',
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

    
}

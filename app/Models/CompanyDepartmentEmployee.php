<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CompanyDepartmentEmployee
 * @package App\Models
 * @version January 2, 2024, 12:00 am UTC
 *
 * @property integer $departmentSystemID
 * @property integer $employeeSystemID
 * @property integer $isHOD
 * @property integer $isActive
 */
class CompanyDepartmentEmployee extends Model
{
    public $table = 'company_departments_employees';
    
    public $primaryKey = 'departmentEmployeeSystemID';

    public $fillable = [
        'departmentSystemID',
        'employeeSystemID',
        'isHOD',
        'isActive'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'departmentEmployeeSystemID' => 'integer',
        'departmentSystemID' => 'integer',
        'employeeSystemID' => 'integer',
        'isHOD' => 'integer',
        'isActive' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'departmentSystemID' => 'required|integer',
        'employeeSystemID' => 'required|integer',
        'isHOD' => 'integer|in:0,1',
        'isActive' => 'integer|in:0,1'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function company()
    {
        return $this->hasOneThrough('App\Models\Company', 'App\Models\CompanyDepartment', 'departmentSystemID', 'companySystemID', 'departmentSystemID', 'companySystemID');
    }

    public function budgetControls()
    {
        return $this->hasMany('App\Models\DepartmentUserBudgetControl', 'departmentEmployeeSystemID', 'departmentEmployeeSystemID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function department()
    {
        return $this->belongsTo('App\Models\CompanyDepartment', 'departmentSystemID', 'departmentSystemID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     **/
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employeeSystemID', 'employeeSystemID');
    }
} 
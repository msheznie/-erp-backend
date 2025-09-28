<?php
/**
 * =============================================
 * -- File Name : CompanyDepartment.php
 * -- Project Name : ERP
 * -- Module Name :  Company Department
 * -- Author : System Generated
 * -- Create date : 18 - December 2024
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CompanyDepartment
 * @package App\Models
 * @version December 18, 2024
 *
 * @property string departmentCode
 * @property string departmentDescription
 * @property integer companySystemID
 * @property integer type
 * @property integer parentDepartmentID
 * @property integer isFinance
 * @property integer isActive

 */
class CompanyDepartment extends Model
{
    public $table = 'company_departments';

    protected $primaryKey = 'departmentSystemID';

    protected $dates = ['deleted_at'];

    public $fillable = [
        'departmentCode',
        'departmentDescription',
        'companySystemID',
        'type',
        'parentDepartmentID',
        'isFinance',
        'isActive',
        'createdUserSystemID',
        'modifiedUserSystemID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'departmentSystemID' => 'integer',
        'departmentCode' => 'string',
        'departmentDescription' => 'string',
        'companySystemID' => 'integer',
        'type' => 'integer',
        'parentDepartmentID' => 'integer',
        'isFinance' => 'integer',
        'isActive' => 'integer',
        'createdUserSystemID' => 'integer',
        'modifiedUserSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'departmentCode' => 'required|unique:company_departments,departmentCode',
        'departmentDescription' => 'required',
        'type' => 'required|in:1,2',
        'parentDepartmentID' => 'required_if:type,2',
        'isActive' => 'required|in:0,1'
    ];

    /**
     * Scope a query to only include active departments.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsActive($query)
    {
        return $query->where('isActive', 1);
    }

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfCompany($query, $type)
    {
        return $query->whereIN('companySystemID', $type);
    }

    protected static function boot()
    {
        parent::boot();
    }

    /**
     * joining the company with departments table.
     */
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function parent()
    {
        return $this->belongsTo('App\Models\CompanyDepartment', 'parentDepartmentID', 'departmentSystemID');
    }

    public function children()
    {
        return $this->hasMany('App\Models\CompanyDepartment', 'parentDepartmentID', 'departmentSystemID')
                    ->with('children');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function employees()
    {
        return $this->hasMany('App\Models\CompanyDepartmentEmployee', 'departmentSystemID', 'departmentSystemID');
    }

    public function hod()
    {
        return $this->belongsTo('App\Models\CompanyDepartmentEmployee', 'departmentSystemID', 'departmentSystemID')->where('isHOD', 1);
    }

    public static function getDepartmentCode($departmentSystemID)
    {
        $department = CompanyDepartment::find($departmentSystemID);
        return ($department) ? $department->departmentCode : null;
    }

    /**
     * Get the grandparent department ID for a given department ID
     * 
     * @param int $departmentSystemID The department ID to find grandparent for
     * @return int|null Returns the grandparent department ID or null if not found
     */
    public static function getGrandParentDepartmentID($departmentSystemID)
    {
        // Find the department
        $department = CompanyDepartment::find($departmentSystemID);
        
        if (!$department) {
            return null;
        }

        // Get the parent department
        $parentDepartment = $department->parent;
        
        if (!$parentDepartment) {
            return null; // No parent, so no grandparent
        }

        // Get the grandparent department (parent of parent)
        $grandParentDepartment = $parentDepartment->parent;
        
        return $grandParentDepartment ? $grandParentDepartment->departmentSystemID : null;
    }

    /**
     * Get the root parent department ID (topmost parent in hierarchy)
     * 
     * @param int $departmentSystemID The department ID to find root parent for
     * @return int|null Returns the root parent department ID or null if not found
     */
    public static function getRootParentDepartmentID($departmentSystemID)
    {
        $currentDepartment = CompanyDepartment::find($departmentSystemID);
        
        if (!$currentDepartment) {
            return null;
        }

        // Traverse up the hierarchy until we find a department with no parent
        while ($currentDepartment && $currentDepartment->parentDepartmentID) {
            $currentDepartment = $currentDepartment->parent;
        }
        
        return $currentDepartment ? $currentDepartment->departmentSystemID : null;
    }
} 
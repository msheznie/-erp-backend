<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class EmployeeNavigationAccess extends Model
{
    use SoftDeletes;

    public $table = 'srp_erp_employeenavigation_access';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $dates = ['deleted_at', 'startDate', 'endDate'];

    public $fillable = [
        'employeeNavigationID',
        'userGroupID',
        'employeeSystemID',
        'companyID',
        'isDelegation',
        'accessType',
        'startDate',
        'endDate',
        'isActive'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'employeeNavigationID' => 'integer',
        'userGroupID' => 'integer',
        'employeeSystemID' => 'integer',
        'companyID' => 'integer',
        'isDelegation' => 'boolean',
        'accessType' => 'string',
        'startDate' => 'date',
        'endDate' => 'date',
        'isActive' => 'boolean'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'employeeNavigationID' => 'required|integer|unique:srp_erp_employeenavigation_access,employeeNavigationID',
        'accessType' => 'required|in:permanent,time_based',
        'startDate' => 'nullable|date|required_if:accessType,time_based',
        'endDate' => 'nullable|date|required_if:accessType,time_based|after_or_equal:startDate',
        'isActive' => 'required|boolean'
    ];

    /**
     * Relationship to EmployeeNavigation
     */
    public function employeeNavigation()
    {
        return $this->belongsTo('App\Models\EmployeeNavigation', 'employeeNavigationID', 'id');
    }

    /**
     * Relationship to UserGroup
     */
    public function userGroup()
    {
        return $this->belongsTo('App\Models\UserGroup', 'userGroupID', 'userGroupID');
    }

    /**
     * Relationship to Employee
     */
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employeeSystemID', 'employeeSystemID');
    }

    /**
     * Relationship to Company
     */
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companyID', 'companySystemID');
    }

    /**
     * Mark access as inactive when EmployeeNavigation is deleted
     *
     * @param EmployeeNavigation $employeeNavigation
     * @return bool
     */
    public static function markAsInactive($employeeNavigation)
    {
        if ($employeeNavigation->access) {
            $employeeNavigation->access->isActive = 0;
            return $employeeNavigation->access->save();
        }
        return false;
    }
}

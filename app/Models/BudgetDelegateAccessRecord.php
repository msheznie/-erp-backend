<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BudgetDelegateAccessRecord
 * @package App\Models
 * @version January 3, 2024, 12:00 am UTC
 *
 * @property integer $id
 * @property integer $budget_planning_detail_id
 * @property integer $delegatee_id
 * @property string $submission_time
 * @property array $access_permissions
 * @property string $status
 * @property integer $created_by
 */
class BudgetDelegateAccessRecord extends Model
{
    public $table = 'dep_budget_pl_delegate_details';
    
    public $primaryKey = 'id';

    // Work Status Constants
    const WORK_STATUS_NOT_STARTED = '1';
    const WORK_STATUS_IN_PROGRESS = '2';
    const WORK_STATUS_SUBMITTED_TO_HOD = '3';
    const WORK_STATUS_APPROVED = '4';
    const WORK_STATUS_REJECT = '5';

    public $fillable = [
        'budget_planning_detail_id',
        'delegatee_id',
        'submission_time',
        'access_permissions',
        'status',
        'work_status',
        'created_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'budget_planning_detail_id' => 'integer',
        'delegatee_id' => 'integer',
        'submission_time' => 'date',
        'access_permissions' => 'array',
        'status' => 'integer',
        'work_status' => 'string',
        'created_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'budget_planning_detail_id' => 'required|integer|exists:department_budget_planning_details,id',
        'delegatee_id' => 'required|integer|exists:company_departments_employees,departmentEmployeeSystemID',
        'submission_time' => 'required|date',
        'access_permissions' => 'required|array',
        'status' => 'required|in:active,inactive,expired',
        'work_status' => 'sometimes|in:1,2,3,4,5'
    ];

    /**
     * Get the budget planning detail that owns the record
     */
    public function budgetPlanningDetail()
    {
        return $this->belongsTo(DepartmentBudgetPlanningDetail::class, 'budget_planning_detail_id', 'id');
    }

    /**
     * Get the delegatee (employee)
     */
    public function delegatee()
    {
        return $this->belongsTo(CompanyDepartmentEmployee::class, 'delegatee_id', 'departmentEmployeeSystemID');
    }

    /**
     * Get the user who created the record
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * Check if delegatee has specific access permission
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        return in_array($permission, $this->access_permissions ?? []);
    }

    /**
     * Get active records for a budget planning detail
     *
     * @param int $budgetPlanningDetailId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getActiveRecords($budgetPlanningDetailId)
    {
        return self::where('budget_planning_detail_id', $budgetPlanningDetailId)
                   ->where('status', 'active')
                   ->with(['delegatee.employee', 'delegatee.department'])
                   ->get();
    }

    //accessor for access_permissions
    public function getAccessPermissionsAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Get the work status label
     *
     * @return string
     */
    public function getWorkStatusLabelAttribute()
    {
        $statuses = [
            self::WORK_STATUS_NOT_STARTED => 'Not Started',
            self::WORK_STATUS_IN_PROGRESS => 'In Progress',
            self::WORK_STATUS_SUBMITTED_TO_HOD => 'Submitted to HOD',
            self::WORK_STATUS_APPROVED => 'Approved',
            self::WORK_STATUS_REJECT => 'Reject'
        ];

        return $statuses[$this->work_status] ?? 'Unknown';
    }

    /**
     * Check if work status is not started
     *
     * @return bool
     */
    public function isNotStarted()
    {
        return $this->work_status === self::WORK_STATUS_NOT_STARTED;
    }

    /**
     * Check if work status is in progress
     *
     * @return bool
     */
    public function isInProgress()
    {
        return $this->work_status === self::WORK_STATUS_IN_PROGRESS;
    }

    /**
     * Check if work status is submitted to HOD
     *
     * @return bool
     */
    public function isSubmittedToHOD()
    {
        return $this->work_status === self::WORK_STATUS_SUBMITTED_TO_HOD;
    }

    /**
     * Check if work status is approved
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->work_status === self::WORK_STATUS_APPROVED;
    }

    /**
     * Check if work status is rejected
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->work_status === self::WORK_STATUS_REJECT;
    }

    /**
     * Get work status badge class for styling
     *
     * @return string
     */
    public function getWorkStatusBadgeClassAttribute()
    {
        $classes = [
            self::WORK_STATUS_NOT_STARTED => 'badge-secondary',
            self::WORK_STATUS_IN_PROGRESS => 'badge-warning',
            self::WORK_STATUS_SUBMITTED_TO_HOD => 'badge-info',
            self::WORK_STATUS_APPROVED => 'badge-success',
            self::WORK_STATUS_REJECT => 'badge-danger'
        ];

        return $classes[$this->work_status] ?? 'badge-secondary';
    }
}

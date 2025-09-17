<?php

namespace App\Models;

use Eloquent as Model;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Schema(
 *      schema="DepartmentBudgetPlanning",
 *      required={""},
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="companyBudgetPlanningID",
 *          description="companyBudgetPlanningID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="departmentID",
 *          description="departmentID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="initiatedDate",
 *          description="initiatedDate",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date"
 *      ),
 *      @OA\Property(
 *          property="periodID",
 *          description="periodID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="yearID",
 *          description="yearID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="typeID",
 *          description="typeID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="submissionDate",
 *          description="submissionDate",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date"
 *      ),
 *      @OA\Property(
 *          property="workflowID",
 *          description="workflowID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="workStatus",
 *          description="Work status of the budget planning (1 - Not Started, 2 - In Progress, 3 - Submitted)",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          enum={"1", "2", "3"}
 *      )
 * )
 */
class DepartmentBudgetPlanning extends Model
{

    public $table = 'department_budget_plannings';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // Work Status Constants
    const WORK_STATUS_NOT_STARTED = '1';
    const WORK_STATUS_IN_PROGRESS = '2';
    const WORK_STATUS_SUBMITTED = '3';




    public $fillable = [
        'companyBudgetPlanningID',
        'departmentID',
        'initiatedDate',
        'periodID',
        'yearID',
        'typeID',
        'submissionDate',
        'workflowID',
        'status',
        'planningCode',
        'workStatus'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'companyBudgetPlanningID' => 'integer',
        'departmentID' => 'integer',
        'initiatedDate' => 'date',
        'periodID' => 'integer',
        'yearID' => 'integer',
        'typeID' => 'integer',
        'submissionDate' => 'date',
        'workflowID' => 'integer',
        'status' => 'integer',
        'planningCode' => 'string',
        'workStatus' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'companyBudgetPlanningID' => 'required',
        'departmentID' => 'required',
        'initiatedDate' => 'required',
        'periodID' => 'required',
        'yearID' => 'required',
        'typeID' => 'required',
        'submissionDate' => 'required',
        'workflowID' => 'required'
    ];

    public function masterBudgetPlannings() {
        return $this->belongsTo(CompanyBudgetPlanning::class, 'companyBudgetPlanningID', 'id');
    }

    public function department() {
        return $this->belongsTo(CompanyDepartment::class, 'departmentID', 'departmentSystemID');
    }

    public function financeYear() {
        return $this->belongsTo(CompanyFinanceYear::class, 'yearID', 'companyFinanceYearID');
    }

    public function workflow() {
        return $this->belongsTo(WorkflowConfiguration::class, 'workflowID', 'id');
    }

    /**
     * Get the time extension requests for the department budget planning.
     */
    public function timeExtensionRequests()
    {
        return $this->hasMany(DeptBudgetPlanningTimeRequest::class, 'department_budget_planning_id');
    }

    public function budgetPlanningDetails()
    {
        return $this->hasMany(DepartmentBudgetPlanningDetail::class, 'department_planning_id');
    }

    public function delegateAccess()
    {
        return $this->hasOne(DepartmentBudgetPlanningsDelegateAccess::class, 'budgetPlanningID')->where('empID', Auth::user()->employee_id);

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
            self::WORK_STATUS_SUBMITTED => 'Submitted'
        ];

        return $statuses[$this->workStatus] ?? 'Unknown';
    }

    /**
     * Check if work status is not started
     *
     * @return bool
     */
    public function isNotStarted()
    {
        return $this->workStatus === self::WORK_STATUS_NOT_STARTED;
    }

    /**
     * Check if work status is in progress
     *
     * @return bool
     */
    public function isInProgress()
    {
        return $this->workStatus === self::WORK_STATUS_IN_PROGRESS;
    }

    /**
     * Check if work status is submitted
     *
     * @return bool
     */
    public function isSubmitted()
    {
        return $this->workStatus === self::WORK_STATUS_SUBMITTED;
    }
}

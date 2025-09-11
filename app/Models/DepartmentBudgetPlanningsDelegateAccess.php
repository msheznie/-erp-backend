<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="DepartmentBudgetPlanningsDelegateAccess",
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
 *          property="empID",
 *          description="Employee ID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="workStatus",
 *          description="Work status of the delegate access (1 - Not Started, 2 - In Progress, 3 - Submitted To HOD)",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          enum={"1", "2", "3"}
 *      ),
 *      @OA\Property(
 *          property="created_by",
 *          description="Created by user ID",
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
 *      )
 * )
 */
class DepartmentBudgetPlanningsDelegateAccess extends Model
{

    public $table = 'department_budget_plannings_delegate_access';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // Work Status Constants
    const WORK_STATUS_NOT_STARTED = '1';
    const WORK_STATUS_IN_PROGRESS = '2';
    const WORK_STATUS_SUBMITTED_TO_HOD = '3';

    public $fillable = [
        'empID',
        'budgetPlanningID',
        'workStatus',
        'created_by'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'empID' => 'integer',
        'budgetPlanningID' => 'integer',
        'workStatus' => 'string',
        'created_by' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'empID' => 'required|integer',
        'budgetPlanningID' => 'required|integer',
        'workStatus' => 'required|in:1,2,3',
        'created_by' => 'nullable|integer'
    ];

    /**
     * Get the employee relationship
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'empID', 'employeeSystemID');
    }

    /**
     * Get the creator relationship
     */
    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by', 'employeeSystemID');
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
            self::WORK_STATUS_SUBMITTED_TO_HOD => 'Submitted To HOD'
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
     * Check if work status is submitted to HOD
     *
     * @return bool
     */
    public function isSubmittedToHOD()
    {
        return $this->workStatus === self::WORK_STATUS_SUBMITTED_TO_HOD;
    }

    /**
     * Get all work status options
     *
     * @return array
     */
    public static function getWorkStatusOptions()
    {
        return [
            self::WORK_STATUS_NOT_STARTED => 'Not Started',
            self::WORK_STATUS_IN_PROGRESS => 'In Progress',
            self::WORK_STATUS_SUBMITTED_TO_HOD => 'Submitted To HOD'
        ];
    }

    /**
     * Get the department budget planning relationship
     */
    public function departmentBudgetPlanning()
    {
        return $this->belongsTo(DepartmentBudgetPlanning::class, 'budgetPlanningID', 'id');
    }

    /**
     * Create or update delegate access record
     *
     * @param array $data
     * @return DepartmentBudgetPlanningsDelegateAccess
     */
    public static function createOrUpdateDelegateAccess($data)
    {
        // Validate required fields
        $validator = \Validator::make($data, self::$rules);
        
        if ($validator->fails()) {
            throw new \Exception('Validation failed: ' . $validator->errors()->first());
        }

        // Check if record already exists
        $existingRecord = self::where('empID', $data['empID'])
            ->where('budgetPlanningID', $data['budgetPlanningID'])
            ->first();

        if ($existingRecord) {
            // Validate work status progression rules
            self::validateWorkStatusProgression($existingRecord->workStatus, $data['workStatus']);
            
            // Update existing record
            $existingRecord->update([
                'workStatus' => $data['workStatus'],
                'created_by' => $data['created_by'] ?? $existingRecord->created_by,
                'updated_at' => now()
            ]);
            
            return $existingRecord;
        } else {
            // Create new record
            return self::create([
                'empID' => $data['empID'],
                'budgetPlanningID' => $data['budgetPlanningID'],
                'workStatus' => $data['workStatus'],
                'created_by' => $data['created_by']
            ]);
        }
    }

    /**
     * Validate work status progression rules
     *
     * @param string $currentStatus
     * @param string $newStatus
     * @throws \Exception
     */
    public static function validateWorkStatusProgression($currentStatus, $newStatus)
    {
        // Define status hierarchy (higher number = more advanced status)
        $statusHierarchy = [
            self::WORK_STATUS_NOT_STARTED => 1,
            self::WORK_STATUS_IN_PROGRESS => 2,
            self::WORK_STATUS_SUBMITTED_TO_HOD => 3
        ];

        $currentLevel = $statusHierarchy[$currentStatus] ?? 0;
        $newLevel = $statusHierarchy[$newStatus] ?? 0;

        // If trying to go backwards in status, throw exception
        if ($newLevel < $currentLevel) {
            $currentLabel = self::getWorkStatusLabel($currentStatus);
            $newLabel = self::getWorkStatusLabel($newStatus);
            
            throw new \Exception("Cannot change work status from '{$currentLabel}' to '{$newLabel}'. Status can only progress forward.");
        }
    }

    /**
     * Get work status label by status value
     *
     * @param string $status
     * @return string
     */
    public static function getWorkStatusLabel($status)
    {
        $statuses = [
            self::WORK_STATUS_NOT_STARTED => 'Not Started',
            self::WORK_STATUS_IN_PROGRESS => 'In Progress',
            self::WORK_STATUS_SUBMITTED_TO_HOD => 'Submitted To HOD'
        ];

        return $statuses[$status] ?? 'Unknown';
    }

}

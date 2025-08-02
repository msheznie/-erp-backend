<?php

namespace App\Models;

use Eloquent as Model;

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
 *      )
 * )
 */
class DepartmentBudgetPlanning extends Model
{

    public $table = 'department_budget_plannings';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




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
        'planningCode'
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
        'planningCode' => 'string'
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

    
}

<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="CompanyBudgetPlanning",
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
 *          property="companySystemID",
 *          description="companySystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="companyID",
 *          description="companyID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
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
class CompanyBudgetPlanning extends Model
{

    public $table = 'company_budget_plannings';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'companySystemID',
        'companyID',
        'initiatedDate',
        'periodID',
        'yearID',
        'typeID',
        'submissionDate',
        'workflowID',
        'status',
        'serialNo',
        'documentSystemID',
        'documentID',
        'planningCode'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'initiatedDate' => 'date',
        'periodID' => 'integer',
        'yearID' => 'integer',
        'typeID' => 'integer',
        'submissionDate' => 'date',
        'workflowID' => 'integer',
        'status' => 'integer',
        'serialNo' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'planningCode' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
    ];

    public function departmentBudgetPlannings() {
        return $this->hasMany(DepartmentBudgetPlanning::class, 'companyBudgetPlanningID', 'id');
    }

    public function financeYear() {
        return $this->belongsTo(CompanyFinanceYear::class, 'yearID', 'companyFinanceYearID');
    }

    public function company() {
        return $this->belongsTo(Company::class, 'companySystemID', 'companySystemID');
    }

    public function workflow() {
        return $this->belongsTo(WorkflowConfiguration::class, 'workflowID', 'id');
    }
}

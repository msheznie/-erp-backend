<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="HrEmpDepartments",
 *      required={""},
 *      @OA\Property(
 *          property="AcademicYearID",
 *          description="AcademicYearID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="BranchID",
 *          description="BranchID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="CreatedDate",
 *          description="CreatedDate",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="CreatedPC",
 *          description="CreatedPC",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="CreatedUserName",
 *          description="CreatedUserName",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="date_from",
 *          description="date_from",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="date_to",
 *          description="date_to",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="DepartmentMasterID",
 *          description="DepartmentMasterID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="EmpDepartmentID",
 *          description="EmpDepartmentID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="EmpID",
 *          description="EmpID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="Erp_companyID",
 *          description="Erp_companyID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="isActive",
 *          description="isActive",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="isPrimary",
 *          description="isPrimary",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="ModifiedPC",
 *          description="ModifiedPC",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="ModifiedUserName",
 *          description="ModifiedUserName",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="SchMasterID",
 *          description="SchMasterID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="Timestamp",
 *          description="Timestamp",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class HrEmpDepartments extends Model
{

    public $table = 'srp_empdepartments';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'AcademicYearID',
        'BranchID',
        'CreatedDate',
        'CreatedPC',
        'CreatedUserName',
        'date_from',
        'date_to',
        'DepartmentMasterID',
        'EmpID',
        'Erp_companyID',
        'isActive',
        'isPrimary',
        'ModifiedPC',
        'ModifiedUserName',
        'SchMasterID',
        'Timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'AcademicYearID' => 'integer',
        'BranchID' => 'integer',
        'CreatedDate' => 'datetime',
        'CreatedPC' => 'string',
        'CreatedUserName' => 'string',
        'date_from' => 'date',
        'date_to' => 'date',
        'DepartmentMasterID' => 'integer',
        'EmpDepartmentID' => 'integer',
        'EmpID' => 'integer',
        'Erp_companyID' => 'integer',
        'isActive' => 'integer',
        'isPrimary' => 'integer',
        'ModifiedPC' => 'string',
        'ModifiedUserName' => 'string',
        'SchMasterID' => 'integer',
        'Timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function departments(){
        return $this->belongsTo(HrDepartmentMaster::class,'DepartmentMasterID','DepartmentMasterID');
    }

    public function employees(){
        return $this->belongsTo(SrpEmployeeDetails::class,'EmpID','EIdNo');
    }
    
}

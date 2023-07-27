<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="HrDepartmentMaster",
 *      required={""},
 *      @OA\Property(
 *          property="BranchID",
 *          description="BranchID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="created_by",
 *          description="created_by",
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
 *          property="DepartmentDes",
 *          description="DepartmentDes",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
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
 *          property="Erp_companyID",
 *          description="Erp_companyID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="hod_id",
 *          description="hod_id",
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
 *          property="SortOrder",
 *          description="SortOrder",
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
class HrDepartmentMaster extends Model
{

    public $table = 'srp_departmentmaster';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'BranchID',
        'created_by',
        'CreatedDate',
        'CreatedPC',
        'CreatedUserName',
        'DepartmentDes',
        'Erp_companyID',
        'hod_id',
        'isActive',
        'ModifiedPC',
        'ModifiedUserName',
        'SchMasterID',
        'SortOrder',
        'Timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'BranchID' => 'integer',
        'created_by' => 'integer',
        'CreatedDate' => 'datetime',
        'CreatedPC' => 'string',
        'CreatedUserName' => 'string',
        'DepartmentDes' => 'string',
        'DepartmentMasterID' => 'integer',
        'Erp_companyID' => 'integer',
        'hod_id' => 'integer',
        'isActive' => 'integer',
        'ModifiedPC' => 'string',
        'ModifiedUserName' => 'string',
        'SchMasterID' => 'integer',
        'SortOrder' => 'integer',
        'Timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}

<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="HRDocumentApproved",
 *      required={""},
 *      @OA\Property(
 *          property="documentApprovedID",
 *          description="documentApprovedID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="wareHouseAutoID",
 *          description="wareHouseAutoID",
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
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="documentID",
 *          description="documentID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="documentCode",
 *          description="documentCode",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="isCancel",
 *          description="Approvals for canceling a document",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="documentDate",
 *          description="documentDate",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="approvalLevelID",
 *          description="approvalLevelID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="isReverseApplicableYN",
 *          description="0 - No 1- Yes",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="roleID",
 *          description="roleID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="leaveSetupID",
 *          description="Fk => srp_erp_leaveapprovalsetup.approvalSetupID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="approvalGroupID",
 *          description="approvalGroupID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="roleLevelOrder",
 *          description="roleLevelOrder",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="docConfirmedDate",
 *          description="docConfirmedDate",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="docConfirmedByEmpID",
 *          description="docConfirmedByEmpID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="table_name",
 *          description="table_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="table_unique_field_name",
 *          description="table_unique_field_name",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="approvedEmpID",
 *          description="approvedEmpID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="approvedDate",
 *          description="approvedDate",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="approvedComments",
 *          description="approvedComments",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="approvedPC",
 *          description="approvedPC",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="companyID",
 *          description="companyID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="companyCode",
 *          description="companyCode",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="timeStamp",
 *          description="timeStamp",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="is_sync",
 *          description="is_sync",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="id_store",
 *          description="id_store",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class HRDocumentApproved extends Model
{

    public $table = 'srp_erp_documentapproved';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';




    public $fillable = [
        'wareHouseAutoID',
        'departmentID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'isCancel',
        'documentDate',
        'approvalLevelID',
        'isReverseApplicableYN',
        'roleID',
        'leaveSetupID',
        'approvalGroupID',
        'roleLevelOrder',
        'docConfirmedDate',
        'docConfirmedByEmpID',
        'table_name',
        'table_unique_field_name',
        'approvedEmpID',
        'approvedYN',
        'approvedDate',
        'approvedComments',
        'approvedPC',
        'companyID',
        'companyCode',
        'timeStamp',
        'is_sync',
        'id_store'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'documentApprovedID' => 'integer',
        'wareHouseAutoID' => 'integer',
        'departmentID' => 'string',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'documentCode' => 'string',
        'isCancel' => 'integer',
        'documentDate' => 'datetime',
        'approvalLevelID' => 'integer',
        'isReverseApplicableYN' => 'integer',
        'roleID' => 'integer',
        'leaveSetupID' => 'integer',
        'approvalGroupID' => 'integer',
        'roleLevelOrder' => 'integer',
        'docConfirmedDate' => 'datetime',
        'docConfirmedByEmpID' => 'string',
        'table_name' => 'string',
        'table_unique_field_name' => 'string',
        'approvedEmpID' => 'string',
        'approvedYN' => 'integer',
        'approvedDate' => 'datetime',
        'approvedComments' => 'string',
        'approvedPC' => 'string',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'timeStamp' => 'datetime',
        'is_sync' => 'integer',
        'id_store' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'id_store' => 'required'
    ];

    
}

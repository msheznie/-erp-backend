<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="HrDeligationDetails",
 *      required={""},
 *      @OA\Property(
 *          property="approval_level",
 *          description="link with srp_erp_approvalusers.levelNo",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="approval_role",
 *          description="fk=>srp_erp_leavesetupsystemapprovaltypes.id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="approval_user_id",
 *          description="fk=>srp_erp_approvalusers.approvalUserID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="comment",
 *          description="comment",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
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
 *          property="delegatee_id",
 *          description="fk=>srp_employeesdetails.EIdNo",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="delegation_id",
 *          description="fk=>deligation.id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="document_id",
 *          description="fk=>srp_erp_documentcodemaster.codeID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="enabled",
 *          description="enabled default 0 enabled 1",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="module_id",
 *          description="fk=>hr_document_module_mapping.id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
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
class HrDeligationDetails extends Model
{

    public $table = 'hr_delegation_details';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'approval_level',
        'approval_role',
        'approval_user_id',
        'comment',
        'delegatee_id',
        'delegation_id',
        'document_id',
        'enabled',
        'module_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'approval_level' => 'integer',
        'approval_role' => 'integer',
        'approval_user_id' => 'integer',
        'comment' => 'string',
        'delegatee_id' => 'integer',
        'delegation_id' => 'integer',
        'document_id' => 'string',
        'enabled' => 'boolean',
        'id' => 'integer',
        'module_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'approval_level' => 'required',
        'approval_role' => 'required',
        'created_at' => 'required',
        'delegation_id' => 'required',
        'document_id' => 'required',
        'enabled' => 'required',
        'module_id' => 'required',
        'updated_at' => 'required'
    ];

    
}

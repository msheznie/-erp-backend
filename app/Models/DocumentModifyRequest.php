<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="DocumentModifyRequest",
 *      required={""},
 *      @OA\Property(
 *          property="approved",
 *          description="approved",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="approved_by_user_system_id",
 *          description="approved_by_user_system_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="approved_date",
 *          description="approved_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
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
 *          property="created_at",
 *          description="created_at",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="document_master_id",
 *          description="document_master_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
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
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="rejected",
 *          description="rejected",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="rejected_by_user_system_id",
 *          description="rejected_by_user_system_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="rejected_date",
 *          description="rejected_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="requested_date",
 *          description="requested_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="requested_document_master_id",
 *          description="requested_document_master_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="requested_employeeSystemID",
 *          description="requested_employeeSystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="status",
 *          description="status",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="type",
 *          description="type",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
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
 *          property="version",
 *          description="version",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class DocumentModifyRequest extends Model
{

    public $table = 'document_modify_request';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'approved',
        'approved_by_user_system_id',
        'approved_date',
        'companySystemID',
        'document_master_id',
        'documentSystemCode',
        'refferedBackYN',
        'rejected_by_user_system_id',
        'rejected_date',
        'requested_date',
        'requested_document_master_id',
        'requested_employeeSystemID',
        'RollLevForApp_curr',
        'status',
        'type',
        'version',
        'description',
        'requested_by_name',
        'code',
        'requested',
        'serial_number',
        'timesReferred',
        'confirmation_date',
        'confirmation_RollLevForApp_curr',
        'confirmation_approved',
        'confirmation_approved_date',
        'confirmation_approved_by_user_system_id',
        'confirmation_rejected',
        'confirmation_rejected_date',
        'confirmation_rejected_by_user_system_id',
        'confirm',
        'modify_type'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'approved' => 'boolean',
        'approved_by_user_system_id' => 'integer',
        'approved_date' => 'datetime',
        'companySystemID' => 'integer',
        'document_master_id' => 'integer',
        'documentSystemCode' => 'integer',
        'id' => 'integer',
        'rejected' => 'boolean',
        'rejected_by_user_system_id' => 'integer',
        'rejected_date' => 'datetime',
        'requested_date' => 'datetime',
        'requested_document_master_id' => 'integer',
        'requested_employeeSystemID' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'status' => 'boolean',
        'type' => 'integer',
        'version' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'approved' => 'required',
        'companySystemID' => 'required',
        'documentSystemCode' => 'required',
        'rejected' => 'required',
        'status' => 'required',
        'type' => 'required'
    ];

    public function documentAttachments(){ 
        return $this->hasMany('App\Models\DocumentAttachments','documentSystemCode','documentSystemCode');
    }

    public function tenderMaster(){ 
        return $this->hasOne('App\Models\TenderMaster','id','documentSystemCode');
    }

    
}

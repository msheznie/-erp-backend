<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @OA\Schema(
 *      schema="Deligation",
 *      required={""},
 *      @OA\Property(
 *          property="modifiedDate",
 *          description="modifiedDate",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="timesReferred",
 *          description="timesReferred",
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
 *          property="id",
 *          description="id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="deligation_id",
 *          description="deligation_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="deligator",
 *          description="deligator",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="description",
 *          description="description",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="deligation_type",
 *          description="deligation_type",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="start_date",
 *          description="start_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date"
 *      ),
 *      @OA\Property(
 *          property="end_date",
 *          description="end_date",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date"
 *      ),
 *      @OA\Property(
 *          property="access_types",
 *          description="access_types",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="departments",
 *          description="departments",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="confirmedByEmpSystemID",
 *          description="confirmedByEmpSystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="confirmedDate",
 *          description="confirmedDate",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date"
 *      ),
 *      @OA\Property(
 *          property="approved",
 *          description="approved",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="approvedByUserSystemID",
 *          description="approvedByUserSystemID",
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
 *          format="date"
 *      ),
 *      @OA\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
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
 *          property="serial_no",
 *          description="serial_no",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="company_id",
 *          description="company_id",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="is_active",
 *          description="is_active",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="boolean"
 *      ),
 *      @OA\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="documentID",
 *          description="documentID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="refferedBackYN",
 *          description="refferedBackYN",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="confirmedByName",
 *          description="confirmedByName",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="confirmedByEmpID",
 *          description="confirmedByEmpID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="approveddByName",
 *          description="approveddByName",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="approvedByEmpID",
 *          description="approvedByEmpID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          readOnly=$FIELD_READ_ONLY$,
 *          nullable=$FIELD_NULLABLE$,
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class Deligation extends Model
{

    public $table = 'deligation';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'modifiedDate',
        'modifiedUserSystemID',
        'timesReferred',
        'companySystemID',
        'deligation_id',
        'deligator',
        'description',
        'deligation_type',
        'start_date',
        'end_date',
        'access_types',
        'departments',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedDate',
        'approved',
        'approvedByUserSystemID',
        'approvedDate',
        'createdUserSystemID',
        'RollLevForApp_curr',
        'serial_no',
        'company_id',
        'is_active',
        'documentSystemID',
        'documentID',
        'refferedBackYN',
        'confirmedByName',
        'confirmedByEmpID',
        'approveddByName',
        'approvedByEmpID',
        'createdUserID',
        'createdDateTime'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'modifiedDate' => 'datetime',
        'modifiedUserSystemID' => 'integer',
        'timesReferred' => 'integer',
        'companySystemID' => 'integer',
        'id' => 'integer',
        'deligation_id' => 'string',
        'deligator' => 'integer',
        'description' => 'string',
        'deligation_type' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'access_types' => 'string',
        'departments' => 'string',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedDate' => 'date',
        'approved' => 'integer',
        'approvedByUserSystemID' => 'integer',
        'approvedDate' => 'date',
        'createdUserSystemID' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'serial_no' => 'integer',
        'company_id' => 'integer',
        'is_active' => 'boolean',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'refferedBackYN' => 'integer',
        'confirmedByName' => 'integer',
        'confirmedByEmpID' => 'integer',
        'approveddByName' => 'integer',
        'approvedByEmpID' => 'integer',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'modifiedUserSystemID' => 'required',
        'companySystemID' => 'required',
        'deligation_id' => 'required',
        'deligator' => 'required',
        'deligation_type' => 'required',
        'start_date' => 'required',
        'end_date' => 'required',
        'access_types' => 'required',
        'departments' => 'required',
        'confirmedYN' => 'required',
        'confirmedByEmpSystemID' => 'required',
        'approved' => 'required',
        'approvedByUserSystemID' => 'required',
        'approvedDate' => 'required',
        'createdUserSystemID' => 'required',
        'RollLevForApp_curr' => 'required',
        'serial_no' => 'required',
        'is_active' => 'required'
    ];

    
}

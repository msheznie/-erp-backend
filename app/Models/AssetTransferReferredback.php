<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AssetTransferReferredback",
 *      required={""},
 *      @SWG\Property(
 *          property="assetTransferMasterRefferedBackID",
 *          description="assetTransferMasterRefferedBackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseRequestCode",
 *          description="purchaseRequestCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="budgetYear",
 *          description="budgetYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="prBelongsYear",
 *          description="prBelongsYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="document_id",
 *          description="document_id",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="document_code",
 *          description="document_code",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="type",
 *          description="type",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="location",
 *          description="location",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="reference_no",
 *          description="reference_no",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="document_date",
 *          description="document_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="approval_comments",
 *          description="approval_comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serial_no",
 *          description="serial_no",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="emp_id",
 *          description="emp_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="narration",
 *          description="narration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="refferedBackYN",
 *          description="refferedBackYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="company_code",
 *          description="company_code",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmed_yn",
 *          description="confirmed_yn",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmed_by_emp_id",
 *          description="confirmed_by_emp_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByName",
 *          description="confirmedByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpID",
 *          description="confirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmed_date",
 *          description="confirmed_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approved_yn",
 *          description="approved_yn",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approved_date",
 *          description="approved_date",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="approved_by_emp_name",
 *          description="approved_by_emp_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approved_by_emp_id",
 *          description="approved_by_emp_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="current_level_no",
 *          description="current_level_no",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_user_id",
 *          description="created_user_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="purchaseRequestID",
 *          description="purchaseRequestID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="updated_user_id",
 *          description="updated_user_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class AssetTransferReferredback extends Model
{

    public $table = 'erp_assettransferreferredback';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'id',
        'serviceLineSystemID',
        'purchaseRequestCode',
        'budgetYear',
        'prBelongsYear',
        'document_id',
        'document_code',
        'type',
        'location',
        'reference_no',
        'document_date',
        'approval_comments',
        'serial_no',
        'emp_id',
        'narration',
        'refferedBackYN',
        'serviceLineCode',
        'company_id',
        'company_code',
        'confirmed_yn',
        'confirmed_by_emp_id',
        'confirmedByName',
        'confirmedByEmpID',
        'confirmed_date',
        'documentSystemID',
        'approved_yn',
        'approved_date',
        'approved_by_emp_name',
        'approved_by_emp_id',
        'current_level_no',
        'timesReferred',
        'created_user_id',
        'purchaseRequestID',
        'updated_user_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'assetTransferMasterRefferedBackID' => 'integer',
        'id' => 'integer',
        'serviceLineSystemID' => 'integer',
        'purchaseRequestCode' => 'string',
        'budgetYear' => 'integer',
        'prBelongsYear' => 'integer',
        'document_id' => 'string',
        'document_code' => 'string',
        'type' => 'integer',
        'location' => 'integer',
        'reference_no' => 'string',
        'document_date' => 'datetime',
        'approval_comments' => 'string',
        'serial_no' => 'integer',
        'emp_id' => 'integer',
        'narration' => 'string',
        'refferedBackYN' => 'integer',
        'serviceLineCode' => 'string',
        'company_id' => 'integer',
        'company_code' => 'string',
        'confirmed_yn' => 'integer',
        'confirmed_by_emp_id' => 'integer',
        'confirmedByName' => 'string',
        'confirmedByEmpID' => 'string',
        'confirmed_date' => 'datetime',
        'documentSystemID' => 'integer',
        'approved_yn' => 'integer',
        'approved_date' => 'datetime',
        'approved_by_emp_name' => 'string',
        'approved_by_emp_id' => 'integer',
        'current_level_no' => 'integer',
        'timesReferred' => 'integer',
        'created_user_id' => 'integer',
        'purchaseRequestID' => 'integer',
        'updated_user_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'id' => 'required'
    ];
    protected $appends = ['document_date_formatted', 'transfer_type'];


    public function getDocumentDateFormattedAttribute(): string
    {
        if (isset($this->attributes['document_date'])) {
            if ($this->attributes['document_date']) {
                return Carbon::parse($this->attributes['document_date'])->format('Y-m-d');
            } else {
                return 'N/A';
            }
        }

        return '';
    }

    public function getTransferTypeAttribute(): string
    {
        if (isset($this->attributes['type'])) {
            if ($this->attributes['type']) {
                return ($this->attributes['type'] == 1) ? 'Request Based Transfer' : 'Direct';
            } else {
                return 'N/A';
            }
        }
        return '';
    }
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id', 'companySystemID');
    }
    public function confirmed_by()
    {
        return $this->hasOne(SrpEmployeeDetails::class, 'EIdNo', 'confirmed_by_emp_id');
    }
    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','id');
    }
    public function detail()
    {
        return $this->hasMany('App\Models\ERPAssetTransferDetail', 'erp_fa_fa_asset_transfer_id', 'id');
    }
    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }
    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'id')->where('documentSystemID',103);
    }
    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'created_user_id', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'updated_user_id', 'employeeSystemID');
    }
    
}

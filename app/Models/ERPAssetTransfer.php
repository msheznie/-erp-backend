<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="ERPAssetTransfer",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          type="boolean"
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
 *          format="date"
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
 *          property="company_id",
 *          description="company_id",
 *          type="integer",
 *          format="int32"
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
 *          property="confirmed_by_name",
 *          description="confirmed_by_name",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmed_date",
 *          description="confirmed_date",
 *          type="string",
 *          format="date-time"
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
 *      )
 * )
 */
class ERPAssetTransfer extends Model
{

    public $table = 'erp_fa_fa_asset_transfer';
    public $primaryKey = 'id';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'document_id',
        'document_code',
        'type',
        'location',
        'budgetYear',
        'prBelongsYear',
        'reference_no',
        'document_date',
        'approval_comments',
        'serial_no',
        'emp_id',
        'narration',
        'company_id',
        'confirmed_yn',
        'confirmed_by_emp_id',
        'confirmedByName',
        'confirmed_date',
        'approved_yn',
        'approved_date',
        'approved_by_emp_name',
        'approved_by_emp_id',
        'current_level_no',
        'created_user_id',
        'confirmedByEmpID',
        'serviceLineSystemID',
        'serviceLineCode',
        'updated_user_id',
        'refferedBackYN',
        'company_code',
        'documentSystemID',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'serviceLineSystemID' => 'integer',
        'documentSystemID' => 'integer',
        'budgetYear' => 'integer',
        'refferedBackYN' => 'integer',
        'prBelongsYear' => 'integer',
        'document_id' => 'string',
        'document_code' => 'string',
        'type' => 'integer',
        'location' => 'integer',
        'reference_no' => 'string',
        'document_date' => 'date',
        'approval_comments' => 'string',
        'serial_no' => 'integer',
        'emp_id' => 'integer',
        'narration' => 'string',
        'serviceLineCode' => 'string',
        'company_id' => 'integer',
        'confirmed_yn' => 'integer',
        'confirmed_by_emp_id' => 'integer',
        'confirmedByName' => 'string',
        'confirmedByEmpID' => 'string',
        'confirmed_date' => 'datetime',
        'approved_yn' => 'integer',
        'approved_date' => 'datetime',
        'approved_by_emp_name' => 'string',
        'approved_by_emp_id' => 'integer',
        'current_level_no' => 'integer',
        'created_user_id' => 'integer',
        'updated_user_id' => 'integer',
        'company_code' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [];
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
        switch ($this->attributes['type']) {
            case 1: return 'Request Based - Employee';
            case 2: return 'Direct to Location';
            case 3: return 'Direct to Employee';
            case 4: return 'Request Based - Department';
            default: return '';

        }
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

    public function scopeLocationJoin($q,$as = 'erp_location', $column = 'LOCATION' , $columnAs = 'locationName')
    {
        return $q->leftJoin('erp_location as '.$as,$as.'.locationID','erp_fa_fa_asset_transfer.'.$column)
        ->addSelect($as.".locationName as ".$columnAs);
    }

    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_fa_fa_asset_transfer.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_fa_fa_asset_transfer_details','erp_fa_fa_asset_transfer_details.erp_fa_fa_asset_transfer_id','erp_fa_fa_asset_transfer.id');
    }


}

<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AssetRequest",
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
class AssetRequest extends Model
{

    public $table = 'erp_fa_fa_asset_request';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'document_id',
        'document_code',
        'document_date',
        'approval_comments',
        'timesReferred',
        'serial_no',
        'emp_id',
        'narration',
        'company_id',
        'confirmed_yn',
        'confirmed_by_emp_id',
        'confirmed_by_name',
        'confirmed_date',
        'approved_yn',
        'approved_date',
        'approved_by_emp_name',
        'approved_by_emp_id',
        'current_level_no',
        'created_user_id',
        'departmentSystemID',
        'type'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'timesReferred' => 'integer',
        'document_id' => 'string',
        'document_code' => 'string',
        'document_date' => 'date',
        'approval_comments' => 'string',
        'serial_no' => 'integer',
        'emp_id' => 'integer',
        'narration' => 'string',
        'company_id' => 'integer',
        'confirmed_yn' => 'integer',
        'confirmed_by_emp_id' => 'integer',
        'confirmed_by_name' => 'string',
        'confirmed_date' => 'datetime',
        'approved_yn' => 'integer',
        'approved_date' => 'datetime',
        'approved_by_emp_name' => 'string',
        'approved_by_emp_id' => 'integer',
        'current_level_no' => 'integer',
        'created_user_id' => 'integer',
        'departmentSystemID'  => 'integer',
        'type'   => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    protected $appends = ['document_date_formatted'];
    
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

    public function employee()
    {
        return $this->hasOne(SrpEmployeeDetails::class, 'EIdNo', 'emp_id');
    }
    public function employeeApproved()
    {
        return $this->hasOne(SrpEmployeeDetails::class, 'EIdNo', 'approved_by_emp_id');
    }
    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'company_id', 'companySystemID');
    }
    public function confirmed_by()
    {
        return $this->hasOne(SrpEmployeeDetails::class, 'EIdNo', 'confirmed_by_emp_id');
    }
    public function approved_by()
    {
        return $this->hasOne(SrpEmployeeDetails::class, 'EIdNo', 'approved_by_emp_id');
    }

    
    public function scopeEmployeeJoin($q,$as = 'srp_employeesdetails' ,$column = 'confirmed_by_emp_id',$columnAs = 'Ename2'){
        $q->leftJoin('srp_employeesdetails as '. $as, $as.'.EIdNo', '=', 'erp_fa_fa_asset_transfer.'.$column)
            ->addSelect($as.".Ename2 as ".$columnAs);
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_fa_fa_asset_request_details','erp_fa_fa_asset_request_details.erp_fa_fa_asset_request_id','erp_fa_fa_asset_request.id');
    }
    public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_fa_fa_asset_transfer.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }

}

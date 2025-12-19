<?php
/**
 * =============================================
 * -- File Name : AssetDisposalMaster.php
 * -- Project Name : ERP
 * -- Module Name :  Asset Management
 * -- Author : Mubashir
 * -- Create date : 27 - September 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */

namespace App\Models;

use App\helper\Helper;
use Awobaz\Compoships\Compoships;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AssetDisposalMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="assetdisposalMasterAutoID",
 *          description="assetdisposalMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="toCompanySystemID",
 *          description="toCompanySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="toCompanyID",
 *          description="toCompanyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="disposalDocumentCode",
 *          description="disposalDocumentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="narration",
 *          description="narration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confimedByEmpSystemID",
 *          description="confimedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confimedByEmpID",
 *          description="confimedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpName",
 *          description="confirmedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="disposalType",
 *          description="disposalType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      )
 * )
 */
class AssetDisposalMaster extends Model
{
    use Compoships;
    public $table = 'erp_fa_asset_disposalmaster';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'assetdisposalMasterAutoID';
    protected $with = ['confirmed_by', 'created_by', 'modified_by', 'confirmed_by'];

    public $fillable = [
        'companySystemID',
        'companyID',
        'toCompanySystemID',
        'toCompanyID',
        'customerID',
        'serialNo',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'documentSystemID',
        'documentID',
        'disposalDocumentCode',
        'disposalDocumentDate',
        'narration',
        'revenuePercentage',
        'vatRegisteredYN',
        'confirmedYN',
        'confimedByEmpSystemID',
        'confimedByEmpID',
        'confirmedByEmpName',
        'confirmedDate',
        'approvedYN',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'disposalType',
        'timesReferred',
        'refferedBackYN',
        'RollLevForApp_curr',
        'createdUserSystemID',
        'createdUserID',
        'createdDateTime',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'assetdisposalMasterAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'toCompanySystemID' => 'integer',
        'toCompanyID' => 'string',
        'customerID' => 'integer',
        'serialNo' => 'integer',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'disposalDocumentCode' => 'string',
        'narration' => 'string',
        'revenuePercentage' => 'float',
        'confirmedYN' => 'integer',
        'confimedByEmpSystemID' => 'integer',
        'confimedByEmpID' => 'string',
        'confirmedByEmpName' => 'string',
        'approvedYN' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'disposalType' => 'integer',
        'timesReferred' => 'integer',
        'refferedBackYN' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    /**
     * Scope a query to only include users of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeOfCompany($query, $type)
    {
        return $query->whereIN('companySystemID', $type);
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'assetdisposalMasterAutoID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confimedByEmpSystemID', 'employeeSystemID');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function disposal_type()
    {
        return $this->belongsTo('App\Models\AssetDisposalType', 'disposalType', 'disposalTypesID');
    }

    public function financeperiod_by()
    {
        return $this->belongsTo('App\Models\CompanyFinancePeriod', 'companyFinancePeriodID', 'companyFinancePeriodID');
    }

    public function financeyear_by()
    {
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function setDisposalDocumentDateAttribute($value)
    {
        $this->attributes['disposalDocumentDate'] = Helper::dateAddTime($value);
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'assetdisposalMasterAutoID')->where('documentSystemID',41);
    }

    public function details()
    {
        return $this->hasMany('App\Models\AssetDisposalDetail', 'assetdisposalMasterAutoID', 'assetdisposalMasterAutoID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function customer(){
        return $this->belongsTo('App\Models\CustomerMaster','customerID','customerCodeSystem');
    }

    public function customerInvoice(){
        return $this->belongsTo('App\Models\CustomerInvoiceDirect','customerInvoiceNo','disposalDocumentCode');
    }

    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_fa_asset_disposalmaster.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_fa_asset_disposalmaster.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }

    public function scopeCustomerJoin($q,$as = 'customermaster', $column = 'customerID' , $columnAs = 'CustomerName')
    {
        return $q->leftJoin('customermaster as '.$as,$as.'.customerCodeSystem','erp_fa_asset_disposalmaster.'.$column)
        ->addSelect($as.".CustomerName as ".$columnAs);
    }

    public function scopeDisposTypeJoin($q,$as = 'erp_fa_asset_disposaltypes', $column = 'disposalType' , $columnAs = 'typeDescription')
    {
        return $q->leftJoin('erp_fa_asset_disposaltypes as '.$as,$as.'.disposalTypesID','erp_fa_asset_disposalmaster.'.$column)
        ->addSelect($as.".typeDescription as ".$columnAs);
    }


}

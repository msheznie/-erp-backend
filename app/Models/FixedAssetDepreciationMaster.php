<?php

namespace App\Models;

use App\helper\Helper;
use Awobaz\Compoships\Compoships;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="FixedAssetDepreciationMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="depMasterAutoID",
 *          description="depMasterAutoID",
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
 *          property="depCode",
 *          description="depCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="depMonthYear",
 *          description="depMonthYear",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="depLocalCur",
 *          description="depLocalCur",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="depAmountLocal",
 *          description="depAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="depRptCur",
 *          description="depRptCur",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="depAmountRpt",
 *          description="depAmountRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpSystemID",
 *          description="confirmedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpID",
 *          description="confirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpName",
 *          description="confirmedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approved",
 *          description="approved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      )
 * )
 */
class FixedAssetDepreciationMaster extends Model
{
    use Compoships;
    public $table = 'erp_fa_depmaster';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'depMasterAutoID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'depCode',
        'depDate',
        'depMonthYear',
        'depLocalCur',
        'depAmountLocal',
        'depRptCur',
        'depAmountRpt',
        'timesReferred',
        'refferedBackYN',
        'RollLevForApp_curr',
        'isDepProcessingYN',
        'totalChunks',
        'counter',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByEmpName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'createdUserID',
        'createdUserSystemID',
        'createdPCID',
        'timeStamp',
        'is_acc_dep',
        'is_cancel'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'depMasterAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'depCode' => 'string',
        'depMonthYear' => 'string',
        'depLocalCur' => 'integer',
        'depAmountLocal' => 'float',
        'depRptCur' => 'integer',
        'depAmountRpt' => 'float',
        'timesReferred' => 'integer',
        'refferedBackYN' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'isDepProcessingYN' => 'integer',
        'counter' => 'integer',
        'totalChunks' => 'integer',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByEmpName' => 'string',
        'approved' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdPCID' => 'string',
        'is_cancel' => 'integer'
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
        return $query->whereIN('companySystemID',  $type);
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'depMasterAutoID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function depperiod_by()
    {
        return $this->hasMany('App\Models\FixedAssetDepreciationPeriod', 'depMasterAutoID', 'depMasterAutoID');
    }
    public function setDepDateAttribute($value)
    {
        $this->attributes['depDate'] = Helper::dateAddTime($value);
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'depMasterAutoID')->where('documentSystemID',23);
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    
    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_fa_depmaster.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeCurrencyJoin($q,$as = 'currencymaster' ,$column = 'supplierTransactionCurrencyID',$columnAs = 'CurrencyName'){
        return $q->leftJoin('currencymaster as '.$as,$as.'.currencyID','=','erp_fa_depmaster.'.$column)
        ->addSelect($as.".CurrencyName as ".$columnAs);

    }

    public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_fa_depmaster.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }

    public function financeperiod_by()
    {
        return $this->belongsTo('App\Models\CompanyFinancePeriod', 'companyFinancePeriodID', 'companyFinancePeriodID');
    }
}

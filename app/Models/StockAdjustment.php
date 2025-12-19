<?php
/**
 * =============================================
 * -- File Name : StockAdjustment.php
 * -- Project Name : ERP
 * -- Module Name : Stock Adjustment
 * -- Author : Mohamed Fayas
 * -- Create date : 20- August 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use App\helper\Helper;
use Awobaz\Compoships\Compoships;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="StockAdjustment",
 *      required={""},
 *      @SWG\Property(
 *          property="stockAdjustmentAutoID",
 *          description="stockAdjustmentAutoID",
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
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
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
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyFinancePeriodID",
 *          description="companyFinancePeriodID",
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
 *          property="stockAdjustmentCode",
 *          description="stockAdjustmentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="refNo",
 *          description="refNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="location",
 *          description="location",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
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
 *          property="confirmedByName",
 *          description="confirmedByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approved",
 *          description="approved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPCid",
 *          description="createdPCid",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      )
 * )
 */
class StockAdjustment extends Model
{
    use Compoships;
    public $table = 'erp_stockadjustment';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'stockAdjustmentAutoID';



    public $fillable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'documentSystemID',
        'documentID',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'serialNo',
        'stockAdjustmentCode',
        'refNo',
        'stockAdjustmentDate',
        'location',
        'comment',
        'stockAdjustmentType',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'createdDateTime',
        'createdUserGroup',
        'createdPCid',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'timestamp',
        'RollLevForApp_curr',
        'refferedBackYN',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'reason',
        'timesReferred ',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'stockAdjustmentAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'serialNo' => 'integer',
        'stockAdjustmentCode' => 'string',
        'refNo' => 'string',
        'location' => 'integer',
        'comment' => 'string',
        'stockAdjustmentType'=>'integer',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'approved' => 'integer',
        'createdUserGroup' => 'string',
        'createdPCid' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'RollLevForApp_curr' => 'integer',
        'stockAdjustmentDate' => 'string',
        'createdDateTime' => 'string',
        'refferedBackYN' => 'integer',
        'approvedDate' => 'string',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'reason' => 'integer',
        'timesReferred' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }


    public function segment_by()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function warehouse_by()
    {
        return $this->belongsTo('App\Models\WarehouseMaster','location','wareHouseSystemCode');
    }

    public function details()
    {
        return $this->hasMany('App\Models\StockAdjustmentDetails','stockAdjustmentAutoID','stockAdjustmentAutoID');
    }

    public function approved_by(){
        return $this->hasMany('App\Models\DocumentApproved','documentSystemCode','stockAdjustmentAutoID');
    }
    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public function finance_period_by()
    {
        return $this->belongsTo('App\Models\CompanyFinancePeriod', 'companyFinancePeriodID', 'companyFinancePeriodID');
    }

    public function finance_year_by()
    {
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }

    public function setStockAdjustmentDateAttribute($value)
    {
        $this->attributes['stockAdjustmentDate'] = Helper::dateAddTime($value);
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'stockAdjustmentAutoID')->where('documentSystemID',7);
    }

    public function reason() {
        return $this->belongsTo('App\Models\StockAdjustmentReason', 'reason', 'id');

    }

    
    public function scopeDetailJoin($q)
    {
        return $q->join('erp_stockadjustmentdetails','erp_stockadjustmentdetails.stockAdjustmentDetailsAutoID','erp_stockadjustment.stockAdjustmentAutoID');
    }
    
    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_stockadjustment.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_stockadjustment.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }

    public function scopeWareHouseJoin($q,$as = 'warehousemaster', $column = 'wareHouseSystemCode' , $columnAs = 'wareHouseDescription')
    {
        return $q->leftJoin('warehousemaster as '.$as,$as.'.wareHouseSystemCode','erp_stockadjustment.'.$column)
        ->addSelect($as.".wareHouseDescription as ".$columnAs);
    }

    
    public function scopeSegmentJoin($q,$as = 'serviceline', $column = 'serviceLineSystemID' , $columnAs = 'ServiceLineDes')
    {
        return $q->leftJoin('serviceline as '.$as,$as.'.serviceLineSystemID','erp_stockadjustment.'.$column)
        ->addSelect($as.".ServiceLineDes as ".$columnAs);
    }

    public function scopeReasonJoin($q,$as = 'stockadjustment_reasons', $column = 'reason' , $columnAs = 'reason')
    {
        return $q->leftJoin('stockadjustment_reasons as '.$as,$as.'.id','erp_stockadjustment.'.$column)
        ->addSelect($as.".reason as ".$columnAs);
    }
}

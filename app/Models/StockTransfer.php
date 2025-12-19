<?php
/**
 * =============================================
 * -- File Name : StockTransfer.php
 * -- Project Name : ERP
 * -- Module Name :  Stock Transfer
 * -- Author : Mohamed Nazir
 * -- Create date : 13 - July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use App\helper\Helper;
use Awobaz\Compoships\Compoships;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="StockTransfer",
 *      required={""},
 *      @SWG\Property(
 *          property="stockTransferAutoID",
 *          description="stockTransferAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
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
 *          property="stockTransferCode",
 *          description="stockTransferCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="refNo",
 *          description="refNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comment",
 *          description="comment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyFrom",
 *          description="companyFrom",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyTo",
 *          description="companyTo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="locationTo",
 *          description="locationTo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="locationFrom",
 *          description="locationFrom",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
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
 *          property="fullyReceived",
 *          description="fullyReceived",
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
 *          property="interCompanyTransferYN",
 *          description="interCompanyTransferYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
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
class StockTransfer extends Model
{
    use Compoships;
    public $table = 'erp_stocktransfer';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'stockTransferAutoID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'FYBiggin',
        'FYEnd',
        'documentSystemID',
        'documentID',
        'serialNo',
        'stockTransferCode',
        'refNo',
        'tranferDate',
        'comment',
        'companyFromSystemID',
        'companyFrom',
        'companyToSystemID',
        'companyTo',
        'locationTo',
        'locationFrom',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'fullyReceived',
        'timesReferred',
        'interCompanyTransferYN',
        'RollLevForApp_curr',
        'createdDateTime',
        'createdUserGroup',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUser',
        'modifiedUserSystemID',
        'modifiedPc',
        'timestamp',
        'approvedByUserID',
        'approvedByUserSystemID',
        'refferedBackYN',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'stockTransferAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'stockTransferCode' => 'string',
        'refNo' => 'string',
        'comment' => 'string',
        'companyFromSystemID' => 'integer',
        'companyFrom' => 'string',
        'companyToSystemID' => 'integer',
        'companyTo' => 'string',
        'locationTo' => 'integer',
        'locationFrom' => 'integer',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'approved' => 'integer',
        'fullyReceived' => 'integer',
        'timesReferred' => 'integer',
        'interCompanyTransferYN' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'createdUserGroup' => 'string',
        'createdUserSystemID' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'refferedBackYN' => 'integer'
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

    public function location_to_by()
    {
        return $this->belongsTo('App\Models\WarehouseMaster', 'locationTo', 'wareHouseSystemCode');
    }

    public function location_from_by()
    {
        return $this->belongsTo('App\Models\WarehouseMaster', 'locationFrom', 'wareHouseSystemCode');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'stockTransferAutoID');
    }

    public function details()
    {
        return $this->hasMany('App\Models\StockTransferDetails', 'stockTransferAutoID', 'stockTransferAutoID');
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

    public function setTranferDateAttribute($value)
    {
        $this->attributes['tranferDate'] = Helper::dateAddTime($value);
    }

    public function setPostedDateAttribute($value)
    {
        $this->attributes['postedDate'] = Helper::dateAddTime($value);
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'stockTransferAutoID')->where('documentSystemID',13);
    }

    public function company_from(){
        return $this->belongsTo('App\Models\Company','companyFromSystemID','companySystemID');
    }

    public function company_to(){
        return $this->belongsTo('App\Models\Company','companyToSystemID','companySystemID');
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_stocktransferdetails','erp_stocktransferdetails.stockTransferAutoID','erp_stocktransfer.stockTransferAutoID');
    }

    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_stocktransfer.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_stocktransfer.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }

    public function scopeWareHouseJoin($q,$as = 'warehousemaster', $column = 'wareHouseSystemCode' , $columnAs = 'wareHouseDescription')
    {
        return $q->leftJoin('warehousemaster as '.$as,$as.'.wareHouseSystemCode','erp_stocktransfer.'.$column)
        ->addSelect($as.".wareHouseDescription as ".$columnAs);
    }

    
    public function scopeSegmentJoin($q,$as = 'serviceline', $column = 'serviceLineSystemID' , $columnAs = 'ServiceLineDes')
    {
        return $q->leftJoin('serviceline as '.$as,$as.'.serviceLineSystemID','erp_stocktransfer.'.$column)
        ->addSelect($as.".ServiceLineDes as ".$columnAs);
    }
}

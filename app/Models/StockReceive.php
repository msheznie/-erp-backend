<?php
/**
 * =============================================
 * -- File Name : StockReceive.php
 * -- Project Name : ERP
 * -- Module Name : StockReceive
 * -- Author : Mohamed Fayas
 * -- Create date : 23 - July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use App\helper\Helper;
use Awobaz\Compoships\Compoships;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="StockReceive",
 *      required={""},
 *      @SWG\Property(
 *          property="stockReceiveAutoID",
 *          description="stockReceiveAutoID",
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
 *          property="stockReceiveCode",
 *          description="stockReceiveCode",
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
 *          property="companyFromSystemID",
 *          description="companyFromSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyFrom",
 *          description="companyFrom",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyToSystemID",
 *          description="companyToSystemID",
 *          type="integer",
 *          format="int32"
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
class StockReceive extends Model
{
    use Compoships;
    public $table = 'erp_stockreceive';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'stockReceiveAutoID';


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
        'stockReceiveCode',
        'refNo',
        'receivedDate',
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
        'timesReferred',
        'interCompanyTransferYN',
        'RollLevForApp_curr',
        'createdDateTime',
        'createdUserGroup',
        'createdPCID',
        'createdUserSystemID',
        'createdUserID',
        'modifiedUserSystemID',
        'modifiedUser',
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
        'stockReceiveAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'stockReceiveCode' => 'string',
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
        'timesReferred' => 'integer',
        'interCompanyTransferYN' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'createdUserGroup' => 'string',
        'createdPCID' => 'string',
        'createdUserSystemID' => 'integer',
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
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'stockReceiveAutoID');
    }

    public function details()
    {
        return $this->hasMany('App\Models\StockReceiveDetails', 'stockReceiveAutoID', 'stockReceiveAutoID');
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

    public function setReceivedDateAttribute($value)
    {
        $this->attributes['receivedDate'] = Helper::dateAddTime($value);
    }

    public function setPostedDateAttribute($value)
    {
        $this->attributes['postedDate'] = Helper::dateAddTime($value);
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'stockReceiveAutoID')->where('documentSystemID',10);
    }

    public function company_from(){
        return $this->belongsTo('App\Models\Company','companyFromSystemID','companySystemID');
    }

    public function company_to(){
        return $this->belongsTo('App\Models\Company','companyToSystemID','companySystemID');
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_stockreceivedetails','erp_stockreceivedetails.stockReceiveAutoID','erp_stockreceive.stockReceiveAutoID');
    }
    
    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_stockreceive.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_stockreceive.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }

    public function scopeWareHouseJoin($q,$as = 'warehousemaster', $column = 'wareHouseSystemCode' , $columnAs = 'wareHouseDescription')
    {
        return $q->leftJoin('warehousemaster as '.$as,$as.'.wareHouseSystemCode','erp_stockreceive.'.$column)
        ->addSelect($as.".wareHouseDescription as ".$columnAs);
    }

    
    public function scopeSegmentJoin($q,$as = 'serviceline', $column = 'serviceLineSystemID' , $columnAs = 'ServiceLineDes')
    {
        return $q->leftJoin('serviceline as '.$as,$as.'.serviceLineSystemID','erp_stockreceive.'.$column)
        ->addSelect($as.".ServiceLineDes as ".$columnAs);
    }

}

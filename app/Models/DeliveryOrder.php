<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DeliveryOrder",
 *      required={""},
 *      @SWG\Property(
 *          property="deliveryOrderID",
 *          description="deliveryOrderID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="orderType",
 *          description="orderType",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="deliveryOrderCode",
 *          description="deliveryOrderCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companySystemId",
 *          description="companySystemId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemId",
 *          description="documentSystemId",
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
 *          property="FYBiggin",
 *          description="FYBiggin",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="FYEnd",
 *          description="FYEnd",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="companyFinancePeriodID",
 *          description="companyFinancePeriodID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="FYPeriodDateFrom",
 *          description="FYPeriodDateFrom",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="FYPeriodDateTo",
 *          description="FYPeriodDateTo",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="deliveryOrderDate",
 *          description="deliveryOrderDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseSystemCode",
 *          description="wareHouseSystemCode",
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
 *          property="referenceNo",
 *          description="referenceNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="salesPersonID",
 *          description="salesPersonID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="narration",
 *          description="narration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="notes",
 *          description="notes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactPersonNumber",
 *          description="contactPersonNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactPersonName",
 *          description="contactPersonName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyID",
 *          description="transactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyER",
 *          description="transactionCurrencyER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="transactionAmount",
 *          description="transactionAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyID",
 *          description="companyLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyER",
 *          description="companyLocalCurrencyER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalAmount",
 *          description="companyLocalAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyID",
 *          description="companyReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyER",
 *          description="companyReportingCurrencyER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingAmount",
 *          description="companyReportingAmount",
 *          type="number",
 *          format="number"
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
 *          property="confirmedDate",
 *          description="confirmedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedDate",
 *          description="approvedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="approvedEmpSystemID",
 *          description="approvedEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="approvedbyEmpID",
 *          description="approvedbyEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedbyEmpName",
 *          description="approvedbyEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="refferedBackYN",
 *          description="refferedBackYN",
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
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="closedYN",
 *          description="closedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="closedDate",
 *          description="closedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="closedReason",
 *          description="closedReason",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="integer",
 *          format="int32"
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
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class DeliveryOrder extends Model
{

    public $table = 'erp_delivery_order';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'deliveryOrderID';


    public $fillable = [
        'orderType',
        'deliveryOrderCode',
        'serialNo',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'deliveryOrderDate',
        'wareHouseSystemCode',
        'serviceLineSystemID',
        'serviceLineCode',
        'referenceNo',
        'customerID',
        'custGLAccountSystemID',
        'custGLAccountCode',
        'custUnbilledAccountSystemID',
        'custUnbilledAccountCode',
        'salesPersonID',
        'narration',
        'notes',
        'contactPersonNumber',
        'contactPersonName',
        'transactionCurrencyID',
        'transactionCurrencyER',
        'transactionAmount',
        'companyLocalCurrencyID',
        'companyLocalCurrencyER',
        'companyLocalAmount',
        'companyReportingCurrencyID',
        'companyReportingCurrencyER',
        'companyReportingAmount',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approvedYN',
        'approvedDate',
        'approvedEmpSystemID',
        'approvedbyEmpID',
        'approvedbyEmpName',
        'refferedBackYN',
        'timesReferred',
        'RollLevForApp_curr',
        'invoiceStatus',
        'closedYN',
        'closedDate',
        'closedReason',
        'createdUserSystemID',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedUserSystemID',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'selectedForCustomerInvoice',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'deliveryOrderID' => 'integer',
        'orderType' => 'integer',
        'deliveryOrderCode' => 'string',
        'serialNo' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'companyFinanceYearID' => 'integer',
        'FYBiggin' => 'datetime',
        'FYEnd' => 'datetime',
        'companyFinancePeriodID' => 'integer',
        'FYPeriodDateFrom' => 'datetime',
        'FYPeriodDateTo' => 'datetime',
        'deliveryOrderDate' => 'datetime',
        'wareHouseSystemCode' => 'integer',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'referenceNo' => 'string',
        'customerID' => 'integer',
        'custGLAccountSystemID' => 'integer',
        'custGLAccountCode' => 'string',
        'custUnbilledAccountSystemID' => 'integer',
        'custUnbilledAccountCode' => 'string',
        'salesPersonID' => 'integer',
        'narration' => 'string',
        'notes' => 'string',
        'contactPersonNumber' => 'string',
        'contactPersonName' => 'string',
        'transactionCurrencyID' => 'integer',
        'transactionCurrencyER' => 'float',
        'transactionAmount' => 'float',
        'companyLocalCurrencyID' => 'integer',
        'companyLocalCurrencyER' => 'float',
        'companyLocalAmount' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingCurrencyER' => 'float',
        'companyReportingAmount' => 'float',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'confirmedDate' => 'datetime',
        'approvedYN' => 'integer',
        'approvedDate' => 'datetime',
        'approvedEmpSystemID' => 'integer',
        'approvedbyEmpID' => 'string',
        'approvedbyEmpName' => 'string',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'invoiceStatus' => 'integer',
        'closedYN' => 'integer',
        'closedDate' => 'datetime',
        'closedReason' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'createdUserName' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'integer',
        'modifiedDateTime' => 'datetime',
        'modifiedUserName' => 'string',
        'selectedForCustomerInvoice' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function detail()
    {
        return $this->hasMany('App\Models\DeliveryOrderDetail', 'deliveryOrderID', 'deliveryOrderID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'deliveryOrderID');
    }

    public function sales_person()
    {
        return $this->belongsTo('App\Models\SalesPersonMaster', 'salesPersonID', 'salesPersonID');
    }

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\CustomerMaster', 'customerID', 'customerCodeSystem');
    }

    public function transaction_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'transactionCurrencyID', 'currencyID');
    }

    public function local_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'companyLocalCurrencyID', 'currencyID');
    }

    public function reporting_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'companyReportingCurrencyID', 'currencyID');
    }

    public function finance_year_by(){
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }

    public function finance_period_by()
    {
        return $this->belongsTo('App\Models\CompanyFinancePeriod', 'companyFinancePeriodID', 'companyFinancePeriodID');
    }
}

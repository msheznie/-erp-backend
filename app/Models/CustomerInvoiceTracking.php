<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerInvoiceTracking",
 *      required={""},
 *      @SWG\Property(
 *          property="customerInvoiceTrackingID",
 *          description="customerInvoiceTrackingID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
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
 *          property="customerInvoiceTrackingCode",
 *          description="customerInvoiceTrackingCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="manualTrackingNo",
 *          description="manualTrackingNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="contractNumber",
 *          description="contractNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvalType",
 *          description="approvalType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="submittedYN",
 *          description="submittedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="submittedEmpID",
 *          description="submittedEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="submittedEmpName",
 *          description="submittedEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="submittedDate",
 *          description="submittedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="submittedYear",
 *          description="submittedYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="closeYN",
 *          description="closeYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="closedByEmpID",
 *          description="closedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="closedByEmpName",
 *          description="closedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="closedDate",
 *          description="closedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="totalBatchAmount",
 *          description="totalBatchAmount",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="totalApprovedAmount",
 *          description="totalApprovedAmount",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="totalRejectedAmount",
 *          description="totalRejectedAmount",
 *          type="float",
 *          format="float"
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
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class CustomerInvoiceTracking extends Model
{

    public $table = 'erp_customerinvoicetracking';
    protected $primaryKey = 'customerInvoiceTrackingID';
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';



    public $fillable = [
        'documentID',
        'documentSystemID',
        'companyID',
        'companySystemID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'customerInvoiceTrackingCode',
        'manualTrackingNo',
        'customerID',
        'contractNumber',
        'contractUID',
        'serviceLineCode',
        'serviceLineSystemID',
        'comments',
        'approvalType',
        'submittedYN',
        'submittedEmpID',
        'submittedEmpSystemID',
        'submittedEmpName',
        'submittedDate',
        'submittedYear',
        'closeYN',
        'closedByEmpID',
        'closedByEmpName',
        'closedDate',
        'totalBatchAmount',
        'totalApprovedAmount',
        'totalRejectedAmount',
        'createdUserID',
        'createdDateTime',
        'timestamp',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'customerInvoiceTrackingID' => 'integer',
        'documentID' => 'string',
        'documentSystemID' => 'integer',
        'companyID' => 'string',
        'companySystemID' => 'integer',
        'serialNo' => 'integer',
        'companyFinanceYearID' => 'integer',
        'FYBiggin' => 'datetime',
        'FYEnd' => 'datetime',
        'companyFinancePeriodID' => 'integer',
        'FYPeriodDateFrom' => 'datetime',
        'FYPeriodDateTo' => 'datetime',
        'customerInvoiceTrackingCode' => 'string',
        'manualTrackingNo' => 'string',
        'customerID' => 'integer',
        'contractNumber' => 'string',
        'contractUID' => 'integer',
        'serviceLineCode' => 'string',
        'serviceLineSystemID' => 'integer',
        'comments' => 'string',
        'approvalType' => 'integer',
        'submittedYN' => 'integer',
        'submittedEmpSystemID' => 'integer',
        'submittedEmpID' => 'string',
        'submittedEmpName' => 'string',
        'submittedDate' => 'datetime',
        'submittedYear' => 'integer',
        'closeYN' => 'integer',
        'closedByEmpID' => 'string',
        'closedByEmpName' => 'string',
        'closedDate' => 'datetime',
        'totalBatchAmount' => 'float',
        'totalApprovedAmount' => 'float',
        'totalRejectedAmount' => 'float',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'customerInvoiceTrackingID' => 'required'
    ];

    public function detail(){
        return $this->hasMany('App\Models\CustomerInvoiceTrackingDetail','customerInvoiceTrackingID','customerInvoiceTrackingID');
    }

    public function customer(){
        return $this->belongsTo('App\Models\CustomerMaster','customerID','customerCodeSystem');
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

    public function approval_type()
    {
        return $this->belongsTo('App\Models\ClientPerformaAppType', 'approvalType', 'performaAppTypeID');
    }
    
}

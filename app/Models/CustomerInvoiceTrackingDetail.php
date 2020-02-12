<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerInvoiceTrackingDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="customerInvoiceTrackingDetailID",
 *          description="customerInvoiceTrackingDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerInvoiceTrackingID",
 *          description="customerInvoiceTrackingID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custInvoiceDirectAutoID",
 *          description="custInvoiceDirectAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bookingInvCode",
 *          description="bookingInvCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bookingDate",
 *          description="bookingDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="customerInvoiceNo",
 *          description="customerInvoiceNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerInvoiceDate",
 *          description="customerInvoiceDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="invoiceDueDate",
 *          description="invoiceDueDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="contractID",
 *          description="contractID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="PerformaInvoiceNo",
 *          description="PerformaInvoiceNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wanNO",
 *          description="wanNO",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="PONumber",
 *          description="PONumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="rigNo",
 *          description="rigNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="wellNo",
 *          description="wellNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="amount",
 *          description="amount",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="confirmedDate",
 *          description="confirmedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="customerApprovedYN",
 *          description="customerApprovedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerApprovedDate",
 *          description="when the customer approved the invoice",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="customerApprovedByEmpID",
 *          description="captures the person who checked on the customer approved",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerApprovedByEmpSystemID",
 *          description="customerApprovedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerApprovedByEmpName",
 *          description="captures the person who checked on the customer approved",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerApprovedByDate",
 *          description="captures the date and time checked on the customer approved",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedAmount",
 *          description="approvedAmount",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="customerRejectedYN",
 *          description="customerRejectedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerRejectedDate",
 *          description="customerRejectedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="customerRejectedByEmpID",
 *          description="customerRejectedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerRejectedByEmpSystemID",
 *          description="customerRejectedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerRejectedByEmpName",
 *          description="customerRejectedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerRejectedByDate",
 *          description="customerRejectedByDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="rejectedAmount",
 *          description="rejectedAmount",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="remarks",
 *          description="remarks",
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
class CustomerInvoiceTrackingDetail extends Model
{

    public $table = 'erp_customerinvoicetrackingdetail';
    protected $primaryKey = 'customerInvoiceTrackingDetailID';
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';



    public $fillable = [
        'customerInvoiceTrackingID',
        'companyID',
        'companySystemID',
        'customerID',
        'custInvoiceDirectAutoID',
        'bookingInvCode',
        'bookingDate',
        'customerInvoiceNo',
        'customerInvoiceDate',
        'invoiceDueDate',
        'contractID',
        'PerformaInvoiceNo',
        'wanNO',
        'PONumber',
        'rigNo',
        'wellNo',
        'amount',
        'confirmedDate',
        'customerApprovedYN',
        'customerApprovedDate',
        'customerApprovedByEmpID',
        'customerApprovedByEmpSystemID',
        'customerApprovedByEmpName',
        'customerApprovedByDate',
        'approvedAmount',
        'customerRejectedYN',
        'customerRejectedDate',
        'customerRejectedByEmpID',
        'customerRejectedByEmpSystemID',
        'customerRejectedByEmpName',
        'customerRejectedByDate',
        'rejectedAmount',
        'remarks',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'customerInvoiceTrackingDetailID' => 'integer',
        'customerInvoiceTrackingID' => 'integer',
        'companyID' => 'string',
        'companySystemID' => 'integer',
        'customerID' => 'integer',
        'custInvoiceDirectAutoID' => 'integer',
        'bookingInvCode' => 'string',
        'bookingDate' => 'datetime',
        'customerInvoiceNo' => 'string',
        'customerInvoiceDate' => 'datetime',
        'invoiceDueDate' => 'datetime',
        'contractID' => 'string',
        'PerformaInvoiceNo' => 'integer',
        'wanNO' => 'string',
        'PONumber' => 'string',
        'rigNo' => 'string',
        'wellNo' => 'string',
        'amount' => 'float',
        'confirmedDate' => 'datetime',
        'customerApprovedYN' => 'integer',
        'customerApprovedDate' => 'datetime',
        'customerApprovedByEmpID' => 'string',
        'customerApprovedByEmpSystemID' => 'integer',
        'customerApprovedByEmpName' => 'string',
        'customerApprovedByDate' => 'string',
        'approvedAmount' => 'float',
        'customerRejectedYN' => 'integer',
        'customerRejectedDate' => 'datetime',
        'customerRejectedByEmpID' => 'string',
        'customerRejectedByEmpSystemID' => 'integer',
        'customerRejectedByEmpName' => 'string',
        'customerRejectedByDate' => 'datetime',
        'rejectedAmount' => 'float',
        'remarks' => 'string',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'customerInvoiceTrackingDetailID' => 'required'
    ];

    public function master(){
        return $this->belongsTo('App\Models\CustomerInvoiceTracking','customerInvoiceTrackingID','customerInvoiceTrackingID');
    }

    public function approved_by(){
        return $this->belongsTo('App\Models\Employee','customerApprovedByEmpSystemID','employeeSystemID');
    }

    public function rejected_by(){
        return $this->belongsTo('App\Models\Employee','customerRejectedByEmpSystemID','employeeSystemID');
    }

    public function customer_invoice_direct(){
        return $this->belongsTo('App\Models\CustomerInvoiceDirect','custInvoiceDirectAutoID','custInvoiceDirectAutoID');
    }
}

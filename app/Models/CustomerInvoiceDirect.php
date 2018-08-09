<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerInvoiceDirect",
 *      required={""},
 *      @SWG\Property(
 *          property="custInvoiceDirectAutoID",
 *          description="custInvoiceDirectAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transactionMode",
 *          description="transactionMode",
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
 *          property="documentSystemiD",
 *          description="documentSystemiD",
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
 *          property="companyFinancePeriodID",
 *          description="companyFinancePeriodID",
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
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseSystemCode",
 *          description="wareHouseSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bookingInvCode",
 *          description="bookingInvCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerGRVAutoID",
 *          description="customerGRVAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankID",
 *          description="bankID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankAccountID",
 *          description="bankAccountID",
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
 *          property="customerID",
 *          description="customerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerGLCode",
 *          description="customerGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerInvoiceNo",
 *          description="customerInvoiceNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="custTransactionCurrencyID",
 *          description="custTransactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custTransactionCurrencyER",
 *          description="custTransactionCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyID",
 *          description="companyReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingER",
 *          description="companyReportingER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyID",
 *          description="localCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyER",
 *          description="localCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="bookingAmountTrans",
 *          description="bookingAmountTrans",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="bookingAmountLocal",
 *          description="bookingAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="bookingAmountRpt",
 *          description="bookingAmountRpt",
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
 *          property="servicePeriod",
 *          description="servicePeriod",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="paymentInDaysForJob",
 *          description="paymentInDaysForJob",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isPerforma",
 *          description="isPerforma",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentType",
 *          description="documentType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="secondaryLogoCompID",
 *          description="secondaryLogoCompID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="secondaryLogo",
 *          description="secondaryLogo",
 *          type="string"
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
 *          property="selectedForTracking",
 *          description="selectedForTracking",
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
 *          property="interCompanyTransferYN",
 *          description="interCompanyTransferYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="canceledYN",
 *          description="canceledYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="canceledByEmpSystemID",
 *          description="canceledByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="canceledByEmpID",
 *          description="canceledByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="canceledByEmpName",
 *          description="canceledByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="vatOutputGLCodeSystemID",
 *          description="vatOutputGLCodeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatOutputGLCode",
 *          description="vatOutputGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="VATPercentage",
 *          description="VATPercentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="VATAmount",
 *          description="VATAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="VATAmountLocal",
 *          description="VATAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="VATAmountRpt",
 *          description="VATAmountRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="discountLocalAmount",
 *          description="discountLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="discountAmount",
 *          description="discountAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="discountRptAmount",
 *          description="discountRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="canceledComments",
 *          description="canceledComments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
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
 *          property="createdPcID",
 *          description="createdPcID",
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
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string"
 *      )
 * )
 */
class CustomerInvoiceDirect extends Model
{

    public $table = 'erp_custinvoicedirect';
    
    const CREATED_AT = 'createdDateAndTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey  = 'custInvoiceDirectAutoID';


    public $fillable = [
        'transactionMode',
        'companySystemID',
        'companyID',
        'documentSystemiD',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'serviceLineSystemID',
        'serviceLineCode',
        'wareHouseSystemCode',
        'bookingInvCode',
        'bookingDate',
        'comments',
        'invoiceDueDate',
        'customerGRVAutoID',
        'bankID',
        'bankAccountID',
        'performaDate',
        'wanNO',
        'PONumber',
        'rigNo',
        'customerID',
        'customerGLCode',
        'customerInvoiceNo',
        'customerInvoiceDate',
        'custTransactionCurrencyID',
        'custTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'bookingAmountTrans',
        'bookingAmountLocal',
        'bookingAmountRpt',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'servicePeriod',
        'paymentInDaysForJob',
        'serviceStartDate',
        'serviceEndDate',
        'isPerforma',
        'documentType',
        'secondaryLogoCompID',
        'secondaryLogo',
        'timesReferred',
        'RollLevForApp_curr',
        'selectedForTracking',
        'customerInvoiceTrackingID',
        'interCompanyTransferYN',
        'canceledYN',
        'canceledByEmpSystemID',
        'canceledByEmpID',
        'canceledByEmpName',
        'vatOutputGLCodeSystemID',
        'vatOutputGLCode',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'discountLocalAmount',
        'discountAmount',
        'discountRptAmount',
        'canceledDateTime',
        'canceledComments',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp',
        'createdDateAndTime'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'custInvoiceDirectAutoID' => 'integer',
        'transactionMode' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemiD' => 'integer',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'wareHouseSystemCode' => 'integer',
        'bookingInvCode' => 'string',
        'comments' => 'string',
        'customerGRVAutoID' => 'integer',
        'bankID' => 'integer',
        'bankAccountID' => 'integer',
        'wanNO' => 'string',
        'PONumber' => 'string',
        'rigNo' => 'string',
        'customerID' => 'integer',
        'customerGLCode' => 'string',
        'customerInvoiceNo' => 'string',
        'custTransactionCurrencyID' => 'integer',
        'custTransactionCurrencyER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'bookingAmountTrans' => 'float',
        'bookingAmountLocal' => 'float',
        'bookingAmountRpt' => 'float',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'approved' => 'integer',
        'servicePeriod' => 'string',
        'paymentInDaysForJob' => 'integer',
        'isPerforma' => 'integer',
        'documentType' => 'integer',
        'secondaryLogoCompID' => 'string',
        'secondaryLogo' => 'string',
        'timesReferred' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'selectedForTracking' => 'integer',
        'customerInvoiceTrackingID' => 'integer',
        'interCompanyTransferYN' => 'integer',
        'canceledYN' => 'integer',
        'canceledByEmpSystemID' => 'integer',
        'canceledByEmpID' => 'string',
        'canceledByEmpName' => 'string',
        'vatOutputGLCodeSystemID' => 'integer',
        'vatOutputGLCode' => 'string',
        'VATPercentage' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'discountLocalAmount' => 'float',
        'discountAmount' => 'float',
        'discountRptAmount' => 'float',
        'canceledComments' => 'string',
        'createdUserGroup' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'createdDateTime' => 'string',
        'createdDateAndTime' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}

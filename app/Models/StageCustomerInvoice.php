<?php

namespace App\Models;


use App\helper\Helper;
use Eloquent as Model;

class StageCustomerInvoice extends Model
{
    public $table = 'erp_stage_custinvoicedirect';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'custInvoiceDirectAutoID';
    protected $dates = ['deleted_at'];

    public $fillable = [
        'referenceNumber',
        'transactionMode',
        'companyID',
        'companySystemID',
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
        'canceledByEmpID',
        'canceledByEmpName',
        'vatOutputGLCodeSystemID',
        'vatOutputGLCode',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'canceledDateTime',
        'canceledComments',
        'createdUserGroup',
        'createdUserID',
        'createdPcID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp',
        'discountLocalAmount',
        'discountAmount',
        'discountRptAmount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'custInvoiceDirectAutoID' => 'integer',
        'transactionMode' => 'integer',
        'companyID' => 'string',
        'companySystemID' => 'integer',
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
        'canceledByEmpID' => 'string',
        'canceledByEmpName' => 'string',
        'vatOutputGLCodeSystemID' => 'integer',
        'vatOutputGLCode' => 'string',
        'VATPercentage' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'canceledComments' => 'string',
        'createdUserGroup' => 'string',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'createdDateTime' => 'string',
        'discountLocalAmount' => 'float',
        'discountAmount' => 'float',
        'discountRptAmount' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];
}

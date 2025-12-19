<?php
/**
 * =============================================
 * -- File Name : CustomerInvoiceDirect.php
 * -- Project Name : ERP
 * -- Module Name :  Customer Invoice
 * -- Author : Nazir
 * -- Create date : 21 - August 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use App\helper\Helper;
use Awobaz\Compoships\Compoships;
use Eloquent as Model;
use App\helper\TaxService;

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
    use Compoships;
    public $table = 'erp_custinvoicedirect';

    const CREATED_AT = 'createdDateAndTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'custInvoiceDirectAutoID';

    protected $appends = ['isVatEligible'];

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
        'customerGLSystemID',
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
        'secondaryLogoCompanySystemID',
        'secondaryLogoCompID',
        'secondaryLogo',
        'refferedBackYN',
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
        'createdDateAndTime',
        'approvedByUserSystemID',
        'returnStatus',
        'selectedForSalesReturn',
        'vatRegisteredYN',
        'createdFrom',
        'customerVATEligible',
        'approvedByUserID',
        'date_of_supply',
        'isPOS',
        'isUpload',
        'isAutoGenerated',
        'isDelegation',
        'statusFromDisposal',
        'salesType'
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
        'customerGLSystemID' => 'integer',
        'createdFrom' => 'integer',
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
        '`secondaryLogoCompanySystemID`' => 'integer',
        'secondaryLogoCompID' => 'string',
        'secondaryLogo' => 'string',
        'refferedBackYN' => 'integer',
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
        'createdDateAndTime' => 'string',
        'approvedByUserSystemID' => 'integer',
        'selectedForSalesReturn' => 'integer',
        'returnStatus' => 'integer',
        'approvedByUserID' => 'integer',
        'vatRegisteredYN' => 'integer',
        'customerVATEligible' => 'integer',
        'date_of_supply' => 'string',
        'isPOS' =>'integer',
        'isPOS' =>'isUpload',
        'statusFromDisposal' =>'integer',
        'salesType' =>'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function getIsVatEligibleAttribute()
    {
        return TaxService::checkPOVATEligible($this->customerVATEligible,$this->vatRegisteredYN,$this->documentSystemiD);
    }


    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function logistic()
    {
        return $this->belongsTo('App\Models\CustomerInvoiceLogistic', 'custInvoiceDirectAutoID', 'custInvoiceDirectAutoID');
    }

    public function secondarycompany()
    {
        return $this->belongsTo('App\Models\SecondaryCompany', 'companySystemID', 'companySystemID');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\CustomerMaster', 'customerID', 'customerCodeSystem');
    }

    public function invoicedetails()
    {
        return $this->hasMany('App\Models\CustomerInvoiceDirectDetail', 'custInvoiceDirectID', 'custInvoiceDirectAutoID');
    }

    public function invoicedetail()
    {
        return $this->belongsTo('App\Models\CustomerInvoiceDirectDetail', 'custInvoiceDirectAutoID','custInvoiceDirectID');
    }

    public function tax()
    {
        return $this->belongsTo('App\Models\Taxdetail', 'custInvoiceDirectAutoID', 'documentSystemCode')
            ->where('documentSystemID', 20);
    }

    public function createduser()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function bankaccount()
    {
        return $this->belongsTo('App\Models\BankAccount', 'bankAccountID', 'bankAccountAutoID');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'custInvoiceDirectAutoID');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'custTransactionCurrencyID', 'currencyID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function cancelled_by()
    {
        return $this->belongsTo('App\Models\Employee', 'canceledByEmpSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function companydocumentattachment_by()
    {
        return $this->hasMany('App\Models\CompanyDocumentAttachment', 'companySystemID', 'companySystemID');
    }

     public function finance_year_by() {
         return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
     }

    public function finance_period_by()
    {
        return $this->belongsTo('App\Models\CompanyFinancePeriod', 'companyFinancePeriodID', 'companyFinancePeriodID');
    }

    public function grv()
    {
        return $this->belongsTo('App\Models\GRVMaster', 'customerGRVAutoID', 'grvAutoID');
    }

    public function setBookingDateAttribute($value)
    {
        $this->attributes['bookingDate'] = Helper::dateAddTime($value);
    }

    public function setPostedDateAttribute($value)
    {
        $this->attributes['postedDate'] = Helper::dateAddTime($value);
    }

    public function report_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'companyReportingCurrencyID', 'currencyID');
    }

    public function item_ledger()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'companyReportingCurrencyID', 'currencyID');
    }

    public function local_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID', 'currencyID');
    }

    public function issue_item_details()
    {
        return $this->hasMany('App\Models\CustomerInvoiceItemDetails', 'custInvoiceDirectAutoID', 'custInvoiceDirectAutoID');
    }

    public function receipt_detail()
    {
        return $this->hasMany('App\Models\CustomerReceivePaymentDetail', 'bookingInvCodeSystem', 'custInvoiceDirectAutoID');
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'custInvoiceDirectAutoID')->where('documentSystemID',20);
    }

    public function warehouse()
    {
        return $this->belongsTo('App\Models\WarehouseMaster','wareHouseSystemCode','wareHouseSystemCode');
    }

    public function segment(){
        return $this->belongsTo('App\Models\SegmentMaster','serviceLineSystemID','serviceLineSystemID');
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_custinvoicedirectdet','erp_custinvoicedirectdet.custInvoiceDirectID','erp_custinvoicedirect.custInvoiceDirectAutoID');
    }

    
    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_custinvoicedirect.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeCurrencyJoin($q,$as = 'currencymaster' ,$column = 'currency',$columnAs = 'currencyByName'){
        return $q->leftJoin('currencymaster as '.$as,$as.'.currencyID','=','erp_custinvoicedirect.'.$column)
        ->addSelect($as.".CurrencyName as ".$columnAs);

    }
    public function scopeBankJoin($q,$as = 'erp_bankmaster', $column = 'bankID' , $columnAs = 'bankName')
    {
        return $q->leftJoin('erp_bankmaster as '.$as,$as.'.bankmasterAutoID','erp_custinvoicedirect.'.$column)
        ->addSelect($as.".bankName as ".$columnAs);
    }

    public function scopeWareHouseJoin($q,$as = 'warehousemaster', $column = 'wareHouseSystemCode' , $columnAs = 'wareHouseDescription')
    {
        return $q->leftJoin('warehousemaster as '.$as,$as.'.wareHouseSystemCode','erp_custinvoicedirect.'.$column)
        ->addSelect($as.".wareHouseDescription as ".$columnAs);
    }

    public function scopeCustomerJoin($q,$as = 'customermaster', $column = 'customerID' , $columnAs = 'CustomerName')
    {
        return $q->leftJoin('customermaster as '.$as,$as.'.customerCodeSystem','erp_custinvoicedirect.'.$column)
        ->addSelect($as.".CustomerName as ".$columnAs);
    }

}

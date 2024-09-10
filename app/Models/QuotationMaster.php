<?php

namespace App\Models;

use App\helper\TaxService;
use Eloquent as Model;
use App\Models\QuotationStatus;

/**
 * @SWG\Definition(
 *      definition="QuotationMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="quotationMasterID",
 *          description="quotationMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentDate",
 *          description="documentDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="documentExpDate",
 *          description="documentExpDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="salesPersonID",
 *          description="salesPersonID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="versionNo",
 *          description="versionNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="referenceNo",
 *          description="referenceNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="narration",
 *          description="narration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="Note",
 *          description="Note",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactPersonName",
 *          description="contactPersonName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contactPersonNumber",
 *          description="contactPersonNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerSystemCode",
 *          description="customerSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCode",
 *          description="customerCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerName",
 *          description="customerName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerAddress",
 *          description="customerAddress",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerTelephone",
 *          description="customerTelephone",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerFax",
 *          description="customerFax",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerEmail",
 *          description="customerEmail",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerReceivableAutoID",
 *          description="customerReceivableAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerReceivableSystemGLCode",
 *          description="customerReceivableSystemGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerReceivableGLAccount",
 *          description="customerReceivableGLAccount",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerReceivableDescription",
 *          description="customerReceivableDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerReceivableType",
 *          description="customerReceivableType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyID",
 *          description="transactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrency",
 *          description="transactionCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="transactionExchangeRate",
 *          description="transactionExchangeRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="transactionAmount",
 *          description="transactionAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyDecimalPlaces",
 *          description="transactionCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyID",
 *          description="companyLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrency",
 *          description="companyLocalCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalExchangeRate",
 *          description="companyLocalExchangeRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalAmount",
 *          description="companyLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyDecimalPlaces",
 *          description="companyLocalCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyID",
 *          description="companyReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrency",
 *          description="companyReportingCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingExchangeRate",
 *          description="companyReportingExchangeRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingAmount",
 *          description="companyReportingAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyDecimalPlaces",
 *          description="companyReportingCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrencyID",
 *          description="customerCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrency",
 *          description="customerCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrencyExchangeRate",
 *          description="customerCurrencyExchangeRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrencyAmount",
 *          description="customerCurrencyAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrencyDecimalPlaces",
 *          description="customerCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isDeleted",
 *          description="isDeleted",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deletedEmpID",
 *          description="deletedEmpID",
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
 *          property="approvedYN",
 *          description="approvedYN",
 *          type="integer",
 *          format="int32"
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
 *          property="closedReason",
 *          description="closedReason",
 *          type="string"
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
 *          property="createdUserName",
 *          description="createdUserName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserName",
 *          description="modifiedUserName",
 *          type="string"
 *      )
 * )
 */
class QuotationMaster extends Model
{

    public $table = 'erp_quotationmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'quotationMasterID';

    protected $appends = ['quotation_last_status','isVatEligible'];

    public $fillable = [
        'documentSystemID',
        'quotationType',
        'documentID',
        'quotationCode',
        'serialNumber',
        'documentDate',
        'documentExpDate',
        'salesPersonID',
        'versionNo',
        'referenceNo',
        'narration',
        'Note',
        'contactPersonName',
        'contactPersonNumber',
        'customerSystemCode',
        'customerCode',
        'customerName',
        'customerAddress',
        'customerTelephone',
        'customerFax',
        'customerEmail',
        'customerReceivableAutoID',
        'customerReceivableSystemGLCode',
        'customerReceivableGLAccount',
        'customerReceivableDescription',
        'customerReceivableType',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionExchangeRate',
        'transactionAmount',
        'transactionCurrencyDecimalPlaces',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalExchangeRate',
        'companyLocalAmount',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingExchangeRate',
        'companyReportingAmount',
        'companyReportingCurrencyDecimalPlaces',
        'customerCurrencyID',
        'customerCurrency',
        'customerCurrencyExchangeRate',
        'customerCurrencyAmount',
        'customerCurrencyDecimalPlaces',
        'serviceLineSystemID',
        'isDeleted',
        'deletedEmpID',
        'deletedDate',
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
        'closedYN',
        'closedDate',
        'closedReason',
        'companySystemID',
        'companyID',
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
        'selectedForDeliveryOrder',
        'selectedForSalesOrder',
        'isInDOorCI',
        'isInSO',
        'invoiceStatus',
        'deliveryStatus',
        'orderStatus',
        'timestamp',
        'vatRegisteredYN',
        'customerVATEligible',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'deliveryTerms',
        'panaltyTerms',
        'sent_to_customer',
        'cancelledYN',
        'cancelledByEmpID',
        'cancelledByEmpName',
        'cancelledComments',
        'cancelledDate',
        'cancelledByEmpSystemID',
        'manuallyClosed',
        'manuallyClosedByEmpSystemID',
        'manuallyClosedByEmpID',
        'manuallyClosedByEmpName',
        'manuallyClosedDate',
        'manuallyClosedComment',
        'is_return',
        'leadTime',
        'isBulkItemJobRun'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'quotationMasterID' => 'integer',
        'documentSystemID' => 'string',
        'quotationType' => 'string',
        'documentID' => 'string',
        'quotationCode' => 'string',
        'serialNumber' => 'integer',
        'documentDate' => 'date',
        'documentExpDate' => 'date',
        'salesPersonID' => 'integer',
        'versionNo' => 'integer',
        'referenceNo' => 'string',
        'narration' => 'string',
        'Note' => 'string',
        'contactPersonName' => 'string',
        'contactPersonNumber' => 'string',
        'customerSystemCode' => 'integer',
        'customerCode' => 'string',
        'customerName' => 'string',
        'customerAddress' => 'string',
        'customerTelephone' => 'string',
        'customerFax' => 'string',
        'customerEmail' => 'string',
        'customerReceivableAutoID' => 'integer',
        'customerReceivableSystemGLCode' => 'string',
        'customerReceivableGLAccount' => 'string',
        'customerReceivableDescription' => 'string',
        'customerReceivableType' => 'string',
        'transactionCurrencyID' => 'integer',
        'transactionCurrency' => 'string',
        'transactionExchangeRate' => 'float',
        'transactionAmount' => 'float',
        'transactionCurrencyDecimalPlaces' => 'integer',
        'companyLocalCurrencyID' => 'integer',
        'companyLocalCurrency' => 'string',
        'companyLocalExchangeRate' => 'float',
        'companyLocalAmount' => 'float',
        'companyLocalCurrencyDecimalPlaces' => 'integer',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingCurrency' => 'string',
        'companyReportingExchangeRate' => 'float',
        'companyReportingAmount' => 'float',
        'companyReportingCurrencyDecimalPlaces' => 'integer',
        'customerCurrencyID' => 'integer',
        'customerCurrency' => 'string',
        'customerCurrencyExchangeRate' => 'float',
        'customerCurrencyAmount' => 'float',
        'customerCurrencyDecimalPlaces' => 'integer',
        'serviceLineSystemID' => 'integer',
        'isDeleted' => 'integer',
        'deletedEmpID' => 'integer',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'approvedYN' => 'integer',
        'approvedEmpSystemID' => 'integer',
        'approvedbyEmpID' => 'string',
        'approvedbyEmpName' => 'string',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'RollLevForApp_curr' => 'integer',
        'closedYN' => 'integer',
        'closedReason' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string',
        'selectedForDeliveryOrder' => 'integer',
        'selectedForSalesOrder' => 'integer',
        'isInDOorCI' => 'integer',
        'isInSO' => 'integer',
        'invoiceStatus' => 'integer',
        'deliveryStatus' => 'integer',
        'orderStatus' => 'integer',
        'vatRegisteredYN' => 'integer',
        'customerVATEligible' => 'integer',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'deliveryTerms'  => 'string',
        'panaltyTerms'  => 'string',
        'sent_to_customer' => 'integer',
        'is_return' => 'boolean',
        'leadTime' => 'float',
        'isBulkItemJobRun' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function getQuotationLastStatusAttribute(){
        return QuotationStatus::getLastStatus($this->quotationMasterID);
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

    public function detail()
    {
        return $this->hasMany('App\Models\QuotationDetails', 'quotationMasterID', 'quotationMasterID');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'quotationMasterID');
    }

    public function sales_person()
    {
        return $this->belongsTo('App\Models\SalesPersonMaster', 'salesPersonID', 'salesPersonID');
    }

    public function segment()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineSystemID', 'serviceLineSystemID');
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

    public function getIsVatEligibleAttribute()
    {
        return TaxService::checkPOVATEligible($this->customerVATEligible,$this->vatRegisteredYN,$this->documentSystemID);
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'quotationMasterID')->whereIn('documentSystemID',[67,68]);
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\CustomerMaster', 'customerSystemCode', 'customerCodeSystem');
    }

    public function paymentTerms_by()
    {
        return $this->hasMany('\App\Models\SoPaymentTerms','soID','quotationMasterID');
    }

    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_quotationmaster.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_quotationmaster.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }

    
    public function scopeSegmentJoin($q,$as = 'serviceline', $column = 'serviceLineSystemID' , $columnAs = 'ServiceLineDes')
    {
        return $q->leftJoin('serviceline as '.$as,$as.'.serviceLineSystemID','erp_quotationmaster.'.$column)
        ->addSelect($as.".ServiceLineDes as ".$columnAs);
    }

    public function scopeSalesPersonJoin($q,$as = 'erp_salespersonmaster', $column = 'salesPersonID' , $columnAs = 'SalesPersonName')
    {
        return $q->leftJoin('erp_salespersonmaster as '.$as,$as.'.salesPersonID','erp_quotationmaster.'.$column)
        ->addSelect($as.".SalesPersonName as ".$columnAs);
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_quotationdetails','erp_quotationdetails.quotationMasterID','erp_quotationmaster.quotationMasterID');
    }
}

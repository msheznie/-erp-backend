<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSInvoiceSource",
 *      required={""},
 *      @SWG\Property(
 *          property="invoiceID",
 *          description="invoiceID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentCode",
 *          description="documentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoiceSequenceNo",
 *          description="invoiceSequenceNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoiceCode",
 *          description="invoiceCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isGroupBasedTax",
 *          description="isGroupBasedTax",
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
 *          property="customerCode",
 *          description="customerCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="counterID",
 *          description="counterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="shiftID",
 *          description="shiftID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="memberID",
 *          description="memberID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="memberName",
 *          description="memberName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="invoiceDate",
 *          description="invoiceDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="subTotal",
 *          description="subTotal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="discountPer",
 *          description="discountPer",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="discountAmount",
 *          description="discountAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="generalDiscountPercentage",
 *          description="generalDiscountPercentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="generalDiscountAmount",
 *          description="generalDiscountAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="netTotal",
 *          description="netTotal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="paidAmount",
 *          description="paidAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="balanceAmount",
 *          description="balanceAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="cashAmount",
 *          description="cashAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="chequeAmount",
 *          description="chequeAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="chequeNo",
 *          description="chequeNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chequeDate",
 *          description="chequeDate",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="cardAmount",
 *          description="cardAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="creditNoteID",
 *          description="creditNoteID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="creditNoteAmount",
 *          description="creditNoteAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="cardNumber",
 *          description="cardNumber",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cardRefNo",
 *          description="cardRefNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cardBank",
 *          description="cardBank",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isCreditSales",
 *          description="isCreditSales",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="creditSalesAmount",
 *          description="creditSalesAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseAutoID",
 *          description="wareHouseAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyID",
 *          description="transactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrency",
 *          description="Document transaction currency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="transactionExchangeRate",
 *          description="Always 1",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyDecimalPlaces",
 *          description="Decimal places of transaction currency ",
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
 *          description="Local currency of company in company master",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalExchangeRate",
 *          description="Exchange rate against transaction currency",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyDecimalPlaces",
 *          description="Decimal places of company currency",
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
 *          description="Reporting currency of company in company master",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingExchangeRate",
 *          description="Exchange rate against transaction currency ",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyDecimalPlaces",
 *          description="Decimal places of company currency",
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
 *          description="Default currency of supplier ",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrencyExchangeRate",
 *          description="Exchange rate against transaction currency",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="customerCurrencyDecimalPlaces",
 *          description="Decimal places of Supplier currency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="segmentID",
 *          description="segmentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyCode",
 *          description="companyCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerReceivableAutoID",
 *          description="customerReceivableAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankGLAutoID",
 *          description="bankGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyID",
 *          description="bankCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrency",
 *          description="Document transaction currency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyExchangeRate",
 *          description="Always 1",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyDecimalPlaces",
 *          description="Decimal places of transaction currency ",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyAmount",
 *          description="bankCurrencyAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="isVoid",
 *          description="0 - not void, 1 canceled",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="voidBy",
 *          description="bill cancelled by",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="voidDatetime",
 *          description="voidDatetime",
 *          type="string",
 *          format="date-time"
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
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
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
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="promotionID",
 *          description="promotionID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="promotiondiscount",
 *          description="promotiondiscount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="promotiondiscountAmount",
 *          description="promotiondiscountAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="isPromotion",
 *          description="isPromotion",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transaction_log_id",
 *          description="transaction_log_id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="mapping_master_id",
 *          description="mapping_master_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSInvoiceSource extends Model
{

    public $table = 'pos_source_invoice';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'documentSystemCode',
        'documentCode',
        'serialNo',
        'invoiceSequenceNo',
        'invoiceCode',
        'isGroupBasedTax',
        'customerID',
        'customerCode',
        'counterID',
        'shiftID',
        'memberID',
        'memberName',
        'invoiceDate',
        'subTotal',
        'discountPer',
        'discountAmount',
        'generalDiscountPercentage',
        'generalDiscountAmount',
        'netTotal',
        'paidAmount',
        'balanceAmount',
        'cashAmount',
        'chequeAmount',
        'chequeNo',
        'chequeDate',
        'cardAmount',
        'creditNoteID',
        'creditNoteAmount',
        'cardNumber',
        'cardRefNo',
        'cardBank',
        'isCreditSales',
        'creditSalesAmount',
        'wareHouseAutoID',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionExchangeRate',
        'transactionCurrencyDecimalPlaces',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalExchangeRate',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingExchangeRate',
        'companyReportingCurrencyDecimalPlaces',
        'customerCurrencyID',
        'customerCurrency',
        'customerCurrencyExchangeRate',
        'customerCurrencyDecimalPlaces',
        'segmentID',
        'companyID',
        'companyCode', 
        'isVoid',
        'voidBy',
        'voidDatetime',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdUserName',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'modifiedDateTime',
        'timestamp',
        'promotionID',
        'promotiondiscount',
        'promotiondiscountAmount',
        'isPromotion',
        'transaction_log_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'invoiceID' => 'integer',
        'documentSystemCode' => 'string',
        'documentCode' => 'string',
        'serialNo' => 'integer',
        'invoiceSequenceNo' => 'integer',
        'invoiceCode' => 'string',
        'isGroupBasedTax' => 'integer',
        'customerID' => 'integer',
        'customerCode' => 'string',
        'counterID' => 'integer',
        'shiftID' => 'integer',
        'memberID' => 'string',
        'memberName' => 'string',
        'invoiceDate' => 'date',
        'subTotal' => 'float',
        'discountPer' => 'float',
        'discountAmount' => 'float',
        'generalDiscountPercentage' => 'float',
        'generalDiscountAmount' => 'float',
        'netTotal' => 'float',
        'paidAmount' => 'float',
        'balanceAmount' => 'float',
        'cashAmount' => 'float',
        'chequeAmount' => 'float',
        'chequeNo' => 'string',
        'chequeDate' => 'date',
        'cardAmount' => 'float',
        'creditNoteID' => 'integer',
        'creditNoteAmount' => 'float',
        'cardNumber' => 'integer',
        'cardRefNo' => 'integer',
        'cardBank' => 'integer',
        'isCreditSales' => 'integer',
        'creditSalesAmount' => 'float',
        'wareHouseAutoID' => 'integer',
        'transactionCurrencyID' => 'integer',
        'transactionCurrency' => 'string',
        'transactionExchangeRate' => 'float',
        'transactionCurrencyDecimalPlaces' => 'integer',
        'companyLocalCurrencyID' => 'integer',
        'companyLocalCurrency' => 'string',
        'companyLocalExchangeRate' => 'float',
        'companyLocalCurrencyDecimalPlaces' => 'integer',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingCurrency' => 'string',
        'companyReportingExchangeRate' => 'float',
        'companyReportingCurrencyDecimalPlaces' => 'integer',
        'customerCurrencyID' => 'integer',
        'customerCurrency' => 'string',
        'customerCurrencyExchangeRate' => 'float',
        'customerCurrencyDecimalPlaces' => 'integer',
        'segmentID' => 'integer',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'isVoid' => 'integer',
        'voidBy' => 'integer',
        'voidDatetime' => 'datetime',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'createdDateTime' => 'datetime',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string',
        'modifiedDateTime' => 'datetime',
        'timestamp' => 'datetime',
        'promotionID' => 'integer',
        'promotiondiscount' => 'float',
        'promotiondiscountAmount' => 'float',
        'isPromotion' => 'integer',
        'transaction_log_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];
    public function wareHouseMaster(){
        return $this->hasOne(WarehouseMaster::class, 'wareHouseSystemCode', 'wareHouseAutoID');
    }
    public function invoiceDetailSource(){ 
        return $this->hasMany('App\Models\POSInvoiceSourceDetail', 'invoiceID', 'invoiceID');
    }
    public function invoicePaymentSource(){ 
        return $this->hasMany('App\Models\POSSourceInvoicePayment', 'invoiceID', 'invoiceID');
    }
    public function employee(){ 
        return $this->hasOne('App\Models\Employee','employeeSystemID','createdUserID');
    }

    public function bankGLEntries() {
        return $this->hasMany(POSBankGLEntries::class, 'invoiceID', 'invoiceID');
    }
}

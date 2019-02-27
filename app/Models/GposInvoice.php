<?php
/**
 * =============================================
 * -- File Name : GposInvoice.php
 * -- Project Name : ERP
 * -- Module Name :  General pos Invoice
 * -- Author : Fayas
 * -- Create date : 22 - January 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="GposInvoice",
 *      required={""},
 *      @SWG\Property(
 *          property="invoiceID",
 *          description="invoiceID",
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
 *          property="segmentCode",
 *          description="segmentCode",
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
 *          property="financialYearID",
 *          description="financialYearID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financialPeriodID",
 *          description="financialPeriodID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="FYBegin",
 *          description="FYBegin",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="FYEnd",
 *          description="FYEnd",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="FYPeriodDateFrom",
 *          description="FYPeriodDateFrom",
 *          type="string",
 *          format="date"
 *      ),
 *      @SWG\Property(
 *          property="FYPeriodDateTo",
 *          description="FYPeriodDateTo",
 *          type="string",
 *          format="date"
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
 *          property="memberContactNo",
 *          description="memberContactNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="memberEmail",
 *          description="memberEmail",
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
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="discountPercentage",
 *          description="discountPercentage",
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
 *          property="netTotal",
 *          description="netTotal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="paidAmount",
 *          description="paidAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="balanceAmount",
 *          description="balanceAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="cashAmount",
 *          description="cashAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="chequeAmount",
 *          description="chequeAmount",
 *          type="number",
 *          format="float"
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
 *          format="float"
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
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="giftCardID",
 *          description="giftCardID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="giftCardAmount",
 *          description="giftCardAmount",
 *          type="number",
 *          format="float"
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
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseAutoID",
 *          description="wareHouseAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseCode",
 *          description="wareHouseCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseLocation",
 *          description="wareHouseLocation",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="wareHouseDescription",
 *          description="wareHouseDescription",
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
 *          property="customerCurrencyDecimalPlaces",
 *          description="customerCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
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
 *          property="bankGLAutoID",
 *          description="bankGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankSystemGLCode",
 *          description="bankSystemGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bankGLAccount",
 *          description="bankGLAccount",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bankGLDescription",
 *          description="bankGLDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bankGLType",
 *          description="bankGLType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyID",
 *          description="bankCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrency",
 *          description="bankCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyExchangeRate",
 *          description="bankCurrencyExchangeRate",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyDecimalPlaces",
 *          description="bankCurrencyDecimalPlaces",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyAmount",
 *          description="bankCurrencyAmount",
 *          type="number",
 *          format="float"
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
class GposInvoice extends Model
{

    public $table = 'erp_gpos_invoice';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';

    protected $primaryKey = 'invoiceID';

    public $fillable = [
        'segmentID',
        'segmentCode',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'invoiceSequenceNo',
        'invoiceCode',
        'financialYearID',
        'financialPeriodID',
        'FYBegin',
        'FYEnd',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'customerID',
        'customerCode',
        'counterID',
        'shiftID',
        'memberID',
        'memberName',
        'memberContactNo',
        'memberEmail',
        'invoiceDate',
        'subTotal',
        'discountPercentage',
        'discountAmount',
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
        'giftCardID',
        'giftCardAmount',
        'cardNumber',
        'cardRefNo',
        'cardBank',
        'isCreditSales',
        'creditSalesAmount',
        'wareHouseAutoID',
        'wareHouseCode',
        'wareHouseLocation',
        'wareHouseDescription',
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
        'customerReceivableAutoID',
        'customerReceivableSystemGLCode',
        'customerReceivableGLAccount',
        'customerReceivableDescription',
        'customerReceivableType',
        'bankGLAutoID',
        'bankSystemGLCode',
        'bankGLAccount',
        'bankGLDescription',
        'bankGLType',
        'bankCurrencyID',
        'bankCurrency',
        'bankCurrencyExchangeRate',
        'bankCurrencyDecimalPlaces',
        'bankCurrencyAmount',
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
        'isHold',
        'isCancelled',
        'reCalledYN',
        'modifiedUserSystemID',
        'createdUserSystemID',
        'isVoid',
        'voidBy',
        'voidDatetime'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'invoiceID' => 'integer',
        'segmentID' => 'integer',
        'segmentCode' => 'string',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'invoiceSequenceNo' => 'integer',
        'invoiceCode' => 'string',
        'financialYearID' => 'integer',
        'financialPeriodID' => 'integer',
        'FYBegin' => 'string',
        'FYEnd' => 'string',
        'FYPeriodDateFrom' => 'string',
        'FYPeriodDateTo' => 'string',
        'customerID' => 'integer',
        'customerCode' => 'string',
        'counterID' => 'integer',
        'shiftID' => 'integer',
        'memberID' => 'string',
        'memberName' => 'string',
        'memberContactNo' => 'string',
        'memberEmail' => 'string',
        'invoiceDate' => 'string',
        'subTotal' => 'float',
        'discountPercentage' => 'float',
        'discountAmount' => 'float',
        'netTotal' => 'float',
        'paidAmount' => 'float',
        'balanceAmount' => 'float',
        'cashAmount' => 'float',
        'chequeAmount' => 'float',
        'chequeNo' => 'string',
        'chequeDate' => 'string',
        'cardAmount' => 'float',
        'creditNoteID' => 'integer',
        'creditNoteAmount' => 'float',
        'giftCardID' => 'integer',
        'giftCardAmount' => 'float',
        'cardNumber' => 'integer',
        'cardRefNo' => 'integer',
        'cardBank' => 'integer',
        'isCreditSales' => 'integer',
        'creditSalesAmount' => 'float',
        'wareHouseAutoID' => 'integer',
        'wareHouseCode' => 'string',
        'wareHouseLocation' => 'string',
        'wareHouseDescription' => 'string',
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
        'customerReceivableAutoID' => 'integer',
        'customerReceivableSystemGLCode' => 'string',
        'customerReceivableGLAccount' => 'string',
        'customerReceivableDescription' => 'string',
        'customerReceivableType' => 'string',
        'bankGLAutoID' => 'integer',
        'bankSystemGLCode' => 'string',
        'bankGLAccount' => 'string',
        'bankGLDescription' => 'string',
        'bankGLType' => 'string',
        'bankCurrencyID' => 'integer',
        'bankCurrency' => 'string',
        'bankCurrencyExchangeRate' => 'float',
        'bankCurrencyDecimalPlaces' => 'integer',
        'bankCurrencyAmount' => 'float',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string',
        'isHold' => 'integer',
        'isCancelled' => 'integer',
        'reCalledYN' => 'integer',
        'modifiedUserSystemID' => 'integer',
        'createdUserSystemID' => 'integer',
        'isVoid' => 'integer',
        'voidBy' => 'integer',
        'voidDatetime' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function details()
    {
        return $this->hasMany('App\Models\GposInvoiceDetail','invoiceID','invoiceID');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function warehouse_by()
    {
        return $this->belongsTo('App\Models\WarehouseMaster','wareHouseAutoID','wareHouseSystemCode');
    }
    public function company(){
        return $this->belongsTo('App\Models\Company','companySystemID','companySystemID');
    }

    public function transaction_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'transactionCurrencyID','currencyID');
    }

}

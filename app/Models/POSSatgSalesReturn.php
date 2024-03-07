<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSSatgSalesReturn",
 *      required={""},
 *      @SWG\Property(
 *          property="salesReturnID",
 *          description="salesReturnID",
 *          type="integer",
 *          format="int32"
 *      ),
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
 *          property="salesReturnDate",
 *          description="salesReturnDate",
 *          type="string",
 *          format="date"
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
 *          property="subTotal",
 *          description="subTotal",
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
 *          property="returnMode",
 *          description="exchange=1, Refund=2,  credit-to-customer=3",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="isRefund",
 *          description="isRefund",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="refundAmount",
 *          description="refundAmount",
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
 *          property="customerCurrencyAmount",
 *          description="Transaction amount in supplier currency ",
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
 *          property="customerReceivableAutoID",
 *          description="customerReceivableAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="isGroupBasedTax",
 *          description="isGroupBasedTax",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transaction_log_id",
 *          description="transaction_log_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSSatgSalesReturn extends Model
{

    public $table = 'pos_stag_salesreturn';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'invoiceID',
        'documentSystemCode',
        'documentCode',
        'serialNo',
        'customerID',
        'customerCode',
        'counterID',
        'shiftID',
        'salesReturnDate',
        'discountPer',
        'discountAmount',
        'generalDiscountPercentage',
        'generalDiscountAmount',
        'promotionID',
        'promotiondiscount',
        'promotiondiscountAmount',
        'subTotal',
        'netTotal',
        'returnMode',
        'isRefund',
        'refundAmount',
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
        'customerCurrencyAmount',
        'customerCurrencyDecimalPlaces',
        'segmentID',
        'companyID',
        'companyCode',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'customerReceivableAutoID',
        'timestamp',
        'isGroupBasedTax',
        'transaction_log_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'salesReturnID' => 'integer',
        'invoiceID' => 'integer',
        'documentSystemCode' => 'string',
        'documentCode' => 'string',
        'serialNo' => 'integer',
        'customerID' => 'integer',
        'customerCode' => 'string',
        'counterID' => 'integer',
        'shiftID' => 'integer',
        'salesReturnDate' => 'date',
        'discountPer' => 'float',
        'discountAmount' => 'float',
        'generalDiscountPercentage' => 'float',
        'generalDiscountAmount' => 'float',
        'promotionID' => 'integer',
        'promotiondiscount' => 'float',
        'promotiondiscountAmount' => 'float',
        'subTotal' => 'float',
        'netTotal' => 'float',
        'returnMode' => 'integer',
        'isRefund' => 'boolean',
        'refundAmount' => 'float',
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
        'customerCurrencyAmount' => 'float',
        'customerCurrencyDecimalPlaces' => 'integer',
        'segmentID' => 'integer',
        'companyID' => 'integer',
        'companyCode' => 'string',
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedDateTime' => 'datetime',
        'modifiedUserName' => 'string',
        'customerReceivableAutoID' => 'integer',
        'timestamp' => 'datetime',
        'isGroupBasedTax' => 'integer',
        'transaction_log_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'invoiceID' => 'required'
    ];

    
}

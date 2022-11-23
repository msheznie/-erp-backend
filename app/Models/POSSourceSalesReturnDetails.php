<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="POSSourceSalesReturnDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="salesReturnDetailID",
 *          description="salesReturnDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
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
 *          property="invoiceDetailID",
 *          description="invoiceDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemAutoID",
 *          description="itemAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemCategory",
 *          description="itemCategory",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="financeCategory",
 *          description="financeCategory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemFinanceCategory",
 *          description="itemFinanceCategory",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemFinanceCategorySub",
 *          description="itemFinanceCategorySub",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="defaultUOMID",
 *          description="defaultUOMID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasure",
 *          description="unitOfMeasure",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="UOMID",
 *          description="UOMID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="conversionRateUOM",
 *          description="conversionRateUOM",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="qty",
 *          description="qty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="price",
 *          description="price",
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
 *          property="transactionAmount",
 *          description="transactionAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyDecimalPlaces",
 *          description="transactionCurrencyDecimalPlaces",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="transactionExchangeRate",
 *          description="transactionExchangeRate",
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
 *          property="companyLocalCurrency",
 *          description="companyLocalCurrency",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalAmount",
 *          description="companyLocalAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalExchangeRate",
 *          description="companyLocalExchangeRate",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalCurrencyDecimalPlaces",
 *          description="companyLocalCurrencyDecimalPlaces",
 *          type="boolean"
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
 *          property="companyReportingAmount",
 *          description="companyReportingAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyDecimalPlaces",
 *          description="companyReportingCurrencyDecimalPlaces",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingExchangeRate",
 *          description="companyReportingExchangeRate",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="taxCalculationformulaID",
 *          description="taxCalculationformulaID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxAmount",
 *          description="taxAmount",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="expenseGLAutoID",
 *          description="expenseGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="revenueGLAutoID",
 *          description="revenueGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assetGLAutoID",
 *          description="assetGLAutoID",
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
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="transaction_log_id",
 *          description="transaction_log_id",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class POSSourceSalesReturnDetails extends Model
{

    public $table = 'pos_source_salesreturndetails';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';




    public $fillable = [
        'salesReturnID',
        'invoiceID',
        'invoiceDetailID',
        'itemAutoID',
        'itemCategory',
        'financeCategory',
        'itemFinanceCategory',
        'itemFinanceCategorySub',
        'defaultUOMID',
        'unitOfMeasure',
        'UOMID',
        'conversionRateUOM',
        'qty',
        'price',
        'discountPer',
        'generalDiscountPercentage',
        'generalDiscountAmount',
        'promotiondiscount',
        'promotiondiscountAmount',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionAmount',
        'transactionCurrencyDecimalPlaces',
        'transactionExchangeRate',
        'companyLocalCurrencyID',
        'companyLocalCurrency',
        'companyLocalAmount',
        'companyLocalExchangeRate',
        'companyLocalCurrencyDecimalPlaces',
        'companyReportingCurrencyID',
        'companyReportingCurrency',
        'companyReportingAmount',
        'companyReportingCurrencyDecimalPlaces',
        'companyReportingExchangeRate',
        'taxCalculationformulaID',
        'taxAmount',
        'expenseGLAutoID',
        'revenueGLAutoID',
        'assetGLAutoID',
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
        'timestamp',
        'transaction_log_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'salesReturnDetailID' => 'integer',
        'salesReturnID' => 'integer',
        'invoiceID' => 'integer',
        'invoiceDetailID' => 'integer',
        'itemAutoID' => 'integer',
        'itemCategory' => 'string',
        'financeCategory' => 'integer',
        'itemFinanceCategory' => 'integer',
        'itemFinanceCategorySub' => 'integer',
        'defaultUOMID' => 'integer',
        'unitOfMeasure' => 'string',
        'UOMID' => 'integer',
        'conversionRateUOM' => 'float',
        'qty' => 'float',
        'price' => 'float',
        'discountPer' => 'float',
        'generalDiscountPercentage' => 'float',
        'generalDiscountAmount' => 'float',
        'promotiondiscount' => 'float',
        'promotiondiscountAmount' => 'float',
        'transactionCurrencyID' => 'integer',
        'transactionCurrency' => 'string',
        'transactionAmount' => 'float',
        'transactionCurrencyDecimalPlaces' => 'boolean',
        'transactionExchangeRate' => 'float',
        'companyLocalCurrencyID' => 'integer',
        'companyLocalCurrency' => 'string',
        'companyLocalAmount' => 'float',
        'companyLocalExchangeRate' => 'float',
        'companyLocalCurrencyDecimalPlaces' => 'boolean',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingCurrency' => 'string',
        'companyReportingAmount' => 'float',
        'companyReportingCurrencyDecimalPlaces' => 'boolean',
        'companyReportingExchangeRate' => 'float',
        'taxCalculationformulaID' => 'integer',
        'taxAmount' => 'string',
        'expenseGLAutoID' => 'integer',
        'revenueGLAutoID' => 'integer',
        'assetGLAutoID' => 'integer',
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
        'timestamp' => 'datetime',
        'transaction_log_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'salesReturnID' => 'required'
    ];
    public function item_assigned()
    {
        return $this->hasOne('App\Models\ItemAssigned', 'itemCodeSystem', 'itemAutoID');
    }
    
}

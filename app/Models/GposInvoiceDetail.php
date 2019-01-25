<?php
/**
 * =============================================
 * -- File Name : GposInvoiceDetail.php
 * -- Project Name : ERP
 * -- Module Name :  General pos Invoice Detail
 * -- Author : Fayas
 * -- Create date : 22 - January 2019
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="GposInvoiceDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="invoiceDetailsID",
 *          description="invoiceDetailsID",
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
 *          property="itemAutoID",
 *          description="itemAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemSystemCode",
 *          description="itemSystemCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemDescription",
 *          description="itemDescription",
 *          type="string"
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
 *          property="defaultUOM",
 *          description="defaultUOM",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasure",
 *          description="unitOfMeasure",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="conversionRateUOM",
 *          description="conversionRateUOM",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="expenseGLAutoID",
 *          description="expenseGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="expenseGLCode",
 *          description="expenseGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="expenseSystemGLCode",
 *          description="expenseSystemGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="expenseGLDescription",
 *          description="expenseGLDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="expenseGLType",
 *          description="expenseGLType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="revenueGLAutoID",
 *          description="revenueGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="revenueGLCode",
 *          description="revenueGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="revenueSystemGLCode",
 *          description="revenueSystemGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="revenueGLDescription",
 *          description="revenueGLDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="revenueGLType",
 *          description="revenueGLType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="assetGLAutoID",
 *          description="assetGLAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="assetGLCode",
 *          description="assetGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="assetSystemGLCode",
 *          description="assetSystemGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="assetGLDescription",
 *          description="assetGLDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="assetGLType",
 *          description="assetGLType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="qty",
 *          description="qty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="price",
 *          description="price",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="totalAmount",
 *          description="totalAmount",
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
 *          property="wacAmount",
 *          description="wacAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="netAmount",
 *          description="netAmount",
 *          type="number",
 *          format="float"
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
 *          property="transactionAmountBeforeDiscount",
 *          description="transactionAmountBeforeDiscount",
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
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="transactionExchangeRate",
 *          description="transactionExchangeRate",
 *          type="number",
 *          format="float"
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
 *          format="float"
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
 *          format="float"
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
class GposInvoiceDetail extends Model
{

    public $table = 'erp_gpos_invoicedetail';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';

    protected $primaryKey = 'invoiceDetailsID';

    public $fillable = [
        'invoiceID',
        'companySystemID',
        'companyID',
        'itemAutoID',
        'itemSystemCode',
        'itemDescription',
        'itemCategory',
        'financeCategory',
        'itemFinanceCategory',
        'itemFinanceCategorySub',
        'defaultUOM',
        'unitOfMeasure',
        'conversionRateUOM',
        'expenseGLAutoID',
        'expenseGLCode',
        'expenseSystemGLCode',
        'expenseGLDescription',
        'expenseGLType',
        'revenueGLAutoID',
        'revenueGLCode',
        'revenueSystemGLCode',
        'revenueGLDescription',
        'revenueGLType',
        'assetGLAutoID',
        'assetGLCode',
        'assetSystemGLCode',
        'assetGLDescription',
        'assetGLType',
        'qty',
        'price',
        'totalAmount',
        'discountPercentage',
        'discountAmount',
        'wacAmount',
        'netAmount',
        'transactionCurrencyID',
        'transactionCurrency',
        'transactionAmountBeforeDiscount',
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
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'createdUserName',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedDateTime',
        'modifiedUserName',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'invoiceDetailsID' => 'integer',
        'invoiceID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'itemAutoID' => 'integer',
        'itemSystemCode' => 'string',
        'itemDescription' => 'string',
        'itemCategory' => 'string',
        'financeCategory' => 'integer',
        'itemFinanceCategory' => 'integer',
        'itemFinanceCategorySub' => 'integer',
        'defaultUOM' => 'string',
        'unitOfMeasure' => 'string',
        'conversionRateUOM' => 'float',
        'expenseGLAutoID' => 'integer',
        'expenseGLCode' => 'string',
        'expenseSystemGLCode' => 'string',
        'expenseGLDescription' => 'string',
        'expenseGLType' => 'string',
        'revenueGLAutoID' => 'integer',
        'revenueGLCode' => 'string',
        'revenueSystemGLCode' => 'string',
        'revenueGLDescription' => 'string',
        'revenueGLType' => 'string',
        'assetGLAutoID' => 'integer',
        'assetGLCode' => 'string',
        'assetSystemGLCode' => 'string',
        'assetGLDescription' => 'string',
        'assetGLType' => 'string',
        'qty' => 'float',
        'price' => 'float',
        'totalAmount' => 'float',
        'discountPercentage' => 'float',
        'discountAmount' => 'float',
        'wacAmount' => 'float',
        'netAmount' => 'float',
        'transactionCurrencyID' => 'integer',
        'transactionCurrency' => 'string',
        'transactionAmountBeforeDiscount' => 'float',
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
        'createdUserGroup' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdUserName' => 'string',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedUserName' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function unit(){
        return $this->hasOne('App\Models\Unit','UnitID','unitOfMeasure');
    }

    public function item_ledger(){
        return $this->hasMany('App\Models\ErpItemLedger','itemSystemCode','itemAutoID');
    }
}

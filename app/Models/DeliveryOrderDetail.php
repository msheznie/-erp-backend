<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DeliveryOrderDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="deliveryOrderDetailID",
 *          description="deliveryOrderDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deliveryOrderID",
 *          description="deliveryOrderID",
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
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemCodeSystem",
 *          description="itemCodeSystem",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemPrimaryCode",
 *          description="itemPrimaryCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemDescription",
 *          description="itemDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemUnitOfMeasure",
 *          description="itemUnitOfMeasure",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasureIssued",
 *          description="unitOfMeasureIssued",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="convertionMeasureVal",
 *          description="convertionMeasureVal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="qtyIssued",
 *          description="qtyIssued",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="qtyIssuedDefaultMeasure",
 *          description="qtyIssuedDefaultMeasure",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="currentStockQty",
 *          description="currentStockQty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="currentWareHouseStockQty",
 *          description="currentWareHouseStockQty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="currentStockQtyInDamageReturn",
 *          description="currentStockQtyInDamageReturn",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="wacValueLocal",
 *          description="wacValueLocal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="wacValueReporting",
 *          description="wacValueReporting",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="unitTransactionAmount",
 *          description="unitTransactionAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="discountPercentage",
 *          description="discountPercentage",
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
 *          property="transactionCurrencyID",
 *          description="transactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyER",
 *          description="transactionCurrencyER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="transactionAmount",
 *          description="transactionAmount",
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
 *          property="companyLocalCurrencyER",
 *          description="companyLocalCurrencyER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyLocalAmount",
 *          description="companyLocalAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyID",
 *          description="companyReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyER",
 *          description="companyReportingCurrencyER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingAmount",
 *          description="companyReportingAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class DeliveryOrderDetail extends Model
{

    public $table = 'erp_delivery_order_detail';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'deliveryOrderDetailID';


    public $fillable = [
        'deliveryOrderID',
        'companySystemID',
        'documentSystemID',
        'itemCodeSystem',
        'itemPrimaryCode',
        'returnQty',
        'approvedReturnQty',
        'itemDescription',
        'itemUnitOfMeasure',
        'unitOfMeasureIssued',
        'convertionMeasureVal',
        'itemFinanceCategoryID',
        'itemFinanceCategorySubID',
        'financeGLcodebBSSystemID',
        'financeGLcodebBS',
        'financeGLcodePLSystemID',
        'financeGLcodePL',
        'financeCogsGLcodePLSystemID',
        'financeCogsGLcodePL',
        'financeGLcodeRevenueSystemID',
        'financeGLcodeRevenue',
        'qtyIssued',
        'trackingType',
        'qtyIssuedDefaultMeasure',
        'currentStockQty',
        'currentWareHouseStockQty',
        'currentStockQtyInDamageReturn',
        'wacValueLocal',
        'wacValueReporting',
        'unitTransactionAmount',
        'discountPercentage',
        'discountAmount',
        'transactionCurrencyID',
        'transactionCurrencyER',
        'transactionAmount',
        'companyLocalCurrencyID',
        'companyLocalCurrencyER',
        'companyLocalAmount',
        'companyReportingCurrencyID',
        'companyReportingCurrencyER',
        'companyReportingAmount',
        'quotationMasterID',
        'quotationDetailsID',
        'remarks',
        'requestedQty',
        'balanceQty',
        'fullyReceived',
        'fullyReturned',
        'invQty',
        'timestamp',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'VATApplicableOn'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'deliveryOrderDetailID' => 'integer',
        'deliveryOrderID' => 'integer',
        'companySystemID' => 'integer',
        'documentSystemID' => 'integer',
        'vatMasterCategoryID' => 'integer',
        'vatSubCategoryID' => 'integer',
        'itemCodeSystem' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'itemUnitOfMeasure' => 'integer',
        'unitOfMeasureIssued' => 'integer',
        'convertionMeasureVal' => 'float',
        'itemFinanceCategoryID' => 'integer',
        'itemFinanceCategorySubID' => 'integer',
        'financeGLcodebBSSystemID' => 'integer',
        'financeGLcodebBS' => 'string',
        'financeGLcodePLSystemID' => 'integer',
        'financeGLcodePL' => 'string',
        'financeCogsGLcodePLSystemID' => 'integer',
        'financeCogsGLcodePL' => 'string',
        'financeGLcodeRevenueSystemID' => 'integer',
        'financeGLcodeRevenue' => 'string',
        'qtyIssued' => 'float',
        'qtyIssuedDefaultMeasure' => 'float',
        'currentStockQty' => 'float',
        'currentWareHouseStockQty' => 'float',
        'currentStockQtyInDamageReturn' => 'float',
        'wacValueLocal' => 'float',
        'wacValueReporting' => 'float',
        'unitTransactionAmount' => 'float',
        'discountPercentage' => 'float',
        'discountAmount' => 'float',
        'trackingType' => 'integer',
        'transactionCurrencyID' => 'integer',
        'transactionCurrencyER' => 'float',
        'transactionAmount' => 'float',
        'companyLocalCurrencyID' => 'integer',
        'companyLocalCurrencyER' => 'float',
        'companyLocalAmount' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingCurrencyER' => 'float',
        'companyReportingAmount' => 'float',
        'quotationMasterID' => 'integer',
        'quotationDetailsID' => 'integer',
        'remarks' => 'string',
        'requestedQty'  => 'float',
        'balanceQty'  => 'float',
        'fullyReceived' => 'integer',
        'fullyReturned' => 'integer',
        'invQty' => 'float',
        'returnQty' => 'float',
        'approvedReturnQty' => 'float',
        'timestamp' => 'datetime',
        'VATPercentage' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'VATApplicableOn' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'deliveryOrderID' => 'required'
    ];

    public function uom_default(){
        return $this->belongsTo('App\Models\Unit','itemUnitOfMeasure','UnitID');
    }

    public function uom_issuing(){
        return $this->belongsTo('App\Models\Unit','unitOfMeasureIssued','UnitID');
    }

    public function item_by(){
        return $this->belongsTo('App\Models\ItemMaster','itemCodeSystem','itemCodeSystem');
    }

    public function master(){
        return $this->belongsTo('App\Models\DeliveryOrder','deliveryOrderID','deliveryOrderID');
    }

    public function quotation(){
        return $this->belongsTo('App\Models\QuotationMaster','quotationMasterID','quotationMasterID');
    }

    public function sales_quotation_detail(){
        return $this->belongsTo('App\Models\QuotationDetails','quotationDetailsID','quotationDetailsID');
    }

    public function invoice_detail() {
        return $this->hasMany('App\Models\CustomerInvoiceItemDetails','deliveryOrderDetailID','deliveryOrderDetailID');
    }

    public function sales_return() {
        return $this->hasOne('App\Models\SalesReturnDetail','deliveryOrderDetailID','deliveryOrderDetailID');
    }
}

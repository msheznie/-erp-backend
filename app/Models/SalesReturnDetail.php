<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SalesReturnDetail",
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
 *          property="itemFinanceCategoryID",
 *          description="itemFinanceCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemFinanceCategorySubID",
 *          description="itemFinanceCategorySubID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodebBSSystemID",
 *          description="financeGLcodebBSSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodebBS",
 *          description="financeGLcodebBS",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodePLSystemID",
 *          description="financeGLcodePLSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodePL",
 *          description="financeGLcodePL",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodeRevenueSystemID",
 *          description="financeGLcodeRevenueSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="financeGLcodeRevenue",
 *          description="financeGLcodeRevenue",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="qtyReturned",
 *          description="qtyReturned",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="qtyReturnedDefaultMeasure",
 *          description="qtyReturnedDefaultMeasure",
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
 *          property="deliveryOrderID",
 *          description="deliveryOrderID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="deliveryOrderDetailID",
 *          description="deliveryOrderDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="remarks",
 *          description="remarks",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="qtyIssued",
 *          description="qtyIssued",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="balanceQty",
 *          description="balanceQty",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="fullyReturned",
 *          description="0 -> default, 1->partially ordered, 2->fully ordered",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SalesReturnDetail extends Model
{

    public $table = 'salesreturndetails';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'salesReturnDetailID';


    public $fillable = [
        'salesReturnID',
        'companySystemID',
        'documentSystemID',
        'itemCodeSystem',
        'itemPrimaryCode',
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
        'qtyReturned',
        'trackingType',
        'reasonCode',
        'isPostItemLedger',
        'reasonGLCode',
        'qtyReturnedDefaultMeasure',
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
        'deliveryOrderID',
        'deliveryOrderDetailID',
        'customerItemDetailID',
        'doInvRemainingQty',
        'custInvoiceDirectAutoID',
        'remarks',
        'qtyIssued',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'VATApplicableOn',
        'balanceQty',
        'fullyReturned',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'salesReturnDetailID' => 'integer',
        'salesReturnID' => 'integer',
        'companySystemID' => 'integer',
        'vatMasterCategoryID' => 'integer',
        'vatSubCategoryID' => 'integer',
        'custInvoiceDirectAutoID' => 'integer',
        'customerItemDetailID' => 'integer',
        'documentSystemID' => 'integer',
        'itemCodeSystem' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'itemUnitOfMeasure' => 'integer',
        'unitOfMeasureIssued' => 'integer',
        'convertionMeasureVal' => 'float',
        'itemFinanceCategoryID' => 'integer',
        'itemFinanceCategorySubID' => 'string',
        'financeGLcodebBSSystemID' => 'integer',
        'financeGLcodebBS' => 'string',
        'financeGLcodePLSystemID' => 'integer',
        'financeGLcodePL' => 'string',
        'financeCogsGLcodePLSystemID' => 'integer',
        'financeCogsGLcodePL' => 'string',
        'financeGLcodeRevenueSystemID' => 'integer',
        'trackingType' => 'integer',
        'reasonCode' => 'integer',
        'financeGLcodeRevenue' => 'string',
        'qtyReturned' => 'float',
        'qtyReturnedDefaultMeasure' => 'float',
        'currentStockQty' => 'float',
        'currentWareHouseStockQty' => 'float',
        'currentStockQtyInDamageReturn' => 'float',
        'wacValueLocal' => 'float',
        'wacValueReporting' => 'float',
        'unitTransactionAmount' => 'float',
        'discountPercentage' => 'float',
        'discountAmount' => 'float',
        'transactionCurrencyID' => 'integer',
        'transactionCurrencyER' => 'float',
        'transactionAmount' => 'float',
        'companyLocalCurrencyID' => 'integer',
        'companyLocalCurrencyER' => 'float',
        'companyLocalAmount' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingCurrencyER' => 'float',
        'companyReportingAmount' => 'float',
        'doInvRemainingQty' => 'float',
        'deliveryOrderID' => 'integer',
        'deliveryOrderDetailID' => 'integer',
        'remarks' => 'string',
        'qtyIssued' => 'float',
        'balanceQty' => 'float',
        'fullyReturned' => 'integer',
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
        'salesReturnID' => 'required'
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
        return $this->belongsTo('App\Models\SalesReturn','salesReturnID','id');
    }

    public function delivery_order(){
        return $this->belongsTo('App\Models\DeliveryOrder','deliveryOrderID','deliveryOrderID');
    }

    public function sales_invoice(){
        return $this->belongsTo('App\Models\CustomerInvoiceDirect','custInvoiceDirectAutoID','custInvoiceDirectAutoID');
    }

    public function delivery_order_detail(){
        return $this->belongsTo('App\Models\DeliveryOrderDetail','deliveryOrderDetailID','deliveryOrderDetailID');
    }

    public function sales_invoice_detail(){
        return $this->belongsTo('App\Models\CustomerInvoiceItemDetails','customerItemDetailID','customerItemDetailID');
    }
}

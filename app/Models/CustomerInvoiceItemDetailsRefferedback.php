<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerInvoiceItemDetailsRefferedback",
 *      required={""},
 *      @SWG\Property(
 *          property="customerItemDetailRefferedBackID",
 *          description="customerItemDetailRefferedBackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerItemDetailID",
 *          description="customerItemDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custInvoiceDirectAutoID",
 *          description="custInvoiceDirectAutoID",
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
 *          property="comments",
 *          description="comments",
 *          type="string"
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
 *          type="integer",
 *          format="int32"
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
 *          property="includePLForGRVYN",
 *          description="includePLForGRVYN",
 *          type="integer",
 *          format="int32"
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
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="issueCostLocal",
 *          description="issueCostLocal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="issueCostLocalTotal",
 *          description="issueCostLocalTotal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="reportingCurrencyID",
 *          description="reportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="reportingCurrencyER",
 *          description="reportingCurrencyER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="issueCostRpt",
 *          description="issueCostRpt",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="issueCostRptTotal",
 *          description="issueCostRptTotal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="marginPercentage",
 *          description="marginPercentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="sellingCurrencyID",
 *          description="sellingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="sellingCurrencyER",
 *          description="sellingCurrencyER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="sellingCost",
 *          description="sellingCost",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="sellingCostAfterMargin",
 *          description="sellingCostAfterMargin",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="sellingTotal",
 *          description="sellingTotal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="sellingCostAfterMarginLocal",
 *          description="sellingCostAfterMarginLocal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="sellingCostAfterMarginRpt",
 *          description="sellingCostAfterMarginRpt",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="customerCatalogDetailID",
 *          description="customerCatalogDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerCatalogMasterID",
 *          description="customerCatalogMasterID",
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
 *          property="deliveryOrderID",
 *          description="deliveryOrderID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="quotationMasterID",
 *          description="quotationMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="quotationDetailsID",
 *          description="quotationDetailsID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
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
class CustomerInvoiceItemDetailsRefferedback extends Model
{

    public $table = 'erp_customerinvoiceitemdetailsrefferedback';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'customerItemDetailRefferedBackID';


    public $fillable = [
        'customerItemDetailID',
        'custInvoiceDirectAutoID',
        'itemCodeSystem',
        'itemPrimaryCode',
        'itemDescription',
        'itemUnitOfMeasure',
        'unitOfMeasureIssued',
        'convertionMeasureVal',
        'qtyIssued',
        'qtyIssuedDefaultMeasure',
        'currentStockQty',
        'currentWareHouseStockQty',
        'currentStockQtyInDamageReturn',
        'comments',
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
        'includePLForGRVYN',
        'localCurrencyID',
        'localCurrencyER',
        'issueCostLocal',
        'issueCostLocalTotal',
        'reportingCurrencyID',
        'reportingCurrencyER',
        'issueCostRpt',
        'issueCostRptTotal',
        'marginPercentage',
        'sellingCurrencyID',
        'sellingCurrencyER',
        'sellingCost',
        'sellingCostAfterMargin',
        'sellingTotal',
        'sellingCostAfterMarginLocal',
        'sellingCostAfterMarginRpt',
        'customerCatalogDetailID',
        'customerCatalogMasterID',
        'deliveryOrderDetailID',
        'deliveryOrderID',
        'quotationMasterID',
        'quotationDetailsID',
        'timesReferred',
        'timestamp',
        'part_no'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'customerItemDetailRefferedBackID' => 'integer',
        'customerItemDetailID' => 'integer',
        'custInvoiceDirectAutoID' => 'integer',
        'itemCodeSystem' => 'integer',
        'itemPrimaryCode' => 'string',
        'itemDescription' => 'string',
        'itemUnitOfMeasure' => 'integer',
        'unitOfMeasureIssued' => 'integer',
        'convertionMeasureVal' => 'float',
        'qtyIssued' => 'float',
        'qtyIssuedDefaultMeasure' => 'float',
        'currentStockQty' => 'float',
        'currentWareHouseStockQty' => 'float',
        'currentStockQtyInDamageReturn' => 'float',
        'comments' => 'string',
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
        'includePLForGRVYN' => 'integer',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'issueCostLocal' => 'float',
        'issueCostLocalTotal' => 'float',
        'reportingCurrencyID' => 'integer',
        'reportingCurrencyER' => 'float',
        'issueCostRpt' => 'float',
        'issueCostRptTotal' => 'float',
        'marginPercentage' => 'float',
        'sellingCurrencyID' => 'integer',
        'sellingCurrencyER' => 'float',
        'sellingCost' => 'float',
        'sellingCostAfterMargin' => 'float',
        'sellingTotal' => 'float',
        'sellingCostAfterMarginLocal' => 'float',
        'sellingCostAfterMarginRpt' => 'float',
        'customerCatalogDetailID' => 'integer',
        'customerCatalogMasterID' => 'integer',
        'deliveryOrderDetailID' => 'integer',
        'deliveryOrderID' => 'integer',
        'quotationMasterID' => 'integer',
        'quotationDetailsID' => 'integer',
        'timesReferred' => 'integer',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function uom_default(){
        return $this->belongsTo('App\Models\Unit','itemUnitOfMeasure','UnitID');
    }

    public function uom_issuing(){
        return $this->belongsTo('App\Models\Unit','unitOfMeasureIssued','UnitID');
    }

    public function delivery_order(){
        return $this->belongsTo('App\Models\DeliveryOrder','deliveryOrderID','deliveryOrderID');
    }

    public function delivery_order_detail(){
        return $this->belongsTo('App\Models\DeliveryOrderDetail','deliveryOrderDetailID','deliveryOrderDetailID');
    }

    public function master(){
        return $this->belongsTo('App\Models\CustomerInvoiceDirect','custInvoiceDirectAutoID','custInvoiceDirectAutoID');
    }

    public function sales_quotation(){
        return $this->belongsTo('App\Models\QuotationMaster','quotationMasterID','quotationMasterID');
    }

    public function sales_quotation_detail(){
        return $this->belongsTo('App\Models\QuotationDetails','quotationDetailsID','quotationDetailsID');
    }
}

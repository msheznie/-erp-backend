<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerInvoiceItemDetails",
 *      required={""},
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
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="qtyIssued",
 *          description="qtyIssued",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="qtyIssuedDefaultMeasure",
 *          description="qtyIssuedDefaultMeasure",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="currentStockQty",
 *          description="currentStockQty",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="currentWareHouseStockQty",
 *          description="currentWareHouseStockQty",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="currentStockQtyInDamageReturn",
 *          description="currentStockQtyInDamageReturn",
 *          type="float",
 *          format="float"
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
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="issueCostLocal",
 *          description="issueCostLocal",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="issueCostLocalTotal",
 *          description="issueCostLocalTotal",
 *          type="float",
 *          format="float"
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
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="issueCostRpt",
 *          description="issueCostRpt",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="issueCostRptTotal",
 *          description="issueCostRptTotal",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="marginPercentage",
 *          description="marginPercentage",
 *          type="float",
 *          format="float"
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
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="sellingCost",
 *          description="sellingCost",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="sellingCostAfterMargin",
 *          description="sellingCostAfterMargin",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="sellingTotal",
 *          description="sellingTotal",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="sellingCostAfterMarginLocal",
 *          description="sellingCostAfterMarginLocal",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="sellingCostAfterMarginRpt",
 *          description="sellingCostAfterMarginRpt",
 *          type="float",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="timestamp",
 *          description="timestamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class CustomerInvoiceItemDetails extends Model
{

    public $table = 'erp_customerinvoiceitemdetails';
    protected $primaryKey = 'customerItemDetailID';
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $appends = ['issueCostTrans', 'issueCostTransTotal'];

    public $fillable = [
        'custInvoiceDirectAutoID',
        'itemCodeSystem',
        'itemPrimaryCode',
        'projectID',
        'itemDescription',
        'itemUnitOfMeasure',
        'unitOfMeasureIssued',
        'convertionMeasureVal',
        'qtyIssued',
        'trackingType',
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
        'salesPrice',
        'sellingCost',
        'discountPercentage',
        'discountAmount',
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
        'fullyReturned',
        'timesReferred',
        'returnQty',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'VATApplicableOn',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'timestamp',
        'part_no',
        'userQty'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'customerItemDetailID' => 'integer',
        'custInvoiceDirectAutoID' => 'integer',
        'vatMasterCategoryID' => 'integer',
        'vatSubCategoryID' => 'integer',
        'projectID' => 'integer',
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
        'trackingType' => 'integer',
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
        'salesPrice' => 'float',
        'discountPercentage' => 'float',
        'discountAmount' => 'float',
        'sellingCost' => 'float',
        'sellingCostAfterMargin' => 'float',
        'sellingTotal' => 'float',
        'sellingCostAfterMarginLocal' => 'float',
        'returnQty' => 'float',
        'sellingCostAfterMarginRpt' => 'float',
        'customerCatalogDetailID'=> 'integer',
        'customerCatalogMasterID'=> 'integer',
        'deliveryOrderDetailID'=> 'integer',
        'deliveryOrderID'=> 'integer',
        'quotationMasterID'=> 'integer',
        'quotationDetailsID'=> 'integer',
        'fullyReturned'=> 'integer',
        'timesReferred' => 'integer',
        'timestamp' => 'datetime',
        'VATPercentage' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'VATApplicableOn' => 'integer',
        'userQty' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'customerItemDetailID' => 'required'
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

    public function currency(){
        return $this->belongsTo('App\Models\CurrencyMaster','sellingCurrencyID','currencyID');
    }

    public function local_currency(){
        return $this->belongsTo('App\Models\CurrencyMaster','localCurrencyID','currencyID');
    }

    public function reporting_currency(){
        return $this->belongsTo('App\Models\CurrencyMaster','reportingCurrencyID','currencyID');
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

    public function sales_return_details(){
        return $this->belongsTo('App\Models\SalesReturnDetail','customerItemDetailID','customerItemDetailID');
    }

    public function getIssueCostTransAttribute()
    {
        $currencyConversion = \Helper::currencyConversion(null, $this->localCurrencyID, $this->sellingCurrencyID, $this->issueCostLocal);

        return isset($currencyConversion['documentAmount']) ? $currencyConversion['documentAmount'] : 0;
    }

    public function getIssueCostTransTotalAttribute()
    {
        $currencyConversion = \Helper::currencyConversion(null, $this->localCurrencyID, $this->sellingCurrencyID, $this->issueCostLocalTotal);

        return isset($currencyConversion['documentAmount']) ? $currencyConversion['documentAmount'] : 0;
    }

    public function project(){
        return $this->belongsTo('App\Models\ErpProjectMaster','projectID','id');
    }
}

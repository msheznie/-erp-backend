<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StageCustomerInvoiceItemDetails extends Model
{
    public $table = 'erp_stage_customerinvoiceitemdetails';
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
        'part_no'
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
        'VATApplicableOn' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'customerItemDetailID' => 'required'
    ];

}

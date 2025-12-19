<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="DeliveryOrderDetailRefferedback",
 *      required={""},
 *      @SWG\Property(
 *          property="deliveryOrderDetailRefferedbackID",
 *          description="deliveryOrderDetailRefferedbackID",
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
 *          property="remarks",
 *          description="remarks",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="requestedQty",
 *          description="requestedQty",
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
 *          property="fullyReceived",
 *          description="0 -> default, 1->partially ordered, 2->fully ordered",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invQty",
 *          description="invQty",
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
class DeliveryOrderDetailRefferedback extends Model
{

    public $table = 'erp_delivery_order_detail_refferedback';

    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'deliveryOrderDetailRefferedbackID';




    public $fillable = [
        'deliveryOrderDetailID',
        'deliveryOrderID',
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
        'qtyIssued',
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
        'invQty',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'deliveryOrderDetailRefferedbackID' => 'integer',
        'deliveryOrderDetailID' => 'integer',
        'deliveryOrderID' => 'integer',
        'companySystemID' => 'integer',
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
        'requestedQty' => 'float',
        'balanceQty' => 'float',
        'fullyReceived' => 'integer',
        'invQty' => 'float',
        'timestamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
//        'deliveryOrderDetailID' => 'required',
//        'deliveryOrderID' => 'required'
    ];


    
}

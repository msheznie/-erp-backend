<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AdvanceReceiptDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="advanceReceiptDetailAutoID",
 *          description="advanceReceiptDetailAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custReceivePaymentAutoID",
 *          description="custReceivePaymentAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="soAdvPaymentID",
 *          description="soAdvPaymentID",
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
 *          property="salesOrderID",
 *          description="salesOrderID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="salesOrderCode",
 *          description="salesOrderCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="paymentAmount",
 *          description="paymentAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="customerTransCurrencyID",
 *          description="customerTransCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerTransER",
 *          description="customerTransER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="customerDefaultCurrencyID",
 *          description="customerDefaultCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerDefaultCurrencyER",
 *          description="customerDefaultCurrencyER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyID",
 *          description="localCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localER",
 *          description="localER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="comRptCurrencyID",
 *          description="comRptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comRptER",
 *          description="comRptER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultAmount",
 *          description="supplierDefaultAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransAmount",
 *          description="supplierTransAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="localAmount",
 *          description="localAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="comRptAmount",
 *          description="comRptAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="VATAmount",
 *          description="VATAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="VATAmountLocal",
 *          description="VATAmountLocal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="VATAmountRpt",
 *          description="VATAmountRpt",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timeStamp",
 *          description="timeStamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class AdvanceReceiptDetails extends Model
{

    public $table = 'erp_advancereceiptdetails';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'advanceReceiptDetailAutoID';



    public $fillable = [
        'custReceivePaymentAutoID',
        'soAdvPaymentID',
        'companySystemID',
        'companyID',
        'salesOrderID',
        'salesOrderCode',
        'comments',
        'paymentAmount',
        'customerTransCurrencyID',
        'customerTransER',
        'customerDefaultCurrencyID',
        'customerDefaultCurrencyER',
        'localCurrencyID',
        'localER',
        'comRptCurrencyID',
        'comRptER',
        'supplierDefaultAmount',
        'supplierTransAmount',
        'localAmount',
        'comRptAmount',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'serviceLineSystemID',
        'serviceLineCode',
        'timesReferred',
        'timeStamp'

    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'advanceReceiptDetailAutoID' => 'integer',
        'custReceivePaymentAutoID' => 'integer',
        'soAdvPaymentID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'salesOrderID' => 'integer',
        'salesOrderCode' => 'string',
        'comments' => 'string',
        'paymentAmount' => 'float',
        'customerTransCurrencyID' => 'integer',
        'customerTransER' => 'float',
        'customerDefaultCurrencyID' => 'integer',
        'customerDefaultCurrencyER' => 'float',
        'localCurrencyID' => 'integer',
        'localER' => 'float',
        'comRptCurrencyID' => 'integer',
        'comRptER' => 'float',
        'supplierDefaultAmount' => 'float',
        'supplierTransAmount' => 'float',
        'localAmount' => 'float',
        'comRptAmount' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'timesReferred' => 'integer',
        'timeStamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function advance_payment_master()
    {
        return $this->hasOne(SalesOrderAdvPayment::class, 'soAdvPaymentID', 'soAdvPaymentID');
    }

    public function sales_order()
    {
        return $this->belongsTo(QuotationMaster::class, 'salesOrderID', 'quotationMasterID');
    }

    public function master()
    {
        return $this->belongsTo(CustomerReceivePayment::class, 'custReceivePaymentAutoID', 'custReceivePaymentAutoID');
    }

    public function customer_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'customerTransCurrencyID', 'currencyID');
    }
    
}

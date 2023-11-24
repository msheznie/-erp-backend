<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerInvoiceDirectDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="custInvDirDetAutoID",
 *          description="custInvDirDetAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custInvoiceDirectID",
 *          description="custInvoiceDirectID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="glCode",
 *          description="glCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glCodeDes",
 *          description="glCodeDes",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="accountType",
 *          description="accountType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="invoiceAmountCurrency",
 *          description="invoiceAmountCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoiceAmountCurrencyER",
 *          description="invoiceAmountCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="unitOfMeasure",
 *          description="unitOfMeasure",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoiceQty",
 *          description="invoiceQty",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="unitCost",
 *          description="unitCost",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="invoiceAmount",
 *          description="invoiceAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localCurrency",
 *          description="localCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localCurrencyER",
 *          description="localCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localAmount",
 *          description="localAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comRptCurrency",
 *          description="comRptCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="comRptCurrencyER",
 *          description="comRptCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="comRptAmount",
 *          description="comRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="discountLocalAmount",
 *          description="discountLocalAmount",
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
 *          property="discountRptAmount",
 *          description="discountRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="discountRate",
 *          description="discountRate",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="performaMasterID",
 *          description="performaMasterID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="clientContractID",
 *          description="clientContractID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class CustomerInvoiceDirectDetail extends Model
{

    public $table = 'erp_custinvoicedirectdet';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'custInvDirDetAutoID';


    public $fillable = [
        'custInvoiceDirectID',
        'companyID',
        'companySystemID',
        'serviceLineCode',
        'customerID',
        'glCode',
        'glSystemID',
        'projectID',
        'glCodeDes',
        'accountType',
        'comments',
        'invoiceAmountCurrency',
        'invoiceAmountCurrencyER',
        'unitOfMeasure',
        'invoiceQty',
        'salesPrice',
        'discountAmount',
        'discountPercentage',
        'unitCost',
        'invoiceAmount',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'discountLocalAmount',
        'discountAmountLine',
        'discountRptAmount',
        'discountRate',
        'performaMasterID',
        'clientContractID',
        'contractID',
        'timesReferred',
        'timeStamp',
        'serviceLineSystemID',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'glSystemID',
        'isDiscount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'custInvDirDetAutoID' => 'integer',
        'custInvoiceDirectID' => 'integer',
        'vatSubCategoryID' => 'integer',
        'vatMasterCategoryID' => 'integer',
        'companySystemID' => 'integer',
        'projectID' => 'integer',
        'companyID' => 'string',
        'serviceLineCode' => 'string',
        'customerID' => 'integer',
        'glCode' => 'string',
        'glCodeDes' => 'string',
        'accountType' => 'string',
        'comments' => 'string',
        'invoiceAmountCurrency' => 'integer',
        'invoiceAmountCurrencyER' => 'float',
        'unitOfMeasure' => 'integer',
        'invoiceQty' => 'float',
        'unitCost' => 'float',
        'invoiceAmount' => 'float',
        'salesPrice' => 'float',
        'discountAmountLine' => 'float',
        'discountPercentage' => 'float',
        'localCurrency' => 'integer',
        'localCurrencyER' => 'float',
        'localAmount' => 'float',
        'comRptCurrency' => 'integer',
        'comRptCurrencyER' => 'float',
        'comRptAmount' => 'float',
        'discountLocalAmount' => 'float',
        'discountAmount' => 'float',
        'discountRptAmount' => 'float',
        'discountRate' => 'integer',
        'performaMasterID' => 'integer',
        'clientContractID' => 'string',
        'timesReferred' => 'integer',
        'contractID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'VATPercentage' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'glSystemID' => 'integer',
        'isDiscount' => 'integer'

    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unitOfMeasure', 'UnitID');
    }

    public function chart_Of_account() {
        return $this->belongsTo('App\Models\ChartOfAccount', 'glSystemID','chartOfAccountSystemID' );
    }

    public function performadetails()
    {
        return $this->belongsTo('App\Models\PerformaDetails', 'custInvoiceDirectID', 'invoiceSsytemCode');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineCode', 'ServiceLineCode');
    }

    public function contract()
    {
        return $this->belongsTo('App\Models\Contract', 'contractID', 'contractUID');
    }

    public function billmaster()
    {
        return $this->belongsTo('App\Models\FreeBillingMasterPerforma', 'performaMasterID', 'PerformaInvoiceNo');
    }

    public function billingdetails()
    {
        return $this->hasMany('App\Models\FreeBilling', 'performaInvoiceNo', 'performaMasterID');
    }

    public function master()
    {
        return $this->belongsTo('App\Models\CustomerInvoiceDirect', 'custInvoiceDirectID', 'custInvoiceDirectAutoID');
    }

     public function project(){
        return $this->belongsTo('App\Models\ErpProjectMaster','projectID','id');
    }
}

<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerInvoiceDirectDetRefferedback",
 *      required={""},
 *      @SWG\Property(
 *          property="custInvDirDetAutoIDRefferedBack",
 *          description="custInvDirDetAutoIDRefferedBack",
 *          type="integer",
 *          format="int32"
 *      ),
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
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
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
 *          property="glSystemID",
 *          description="glSystemID",
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
 *          property="comRptAmount",
 *          description="comRptAmount",
 *          type="number",
 *          format="float"
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
 *          property="contractID",
 *          description="contractID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class CustomerInvoiceDirectDetRefferedback extends Model
{

    public $table = 'erp_custinvoicedirectdetrefferedback';

    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'custInvDirDetAutoIDRefferedBack';

    public $fillable = [
        'custInvDirDetAutoID',
        'custInvoiceDirectID',
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'customerID',
        'glSystemID',
        'glCode',
        'glCodeDes',
        'accountType',
        'comments',
        'invoiceAmountCurrency',
        'invoiceAmountCurrencyER',
        'unitOfMeasure',
        'invoiceQty',
        'unitCost',
        'invoiceAmount',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'discountLocalAmount',
        'discountAmount',
        'discountRptAmount',
        'discountRate',
        'comRptAmount',
        'performaMasterID',
        'clientContractID',
        'contractID',
        'timesReferred',
        'timeStamp',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'mfqInvoiceDetailID',
        'glSystemID',
        'isDiscount'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'custInvDirDetAutoIDRefferedBack' => 'integer',
        'custInvDirDetAutoID' => 'integer',
        'custInvoiceDirectID' => 'integer',
        'companyID' => 'string',
        'companySystemID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'customerID' => 'integer',
        'glSystemID' => 'integer',
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
        'localCurrency' => 'integer',
        'localCurrencyER' => 'float',
        'localAmount' => 'float',
        'comRptCurrency' => 'integer',
        'comRptCurrencyER' => 'float',
        'discountLocalAmount' => 'float',
        'discountAmount' => 'float',
        'discountRptAmount' => 'float',
        'discountRate' => 'integer',
        'comRptAmount' => 'float',
        'performaMasterID' => 'integer',
        'clientContractID' => 'string',
        'contractID' => 'integer',
        'timesReferred' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function department()
    {
        return $this->belongsTo('App\Models\SegmentMaster', 'serviceLineCode', 'ServiceLineCode');
    }

    public function contract()
    {
        return $this->belongsTo('App\Models\Contract', 'clientContractID', 'ContractNumber');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unitOfMeasure', 'UnitID');
    }

    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StageCustomerInvoiceDirectDetail extends Model
{
    public $table = 'erp_stage_custinvoicedirectdet';

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
        'glSystemID'
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
        'glSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

}

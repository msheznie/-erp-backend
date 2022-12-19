<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StageDirectReceiptDetail extends Model
{
    public $table = 'erp_stage_directreceiptdetails';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';
    protected $primaryKey = 'directReceiptDetailsID';


    public $fillable = [
        'directReceiptAutoID',
        'companyID',
        'companySystemID',
        'serviceLineSystemID',
        'serviceLineCode',
        'chartOfAccountSystemID',
        'glCode',
        'glCodeDes',
        'contractUID',
        'contractID',
        'comments',
        'DRAmountCurrency',
        'DDRAmountCurrencyER',
        'DRAmount',
        'localCurrency',
        'localCurrencyER',
        'localAmount',
        'comRptCurrency',
        'comRptCurrencyER',
        'comRptAmount',
        'timeStamp',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'netAmount',
        'netAmountLocal',
        'netAmountRpt',
        'detail_project_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'directReceiptDetailsID' => 'integer',
        'directReceiptAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'chartOfAccountSystemID' => 'integer',
        'glCode' => 'string',
        'glCodeDes' => 'string',
        'contractUID' => 'integer',
        'contractID' => 'string',
        'comments' => 'string',
        'DRAmountCurrency' => 'integer',
        'DDRAmountCurrencyER' => 'float',
        'DRAmount' => 'float',
        'localCurrency' => 'integer',
        'localCurrencyER' => 'float',
        'localAmount' => 'float',
        'comRptCurrency' => 'integer',
        'comRptCurrencyER' => 'float',
        'comRptAmount' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'netAmount' => 'float',
        'netAmountLocal' => 'float',
        'netAmountRpt' => 'float',
        'detail_project_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

}

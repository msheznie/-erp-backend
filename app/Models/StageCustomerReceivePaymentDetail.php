<?php

namespace App\Models;

use Eloquent as Model;

class StageCustomerReceivePaymentDetail extends Model
{
    public $table = 'erp_stage_custreceivepaymentdet';

    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'custRecivePayDetAutoID';


    public $fillable = [
        'custReceivePaymentAutoID',
        'arAutoID',
        'companySystemID',
        'companyID',
        'matchingDocID',
        'addedDocumentSystemID',
        'addedDocumentID',
        'bookingInvCodeSystem',
        'bookingInvCode',
        'bookingDate',
        'comments',
        'custTransactionCurrencyID',
        'custTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'bookingAmountTrans',
        'bookingAmountLocal',
        'bookingAmountRpt',
        'custReceiveCurrencyID',
        'custReceiveCurrencyER',
        'custbalanceAmount',
        'receiveAmountTrans',
        'receiveAmountLocal',
        'receiveAmountRpt',
        'timesReferred',
        'timestamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'custRecivePayDetAutoID' => 'integer',
        'custReceivePaymentAutoID' => 'integer',
        'arAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'matchingDocID' => 'integer',
        'addedDocumentSystemID' => 'integer',
        'addedDocumentID' => 'string',
        'bookingInvCodeSystem' => 'integer',
        'bookingInvCode' => 'string',
        'comments' => 'string',
        'custTransactionCurrencyID' => 'integer',
        'custTransactionCurrencyER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'bookingAmountTrans' => 'float',
        'bookingAmountLocal' => 'float',
        'bookingAmountRpt' => 'float',
        'custReceiveCurrencyID' => 'integer',
        'custReceiveCurrencyER' => 'float',
        'custbalanceAmount' => 'float',
        'receiveAmountTrans' => 'float',
        'receiveAmountLocal' => 'float',
        'receiveAmountRpt' => 'float',
        'timesReferred' => 'integer'
    ];

}

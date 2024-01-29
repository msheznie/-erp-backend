<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustReceivePaymentDetRefferedHistory",
 *      required={""},
 *      @SWG\Property(
 *          property="custRecivePayDetRefferedBackID",
 *          description="custRecivePayDetRefferedBackID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custRecivePayDetAutoID",
 *          description="custRecivePayDetAutoID",
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
 *          property="arAutoID",
 *          description="arAutoID",
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
 *          property="matchingDocID",
 *          description="matchingDocID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="addedDocumentSystemID",
 *          description="addedDocumentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="addedDocumentID",
 *          description="addedDocumentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bookingInvCodeSystem",
 *          description="bookingInvCodeSystem",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bookingInvCode",
 *          description="bookingInvCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="comments",
 *          description="comments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="custTransactionCurrencyID",
 *          description="custTransactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custTransactionCurrencyER",
 *          description="custTransactionCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingCurrencyID",
 *          description="companyReportingCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyReportingER",
 *          description="companyReportingER",
 *          type="number",
 *          format="float"
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
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="bookingAmountTrans",
 *          description="bookingAmountTrans",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="bookingAmountLocal",
 *          description="bookingAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="bookingAmountRpt",
 *          description="bookingAmountRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="custReceiveCurrencyID",
 *          description="custReceiveCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custReceiveCurrencyER",
 *          description="custReceiveCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="custbalanceAmount",
 *          description="custbalanceAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="receiveAmountTrans",
 *          description="receiveAmountTrans",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="receiveAmountLocal",
 *          description="receiveAmountLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="receiveAmountRpt",
 *          description="receiveAmountRpt",
 *          type="number",
 *          format="float"
 *      )
 * )
 */
class CustReceivePaymentDetRefferedHistory extends Model
{

    public $table = 'erp_custreceivepaymentdetrefferedhistory';
    
    const CREATED_AT = 'timestamp';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'custRecivePayDetRefferedBackID';

    public $fillable = [
        'custRecivePayDetAutoID',
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
        'timestamp',
        'VATAmount',
        'VATAmountRpt',
        'VATAmountLocal',
        'VATPercentage',
        'vatMasterCategoryID',
        'vatSubCategoryID',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'custRecivePayDetRefferedBackID' => 'integer',
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
        'receiveAmountRpt' => 'float'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}

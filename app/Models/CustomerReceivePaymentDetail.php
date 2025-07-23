<?php
/**
 * =============================================
 * -- File Name : CustomerReceivePaymentDetail.php
 * -- Project Name : ERP
 * -- Module Name :  Accounts receivable
 * -- Author : Mubashir
 * -- Create date : 24 - August 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(
 *      definition="CustomerReceivePaymentDetail",
 *      required={""},
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
 * @property integer $matchingDocID
 * @property integer $arAutoID
 * @property integer $companySystemID
 * @property string $companyID
 * @property string $bookingInvCode
 * @property float $receiveAmountTrans
 * @property integer $bookingInvCodeSystem
 * @property integer $addedDocumentSystemID
 */
class CustomerReceivePaymentDetail extends Model
{

    public $table = 'erp_custreceivepaymentdet';
    
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
        'timestamp',
        'VATAmount',
        'VATAmountRpt',
        'VATAmountLocal',
        'VATPercentage',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'isVatDisabled',
        'serviceLineCode',
        'serviceLineSystemID'
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
        'timesReferred' => 'integer',
        'serviceLineCode' => 'string',
        'serviceLineSystemID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function master()
    {
        return $this->belongsTo(CustomerReceivePayment::class, 'custReceivePaymentAutoID', 'custReceivePaymentAutoID');
    }

    public function credit_note()
    {
        return $this->belongsTo(CreditNote::class, 'bookingInvCodeSystem', 'creditNoteAutoID');
    }

    
    public function reciept_vocuher()
    {
        return $this->belongsTo(CustomerInvoiceDirect::class, 'bookingInvCodeSystem', 'custInvoiceDirectAutoID');
    }


    public function matching_master()
    {
        return $this->belongsTo(MatchDocumentMaster::class, 'matchingDocID', 'matchDocumentMasterAutoID');
    }

    public function ar_data()
    {
        return $this->belongsTo(AccountsReceivableLedger::class, 'arAutoID', 'arAutoID');
    }
}

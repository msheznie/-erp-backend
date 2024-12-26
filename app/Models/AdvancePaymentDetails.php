<?php
/**
 * =============================================
 * -- File Name : AdvancePaymentDetails.php
 * -- Project Name : ERP
 * -- Module Name :  AdvancePaymentDetails
 * -- Author : Nazir
 * -- Create date : 10 - August 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AdvancePaymentDetails",
 *      required={""},
 *      @SWG\Property(
 *          property="advancePaymentDetailAutoID",
 *          description="advancePaymentDetailAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="PayMasterAutoId",
 *          description="PayMasterAutoId",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="poAdvPaymentID",
 *          description="poAdvPaymentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="purchaseOrderID",
 *          description="purchaseOrderID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseOrderCode",
 *          description="purchaseOrderCode",
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
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransCurrencyID",
 *          description="supplierTransCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransER",
 *          description="supplierTransER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultCurrencyID",
 *          description="supplierDefaultCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultCurrencyER",
 *          description="supplierDefaultCurrencyER",
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
 *          property="localER",
 *          description="localER",
 *          type="number",
 *          format="float"
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
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefaultAmount",
 *          description="supplierDefaultAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransAmount",
 *          description="supplierTransAmount",
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
 *          property="comRptAmount",
 *          description="comRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      )
 * )
 */
class AdvancePaymentDetails extends Model
{

    public $table = 'erp_advancepaymentdetails';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'advancePaymentDetailAutoID';

    public $fillable = [
        'PayMasterAutoId',
        'poAdvPaymentID',
        'matchingDocID',
        'companySystemID',
        'companyID',
        'purchaseOrderID',
        'purchaseOrderCode',
        'comments',
        'paymentAmount',
        'amountBeforeVAT',
        'supplierTransCurrencyID',
        'supplierTransER',
        'supplierDefaultCurrencyID',
        'supplierDefaultCurrencyER',
        'localCurrencyID',
        'localER',
        'comRptCurrencyID',
        'comRptER',
        'supplierDefaultAmount',
        'supplierTransAmount',
        'localAmount',
        'comRptAmount',
        'VATAmount',
        'VATAmountRpt',
        'VATAmountLocal',
        'timesReferred',
        'VATPercentage',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'fullAmount',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'advancePaymentDetailAutoID' => 'integer',
        'PayMasterAutoId' => 'integer',
        'poAdvPaymentID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'purchaseOrderID' => 'integer',
        'purchaseOrderCode' => 'string',
        'comments' => 'string',
        'paymentAmount' => 'float',
        'amountBeforeVAT' => 'float',
        'supplierTransCurrencyID' => 'integer',
        'supplierTransER' => 'float',
        'supplierDefaultCurrencyID' => 'integer',
        'supplierDefaultCurrencyER' => 'float',
        'localCurrencyID' => 'integer',
        'localER' => 'float',
        'comRptCurrencyID' => 'integer',
        'vatMasterCategoryID' => 'integer',
        'vatSubCategoryID' => 'integer',
        'comRptER' => 'float',
        'supplierDefaultAmount' => 'float',
        'supplierTransAmount' => 'float',
        'VATAmount' => 'float',
        'VATAmountRpt' => 'float',
        'VATAmountLocal' => 'float',
        'localAmount' => 'float',
        'comRptAmount' => 'float',
        'VATPercentage' => 'float',
        'fullAmount' => 'float',
        'timesReferred' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function purchaseorder_by()
    {
        return $this->belongsTo('App\Models\ProcumentOrder', 'purchaseOrderID', 'purchaseOrderID');
    }

    public function advancepaymentmaster()
    {
        return $this->hasOne('App\Models\PoAdvancePayment', 'poAdvPaymentID', 'poAdvPaymentID');
    }

    public function supplier_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'supplierTransCurrencyID', 'currencyID');
    }

    public function pay_invoice()
    {
        return $this->belongsTo('App\Models\PaySupplierInvoiceMaster', 'PayMasterAutoId', 'PayMasterAutoId');
    }

    public function document_matching()
    {
        return $this->belongsTo('App\Models\MatchDocumentMaster', 'matchingDocID', 'matchDocumentMasterAutoID');
    }

}

<?php
/**
 * =============================================
 * -- File Name : PaySupplierInvoiceDetail.php
 * -- Project Name : ERP
 * -- Module Name :  PaySupplierInvoiceDetail
 * -- Author : Nazir
 * -- Create date : 09 - August 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PaySupplierInvoiceDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="payDetailAutoID",
 *          description="payDetailAutoID",
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
 *          property="apAutoID",
 *          description="apAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="matchingDocID",
 *          description="matchingDocID",
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
 *          property="bookingInvSystemCode",
 *          description="bookingInvSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bookingInvDocCode",
 *          description="bookingInvDocCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="addedDocumentType",
 *          description="addedDocumentType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierCodeSystem",
 *          description="supplierCodeSystem",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierInvoiceNo",
 *          description="supplierInvoiceNo",
 *          type="string"
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
 *          property="supplierInvoiceAmount",
 *          description="supplierInvoiceAmount",
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
 *          property="supplierDefaultAmount",
 *          description="supplierDefaultAmount",
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
 *          property="localAmount",
 *          description="localAmount",
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
 *          property="comRptAmount",
 *          description="comRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="supplierPaymentCurrencyID",
 *          description="supplierPaymentCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierPaymentER",
 *          description="supplierPaymentER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="supplierPaymentAmount",
 *          description="supplierPaymentAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="paymentBalancedAmount",
 *          description="paymentBalancedAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="paymentSupplierDefaultAmount",
 *          description="paymentSupplierDefaultAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="paymentLocalAmount",
 *          description="paymentLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="paymentComRptAmount",
 *          description="paymentComRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdPcID",
 *          description="createdPcID",
 *          type="string"
 *      )
 * )
 */
class PaySupplierInvoiceDetail extends Model
{

    public $table = 'erp_paysupplierinvoicedetail';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'payDetailAutoID';

    public $fillable = [
        'PayMasterAutoId',
        'documentID',
        'documentSystemID',
        'apAutoID',
        'matchingDocID',
        'companySystemID',
        'companyID',
        'addedDocumentSystemID',
        'addedDocumentID',
        'bookingInvSystemCode',
        'bookingInvDocCode',
        'bookingInvoiceDate',
        'addedDocumentType',
        'supplierCodeSystem',
        'employeeSystemID',
        'supplierInvoiceNo',
        'supplierInvoiceDate',
        'supplierTransCurrencyID',
        'supplierTransER',
        'supplierInvoiceAmount',
        'supplierDefaultCurrencyID',
        'supplierDefaultCurrencyER',
        'supplierDefaultAmount',
        'localCurrencyID',
        'localER',
        'localAmount',
        'comRptCurrencyID',
        'comRptER',
        'comRptAmount',
        'supplierPaymentCurrencyID',
        'supplierPaymentER',
        'supplierPaymentAmount',
        'paymentBalancedAmount',
        'paymentSupplierDefaultAmount',
        'paymentLocalAmount',
        'paymentComRptAmount',
        'retentionVatAmount',
        'timesReferred',
        'isRetention',
        'modifiedUserID',
        'modifiedPCID',
        'createdDateTime',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'VATAmount',
        'VATAmountRpt',
        'VATAmountLocal',
        'VATPercentage',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'timeStamp',
        'purchaseOrderID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'payDetailAutoID' => 'integer',
        'PayMasterAutoId' => 'integer',
        'documentID' => 'string',
        'documentSystemID' => 'integer',
        'apAutoID' => 'integer',
        'matchingDocID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'addedDocumentSystemID' => 'integer',
        'addedDocumentID' => 'string',
        'bookingInvSystemCode' => 'integer',
        'bookingInvDocCode' => 'string',
        'addedDocumentType' => 'integer',
        'supplierCodeSystem' => 'integer',
        'supplierInvoiceNo' => 'string',
        'supplierTransCurrencyID' => 'integer',
        'supplierTransER' => 'float',
        'supplierInvoiceAmount' => 'float',
        'supplierDefaultCurrencyID' => 'integer',
        'supplierDefaultCurrencyER' => 'float',
        'supplierDefaultAmount' => 'float',
        'localCurrencyID' => 'integer',
        'localER' => 'float',
        'localAmount' => 'float',
        'comRptCurrencyID' => 'integer',
        'comRptER' => 'float',
        'comRptAmount' => 'float',
        'supplierPaymentCurrencyID' => 'integer',
        'supplierPaymentER' => 'float',
        'supplierPaymentAmount' => 'float',
        'paymentBalancedAmount' => 'float',
        'paymentSupplierDefaultAmount' => 'float',
        'paymentLocalAmount' => 'float',
        'paymentComRptAmount' => 'float',
        'VATAmount' => 'float',
        'VATAmountRpt' => 'float',
        'VATAmountLocal' => 'float',
        'VATPercentage' => 'float',
        'vatMasterCategoryID' => 'integer',
        'vatSubCategoryID' => 'integer',
        'timesReferred' => 'integer',
        'isRetention' => 'boolean',
        'modifiedUserID' => 'string',
        'modifiedPCID' => 'string',
        'createdUserSystemID' => 'integer',
        'purchaseOrderID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function payment_master()
    {
        return $this->hasOne('App\Models\PaySupplierInvoiceMaster', 'PayMasterAutoId', 'PayMasterAutoId');
    }

    public function supplier_invoice()
    {
        return $this->hasOne('App\Models\BookInvSuppMaster', 'bookingSuppMasInvAutoID', 'bookingInvSystemCode');
    }

    public function matching_master()
    {
        return $this->hasOne('App\Models\MatchDocumentMaster', 'matchDocumentMasterAutoID', 'matchingDocID');
    }

    public function debite_note()
    {
        return $this->hasOne('App\Models\DebitNote', 'debitNoteAutoID', 'PayMasterAutoId');
    }

    public function pomaster()
    {
        return $this->belongsTo('App\Models\ProcumentOrder', 'purchaseOrderID', 'purchaseOrderID');
    }
}

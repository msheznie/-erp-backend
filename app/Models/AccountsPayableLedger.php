<?php

namespace App\Models;

use App\helper\Helper;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="AccountsPayableLedger",
 *      required={""},
 *      @SWG\Property(
 *          property="apAutoID",
 *          description="apAutoID",
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
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemCode",
 *          description="documentSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentCode",
 *          description="documentCode",
 *          type="string"
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
 *          property="isInvoiceLockedYN",
 *          description="isInvoiceLockedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="lockedBy",
 *          description="lockedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="lockedByEmpName",
 *          description="lockedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="lockedComments",
 *          description="lockedComments",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="invoiceType",
 *          description="invoiceType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="selectedToPaymentInv",
 *          description="selectedToPaymentInv",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="fullyInvoice",
 *          description="fullyInvoice",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="advancePaymentTypeID",
 *          description="advancePaymentTypeID",
 *          type="integer",
 *          format="int32"
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
class AccountsPayableLedger extends Model
{

    public $table = 'erp_accountspayableledger';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey  = 'apAutoID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'documentDate',
        'supplierCodeSystem',
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
        'isInvoiceLockedYN',
        'lockedBy',
        'lockedByEmpName',
        'lockedDate',
        'lockedComments',
        'invoiceType',
        'selectedToPaymentInv',
        'fullyInvoice',
        'advancePaymentTypeID',
        'createdDateTime',
        'createdUserID',
        'createdPcID',
        'timeStamp',
        'isRetention',
        'purchaseOrderID',
        'isWHT',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'apAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'isRetention' => 'integer',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'documentCode' => 'string',
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
        'isInvoiceLockedYN' => 'integer',
        'lockedBy' => 'string',
        'lockedByEmpName' => 'string',
        'lockedComments' => 'string',
        'invoiceType' => 'integer',
        'selectedToPaymentInv' => 'integer',
        'fullyInvoice' => 'integer',
        'advancePaymentTypeID' => 'integer',
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

    public function setDocumentDateAttribute($value)
    {
        $this->attributes['documentDate'] = Helper::dateAddTime($value);
    }

    public function local_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID', 'currencyID');
    }

    public function transaction_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'supplierTransCurrencyID', 'currencyID');
    }

    public function reporting_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'comRptCurrencyID', 'currencyID');
    }

     public function supplier()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'supplierCodeSystem', 'supplierCodeSystem');
    }
}

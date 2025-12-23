<?php
/**
 * =============================================
 * -- File Name : BookInvSuppDet.php
 * -- Project Name : ERP
 * -- Module Name :  BookInvSuppDet
 * -- Author : Nazir
 * -- Create date : 08 - August 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="BookInvSuppDet",
 *      required={""},
 *      @SWG\Property(
 *          property="bookingSupInvoiceDetAutoID",
 *          description="bookingSupInvoiceDetAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bookingSuppMasInvAutoID",
 *          description="bookingSuppMasInvAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="unbilledgrvAutoID",
 *          description="unbilledgrvAutoID",
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
 *          property="supplierID",
 *          description="supplierID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="purchaseOrderID",
 *          description="purchaseOrderID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvAutoID",
 *          description="grvAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="grvType",
 *          description="grvType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionCurrencyID",
 *          description="supplierTransactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionCurrencyER",
 *          description="supplierTransactionCurrencyER",
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
 *          property="supplierInvoOrderedAmount",
 *          description="supplierInvoOrderedAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="supplierInvoAmount",
 *          description="supplierInvoAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="transSupplierInvoAmount",
 *          description="transSupplierInvoAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="localSupplierInvoAmount",
 *          description="localSupplierInvoAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="rptSupplierInvoAmount",
 *          description="rptSupplierInvoAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="totTransactionAmount",
 *          description="totTransactionAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="totLocalAmount",
 *          description="totLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="totRptAmount",
 *          description="totRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="isAddon",
 *          description="isAddon",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoiceBeforeGRVYN",
 *          description="invoiceBeforeGRVYN",
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
class BookInvSuppDet extends Model
{

    public $table = 'erp_bookinvsuppdet';

    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';

    protected $primaryKey = 'bookingSupInvoiceDetAutoID';

    public $fillable = [
        'bookingSuppMasInvAutoID',
        'unbilledgrvAutoID',
        'companySystemID',
        'companyID',
        'supplierID',
        'purchaseOrderID',
        'grvAutoID',
        'grvType',
        'supplierTransactionCurrencyID',
        'supplierTransactionCurrencyER',
        'companyReportingCurrencyID',
        'companyReportingER',
        'localCurrencyID',
        'localCurrencyER',
        'supplierInvoOrderedAmount',
        'supplierInvoAmount',
        'transSupplierInvoAmount',
        'localSupplierInvoAmount',
        'rptSupplierInvoAmount',
        'totTransactionAmount',
        'totLocalAmount',
        'totRptAmount',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'isAddon',
        'invoiceBeforeGRVYN',
        'timesReferred',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'bookingSupInvoiceDetAutoID' => 'integer',
        'bookingSuppMasInvAutoID' => 'integer',
        'unbilledgrvAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'supplierID' => 'integer',
        'purchaseOrderID' => 'integer',
        'grvAutoID' => 'integer',
        'grvType' => 'string',
        'supplierTransactionCurrencyID' => 'integer',
        'supplierTransactionCurrencyER' => 'float',
        'companyReportingCurrencyID' => 'integer',
        'companyReportingER' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'supplierInvoOrderedAmount' => 'float',
        'supplierInvoAmount' => 'float',
        'transSupplierInvoAmount' => 'float',
        'localSupplierInvoAmount' => 'float',
        'rptSupplierInvoAmount' => 'float',
        'totTransactionAmount' => 'float',
        'totLocalAmount' => 'float',
        'totRptAmount' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'isAddon' => 'integer',
        'invoiceBeforeGRVYN' => 'integer',
        'timesReferred' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function grvmaster()
    {
        return $this->belongsTo('App\Models\GRVMaster', 'grvAutoID', 'grvAutoID');
    }

    public function unbilled_grv()
    {
        return $this->belongsTo('App\Models\UnbilledGrvGroupBy', 'unbilledgrvAutoID', 'unbilledgrvAutoID');
    }

    public function pomaster()
    {
        return $this->belongsTo('App\Models\ProcumentOrder', 'purchaseOrderID', 'purchaseOrderID');
    }

    public function suppinvmaster()
    {
        return $this->hasOne('App\Models\BookInvSuppMaster', 'bookingSuppMasInvAutoID', 'bookingSuppMasInvAutoID');
    }

    public function supplier_invoice_item_details()
    {
        return $this->hasMany('App\Models\SupplierInvoiceItemDetail', 'bookingSupInvoiceDetAutoID', 'bookingSupInvoiceDetAutoID');
    }

    public function getSupplierInvoiceItemDetailsVATAmountSum()
    {
        return $this->supplier_invoice_item_details()->sum('VATAmount');
    }





}

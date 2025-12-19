<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="SupplierInvoiceItemDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="bookingSupInvoiceDetAutoID",
 *          description="bookingSupInvoiceDetAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="grvDetailsID",
 *          description="grvDetailsID",
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
 *          property="supplierTransactionCurrencyID",
 *          description="supplierTransactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransactionCurrencyER",
 *          description="supplierTransactionCurrencyER",
 *          type="number",
 *          format="number"
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
 *          format="number"
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
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="supplierInvoOrderedAmount",
 *          description="supplierInvoOrderedAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="supplierInvoAmount",
 *          description="supplierInvoAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="transSupplierInvoAmount",
 *          description="transSupplierInvoAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="localSupplierInvoAmount",
 *          description="localSupplierInvoAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="rptSupplierInvoAmount",
 *          description="rptSupplierInvoAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totTransactionAmount",
 *          description="totTransactionAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totLocalAmount",
 *          description="totLocalAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="totRptAmount",
 *          description="totRptAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="VATAmount",
 *          description="VATAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="VATAmountLocal",
 *          description="VATAmountLocal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="VATAmountRpt",
 *          description="VATAmountRpt",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="timeStamp",
 *          description="timeStamp",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class SupplierInvoiceItemDetail extends Model
{

    public $table = 'erp_bookinvsupp_item_det';
    
    const CREATED_AT = 'timeStamp';
    const UPDATED_AT = 'timeStamp';




    public $fillable = [
        'bookingSupInvoiceDetAutoID',
        'bookingSuppMasInvAutoID',
        'unbilledgrvAutoID',
        'companySystemID',
        'grvDetailsID',
        'logisticID',
        'purchaseOrderID',
        'grvAutoID',
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
        'exempt_vat_portion',
        'VATAmountRpt',
        'vatMasterCategoryID',
        'vatSubCategoryID',
        'grvRecivedQty',
        'grvReturnQty',
        'invoicedAmount',
        'balanceAmount',
        'timeStamp'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'bookingSupInvoiceDetAutoID' => 'integer',
        'id' => 'integer',
        'bookingSuppMasInvAutoID' => 'integer',
        'vatMasterCategoryID' => 'integer',
        'vatSubCategoryID' => 'integer',
        'unbilledgrvAutoID' => 'integer',
        'companySystemID' => 'integer',
        'grvDetailsID' => 'integer',
        'logisticID' => 'integer',
        'purchaseOrderID' => 'integer',
        'grvAutoID' => 'integer',
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
        'exempt_vat_portion' => 'float',
        'rptSupplierInvoAmount' => 'float',
        'totTransactionAmount' => 'float',
        'totLocalAmount' => 'float',
        'totRptAmount' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'grvRecivedQty' => 'float',
        'grvReturnQty' => 'float',
        'invoicedAmount' => 'float',
        'balanceAmount' => 'float',
        'timeStamp' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'bookingSupInvoiceDetAutoID' => 'required',
        'bookingSuppMasInvAutoID' => 'required'
    ];

    public function vat_sub_category(){
        return $this->belongsTo('App\Models\TaxVatCategories','vatSubCategoryID','taxVatSubCategoriesAutoID');
    }

    public function invoice_master()
    {
        return $this->belongsTo('App\Models\BookInvSuppMaster', 'bookingSuppMasInvAutoID', 'bookingSuppMasInvAutoID');
    }

    public function invoice_detail()
    {
        return $this->belongsTo('App\Models\BookInvSuppDet', 'bookingSupInvoiceDetAutoID', 'bookingSupInvoiceDetAutoID');
    }

    public function grv_detail()
    {
        return $this->belongsTo('App\Models\GRVDetails', 'grvDetailsID', 'grvDetailsID');
    }

    public function logistic_detail()
    {
        return $this->belongsTo('App\Models\PoAdvancePayment', 'logisticID', 'poAdvPaymentID');
    }

    public function grv()
    {
        return $this->belongsTo('App\Models\GRVMaster', 'grvAutoID', 'grvAutoID');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\ProcumentOrder', 'purchaseOrderID', 'purchaseOrderID');
    }

}

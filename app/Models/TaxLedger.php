<?php

namespace App\Models;

use Eloquent as Model;
use Awobaz\Compoships\Compoships;

/**
 * @SWG\Definition(
 *      definition="TaxLedger",
 *      required={""},
 *      @SWG\Property(
 *          property="taxLedgerID",
 *          description="taxLedgerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentMasterAutoID",
 *          description="documentMasterAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentCode",
 *          description="documentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentDate",
 *          description="documentDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="subCategoryID",
 *          description="subCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="masterCategoryID",
 *          description="masterCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="rcmApplicableYN",
 *          description="rcmApplicableYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="localAmount",
 *          description="localAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="rptAmount",
 *          description="rptAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="transAmount",
 *          description="transAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="transER",
 *          description="transER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="localER",
 *          description="localER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="comRptER",
 *          description="comRptER",
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
 *          property="rptCurrencyID",
 *          description="rptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="transCurrencyID",
 *          description="transCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isClaimable",
 *          description="isClaimable",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isClaimed",
 *          description="isClaimed",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxAuthorityAutoID",
 *          description="taxAuthorityAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="inputVATGlAccountID",
 *          description="inputVATGlAccountID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="inputVatTransferAccountID",
 *          description="inputVatTransferAccountID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="outputVatTransferGLAccountID",
 *          description="outputVatTransferGLAccountID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="outputVatGLAccountID",
 *          description="outputVatGLAccountID",
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
 *          property="createdPCID",
 *          description="createdPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPCID",
 *          description="modifiedPCID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserID",
 *          description="modifiedUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedDateTime",
 *          description="modifiedDateTime",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class TaxLedger extends Model
{
    use Compoships;

    public $table = 'erp_tax_ledger';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'modifiedDateTime';

    protected $primaryKey = 'taxLedgerID';


    public $fillable = [
        'documentSystemID',
        'documentMasterAutoID',
        'documentCode',
        'documentDate',
        'subCategoryID',
        'masterCategoryID',
        'rcmApplicableYN',
        'localAmount',
        'rptAmount',
        'transAmount',
        'documentTransAmount',
        'documentLocalAmount',
        'documentReportingAmount',
        'transER',
        'localER',
        'comRptER',
        'localCurrencyID',
        'matchDocumentMasterAutoID',
        'rptCurrencyID',
        'transCurrencyID',
        'isClaimable',
        'isClaimed',
        'taxAuthorityAutoID',
        'inputVATGlAccountID',
        'inputVatTransferAccountID',
        'outputVatTransferGLAccountID',
        'outputVatGLAccountID',
        'companySystemID',
        'createdPCID',
        'createdUserID',
        'createdDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'partyID',
        'documentFinalApprovedByEmpSystemID',
        'modifiedDateTime'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'taxLedgerID' => 'integer',
        'documentSystemID' => 'integer',
        'documentMasterAutoID' => 'integer',
        'documentFinalApprovedByEmpSystemID' => 'integer',
        'matchDocumentMasterAutoID' => 'integer',
        'partyID' => 'integer',
        'documentCode' => 'string',
        'documentDate' => 'datetime',
        'subCategoryID' => 'integer',
        'masterCategoryID' => 'integer',
        'rcmApplicableYN' => 'integer',
        'localAmount' => 'float',
        'documentTransAmount' => 'float',
        'documentLocalAmount' => 'float',
        'documentReportingAmount' => 'float',
        'rptAmount' => 'float',
        'transAmount' => 'float',
        'transER' => 'float',
        'localER' => 'float',
        'comRptER' => 'float',
        'localCurrencyID' => 'integer',
        'rptCurrencyID' => 'integer',
        'transCurrencyID' => 'integer',
        'isClaimable' => 'integer',
        'isClaimed' => 'integer',
        'taxAuthorityAutoID' => 'integer',
        'inputVATGlAccountID' => 'integer',
        'inputVatTransferAccountID' => 'integer',
        'outputVatTransferGLAccountID' => 'integer',
        'outputVatGLAccountID' => 'integer',
        'companySystemID' => 'integer',
        'createdPCID' => 'string',
        'createdUserID' => 'string',
        'createdDateTime' => 'datetime',
        'modifiedPCID' => 'string',
        'modifiedUserID' => 'string',
        'modifiedDateTime' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function supplier(){
        return $this->belongsTo('App\Models\SupplierMaster', 'partyID','supplierCodeSystem');
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'partyID', 'employeeSystemID');
    }

    public function document_master(){
        return $this->belongsTo('App\Models\DocumentMaster', 'documentSystemID','documentSystemID');
    }

    public function main_category(){
        return $this->belongsTo('App\Models\TaxVatMainCategories', 'masterCategoryID','taxVatMainCategoriesAutoID');
    }

    public function sub_category(){
        return $this->belongsTo('App\Models\TaxVatCategories', 'subCategoryID','taxVatSubCategoriesAutoID');
    }

    public function customer(){
        return $this->belongsTo('App\Models\CustomerMaster', 'partyID','customerCodeSystem');
    }

    public function localcurrency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID','currencyID');
    }

    public function transcurrency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'transCurrencyID','currencyID');
    }

    public function rptcurrency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'rptCurrencyID','currencyID');
    }

    public function final_approved_by(){
        return $this->belongsTo('App\Models\Employee', 'documentFinalApprovedByEmpSystemID', 'employeeSystemID');
    }

    public function grv(){
        return $this->belongsTo('App\Models\GRVMaster', ['documentMasterAutoID','documentSystemID','companySystemID'], ['grvAutoID','documentSystemID','companySystemID']);
    }

    public function material_issue(){
        return $this->belongsTo('App\Models\ItemIssueMaster',['documentMasterAutoID','documentSystemID','companySystemID'], ['itemIssueAutoID','documentSystemID','companySystemID']);
    }

    public function stock_return(){
        return $this->belongsTo('App\Models\ItemReturnMaster',['documentMasterAutoID','documentSystemID','companySystemID'], ['itemReturnAutoID','documentSystemID','companySystemID']);
    }

    public function stock_transfer(){
        return $this->belongsTo('App\Models\StockTransfer',['documentMasterAutoID','documentSystemID','companySystemID'], ['stockTransferAutoID','documentSystemID','companySystemID']);
    }

    public function receive_stock(){
        return $this->belongsTo('App\Models\StockReceive',['documentMasterAutoID','documentSystemID','companySystemID'], ['stockReceiveAutoID','documentSystemID','companySystemID']);
    }

    public function stock_adjustment(){
        return $this->belongsTo('App\Models\StockAdjustment',['documentMasterAutoID','documentSystemID','companySystemID'], ['stockAdjustmentAutoID','documentSystemID','companySystemID']);
    }

    public function inventory_reclassification(){
        return $this->belongsTo('App\Models\InventoryReclassification',['documentMasterAutoID','documentSystemID','companySystemID'], ['inventoryreclassificationID','documentSystemID','companySystemID']);
    }

    public function purchase_return(){
        return $this->belongsTo('App\Models\PurchaseReturn',['documentMasterAutoID','documentSystemID','companySystemID'], ['purhaseReturnAutoID','documentSystemID','companySystemID']);
    }

    public function customer_invoice(){
        return $this->belongsTo('App\Models\CustomerInvoiceDirect',['documentMasterAutoID','documentSystemID','companySystemID'], ['custInvoiceDirectAutoID','documentSystemiD','companySystemID']);
    }

    public function supplier_invoice(){
        return $this->belongsTo('App\Models\BookInvSuppMaster',['documentMasterAutoID','documentSystemID','companySystemID'], ['bookingSuppMasInvAutoID','documentSystemID','companySystemID']);
    }

    public function debit_note(){
        return $this->belongsTo('App\Models\DebitNote',['documentMasterAutoID','documentSystemID','companySystemID'], ['debitNoteAutoID','documentSystemID','companySystemID']);
    }

    public function credit_note(){
        return $this->belongsTo('App\Models\CreditNote',['documentMasterAutoID','documentSystemID','companySystemID'], ['creditNoteAutoID','documentSystemiD','companySystemID']);
    }

    public function payment_voucher(){
        return $this->belongsTo('App\Models\PaySupplierInvoiceMaster',['documentMasterAutoID','documentSystemID','companySystemID'], ['PayMasterAutoId','documentSystemID','companySystemID']);
    }

    public function bank_receipt(){
        return $this->belongsTo('App\Models\CustomerReceivePayment',['documentMasterAutoID','documentSystemID','companySystemID'], ['custReceivePaymentAutoID','documentSystemID','companySystemID']);
    }

    public function journal_entries(){
        return $this->belongsTo('App\Models\JvMaster',['documentMasterAutoID','documentSystemID','companySystemID'], ['jvMasterAutoId','documentSystemID','companySystemID']);
    }

    public function fixed_asset(){
        return $this->belongsTo('App\Models\FixedAssetMaster',['documentMasterAutoID','documentSystemID','companySystemID'], ['faID','documentSystemID','companySystemID']);
    }

    public function fixed_asset_dep(){
        return $this->belongsTo('App\Models\FixedAssetDepreciationMaster',['documentMasterAutoID','documentSystemID','companySystemID'], ['depMasterAutoID','documentSystemID','companySystemID']);
    }

    public function fixed_asset_disposal(){
        return $this->belongsTo('App\Models\AssetDisposalMaster',['documentMasterAutoID','documentSystemID','companySystemID'], ['assetdisposalMasterAutoID','documentSystemID','companySystemID']);
    }

    public function delivery_order(){
        return $this->belongsTo('App\Models\DeliveryOrder',['documentMasterAutoID','documentSystemID','companySystemID'], ['deliveryOrderID','documentSystemID','companySystemID']);
    }

    public function sales_return(){
        return $this->belongsTo('App\Models\SalesReturn',['documentMasterAutoID','documentSystemID','companySystemID'], ['id','documentSystemID','companySystemID']);
    }
    
}

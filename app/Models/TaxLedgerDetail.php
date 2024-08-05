<?php

namespace App\Models;

use Eloquent as Model;
use Awobaz\Compoships\Compoships;

/**
 * @SWG\Definition(
 *      definition="TaxLedgerDetail",
 *      required={""},
 *      @SWG\Property(
 *          property="id",
 *          description="id",
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
 *          property="documentDetailID",
 *          description="documentDetailID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="taxLedgerID",
 *          description="taxLedgerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatSubCategoryID",
 *          description="vatSubCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="vatMasterCategoryID",
 *          description="vatMasterCategoryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentDate",
 *          description="documentDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="postedDate",
 *          description="postedDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="documentNumber",
 *          description="documentNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="accountCode",
 *          description="accountCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="accountDescription",
 *          description="accountDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="transactionCurrencyID",
 *          description="transactionCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="originalInvoice",
 *          description="originalInvoice",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="originalInvoiceDate",
 *          description="originalInvoiceDate",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="dateOfSupply",
 *          description="dateOfSupply",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @SWG\Property(
 *          property="partyType",
 *          description="partyType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="partyAutoID",
 *          description="partyAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="partyVATRegisteredYN",
 *          description="partyVATRegisteredYN",
 *          type="boolean"
 *      ),
 *      @SWG\Property(
 *          property="partyVATRegNo",
 *          description="partyVATRegNo",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="countryID",
 *          description="countryID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemSystemCode",
 *          description="itemSystemCode",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="itemCode",
 *          description="itemCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="itemDescription",
 *          description="itemDescription",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="VATPercentage",
 *          description="VATPercentage",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="taxableAmount",
 *          description="taxableAmount",
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
 *          property="localER",
 *          description="localER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="localAmount",
 *          description="localAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="reportingER",
 *          description="reportingER",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="reportingAmount",
 *          description="reportingAmount",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="taxableAmountLocal",
 *          description="taxableAmountLocal",
 *          type="number",
 *          format="number"
 *      ),
 *      @SWG\Property(
 *          property="taxableAmountReporting",
 *          description="taxableAmountReporting",
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
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdDateTime",
 *          description="createdDateTime",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class TaxLedgerDetail extends Model
{
    use Compoships;

        public $table = 'tax_ledger_details';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'createdDateTime';




    public $fillable = [
        'documentSystemID',
        'documentMasterAutoID',
        'documentDetailID',
        'taxLedgerID',
        'vatSubCategoryID',
        'vatMasterCategoryID',
        'serviceLineSystemID',
        'documentDate',
        'postedDate',
        'documentNumber',
        'chartOfAccountSystemID',
        'accountCode',
        'accountDescription',
        'transactionCurrencyID',
        'originalInvoice',
        'originalInvoiceDate',
        'dateOfSupply',
        'partyType',
        'partyAutoID',
        'partyVATRegisteredYN',
        'partyVATRegNo',
        'countryID',
        'itemSystemCode',
        'itemCode',
        'itemDescription',
        'rptCurrencyID',
        'localCurrencyID',
        'VATPercentage',
        'taxableAmount',
        'VATAmount',
        'localER',
        'reportingER',
        'taxableAmountLocal',
        'taxableAmountReporting',
        'VATAmountLocal',
        'VATAmountRpt',
        'inputVATGlAccountID',
        'inputVatTransferAccountID',
        'outputVatTransferGLAccountID',
        'outputVatGLAccountID',
        'companySystemID',
        'createdPCID',
        'createdUserSystemID',
        'rcmApplicableYN',
        'recovertabilityPercentage',
        'returnFilledDetailID',
        'recoverabilityAmount',
        'logisticYN',
        'addVATonPO',
        'matchDocumentMasterAutoID',
        'exempt_vat_portion',
        'createdDateTime'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'addVATonPO' => 'integer',
        'logisticYN' => 'integer',
        'documentSystemID' => 'integer',
        'documentMasterAutoID' => 'integer',
        'localCurrencyID' => 'integer',
        'rptCurrencyID' => 'integer',
        'documentDetailID' => 'integer',
        'taxLedgerID' => 'integer',
        'vatSubCategoryID' => 'integer',
        'vatMasterCategoryID' => 'integer',
        'serviceLineSystemID' => 'integer',
        'matchDocumentMasterAutoID' => 'integer',
        'rcmApplicableYN' => 'integer',
        'documentDate' => 'datetime',
        'postedDate' => 'datetime',
        'documentNumber' => 'string',
        'chartOfAccountSystemID' => 'integer',
        'accountCode' => 'string',
        'accountDescription' => 'string',
        'transactionCurrencyID' => 'integer',
        'originalInvoice' => 'string',
        'originalInvoiceDate' => 'datetime',
        'dateOfSupply' => 'datetime',
        'partyType' => 'integer',
        'partyAutoID' => 'integer',
        'partyVATRegisteredYN' => 'boolean',
        'partyVATRegNo' => 'string',
        'countryID' => 'integer',
        'itemSystemCode' => 'integer',
        'itemCode' => 'string',
        'itemDescription' => 'string',
        'VATPercentage' => 'float',
        'recovertabilityPercentage' => 'float',
        'recoverabilityAmount' => 'float',
        'taxableAmount' => 'float',
        'VATAmount' => 'float',
        'localER' => 'float',
        'reportingER' => 'float',
        'taxableAmountLocal' => 'float',
        'exempt_vat_portion' => 'float',
        'taxableAmountReporting' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'inputVATGlAccountID' => 'integer',
        'inputVatTransferAccountID' => 'integer',
        'outputVatTransferGLAccountID' => 'integer',
        'outputVatGLAccountID' => 'integer',
        'companySystemID' => 'integer',
        'createdPCID' => 'string',
        'createdUserSystemID' => 'integer',
        'returnFilledDetailID' => 'integer',
        'createdDateTime' => 'datetime'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function supplier(){
        return $this->belongsTo('App\Models\SupplierMaster', 'partyAutoID','supplierCodeSystem');
    }

    public function document_master(){
        return $this->belongsTo('App\Models\DocumentMaster', 'documentSystemID','documentSystemID');
    }


    public function main_category(){
        return $this->belongsTo('App\Models\TaxVatMainCategories', 'vatMasterCategoryID','taxVatMainCategoriesAutoID');
    }

    public function sub_category(){
        return $this->belongsTo('App\Models\TaxVatCategories', 'vatSubCategoryID','taxVatSubCategoriesAutoID');
    }

    public function customer(){
        return $this->belongsTo('App\Models\CustomerMaster', 'partyAutoID','customerCodeSystem');
    }

    public function tax_ledger()
    {
        return $this->belongsTo('App\Models\TaxLedger', ['documentSystemID', 'documentMasterAutoID'],['documentSystemID', 'documentMasterAutoID']);
    }

    public function customer_invoice(){
        return $this->belongsTo('App\Models\CustomerInvoiceDirect',['documentMasterAutoID','documentSystemID','companySystemID'], ['custInvoiceDirectAutoID','documentSystemiD','companySystemID']);
    }

    public function grv(){
        return $this->belongsTo('App\Models\GRVMaster', ['documentMasterAutoID','documentSystemID','companySystemID'], ['grvAutoID','documentSystemID','companySystemID']);
    }

    public function purchase_return(){
        return $this->belongsTo('App\Models\PurchaseReturn',['documentMasterAutoID','documentSystemID','companySystemID'], ['purhaseReturnAutoID','documentSystemID','companySystemID']);
    }


    public function customer_invoice_details(){
        return $this->belongsTo('App\Models\CustomerInvoiceItemDetails','documentDetailID', 'customerItemDetailID');
    }

    public function sales_return_details(){
        return $this->belongsTo('App\Models\SalesReturnDetail','documentDetailID', 'salesReturnDetailID');
    } 

    public function credit_note_details(){
        return $this->belongsTo('App\Models\CreditNoteDetails','documentDetailID', 'creditNoteDetailsID');
    }

    public function localcurrency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID','currencyID');
    }

    public function transcurrency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'transactionCurrencyID','currencyID');
    }

    public function rptcurrency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'rptCurrencyID','currencyID');
    } 

    public function country(){
        return $this->belongsTo('App\Models\CountryMaster', 'countryID','countryID');
    }

     public function company(){
        return $this->belongsTo('App\Models\Company', 'companySystemID','companySystemID');
    }


    public function input_vat(){
        return $this->belongsTo('App\Models\ChartOfAccount', 'inputVATGlAccountID','chartOfAccountSystemID');
    }

    public function input_vat_transfer(){
        return $this->belongsTo('App\Models\ChartOfAccount', 'inputVatTransferAccountID','chartOfAccountSystemID');
    }

    public function output_vat(){
        return $this->belongsTo('App\Models\ChartOfAccount', 'outputVatGLAccountID','chartOfAccountSystemID');
    }

    public function output_vat_transfer(){
        return $this->belongsTo('App\Models\ChartOfAccount', 'outputVatTransferGLAccountID','chartOfAccountSystemID');
    }

    public function supplier_invoice(){
        return $this->belongsTo('App\Models\BookInvSuppMaster',['documentMasterAutoID','documentSystemID','companySystemID'], ['bookingSuppMasInvAutoID','documentSystemID','companySystemID']);
    }

    public function payment_voucher(){
        return $this->belongsTo('App\Models\PaySupplierInvoiceMaster',['documentMasterAutoID','documentSystemID','companySystemID'], ['PayMasterAutoId','documentSystemID','companySystemID']);
    }
    public function supplier_invoice_details(){
        return $this->belongsTo('App\Models\SupplierInvoiceItemDetail','documentDetailID', 'id');
    } 

    public function grv_detail(){
        return $this->belongsTo('App\Models\SupplierInvoiceItemDetail','documentDetailID', 'id');
    } 

    public function purchase_return_detail(){
        return $this->belongsTo('App\Models\SupplierInvoiceItemDetail','documentDetailID', 'id');
    } 

    public function item_detail(){
        return $this->belongsTo('App\Models\ItemMaster','itemSystemCode', 'itemCodeSystem');
    }

    public function vat_return_filling_details(){
        return $this->belongsTo('App\Models\VatReturnFillingDetail','returnFilledDetailID', 'id');
    }
}

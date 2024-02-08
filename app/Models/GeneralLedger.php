<?php
/**
 * =============================================
 * -- File Name : GeneralLedger.php
 * -- Project Name : ERP
 * -- Module Name :  General Ledger
 * -- Author : Mohamed Nazir
 * -- Create date : 02 - July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */

namespace App\Models;

use App\helper\Helper;
use Eloquent as Model;
use Awobaz\Compoships\Compoships;
/**
 * @SWG\Definition(
 *      definition="GeneralLedger",
 *      required={""},
 *      @SWG\Property(
 *          property="GeneralLedgerID",
 *          description="GeneralLedgerID",
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
 *          property="serviceLineSystemID",
 *          description="serviceLineSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="serviceLineCode",
 *          description="serviceLineCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="masterCompanyID",
 *          description="masterCompanyID",
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
 *          property="documentYear",
 *          description="documentYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentMonth",
 *          description="documentMonth",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chequeNumber",
 *          description="chequeNumber",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoiceNumber",
 *          description="invoiceNumber",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chartOfAccountSystemID",
 *          description="chartOfAccountSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="glCode",
 *          description="glCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="glAccountType",
 *          description="glAccountType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="holdingShareholder",
 *          description="holdingShareholder",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="holdingPercentage",
 *          description="holdingPercentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="nonHoldingPercentage",
 *          description="nonHoldingPercentage",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="documentConfirmedBy",
 *          description="documentConfirmedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentFinalApprovedBy",
 *          description="documentFinalApprovedBy",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentNarration",
 *          description="documentNarration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="contractUID",
 *          description="contractUID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="clientContractID",
 *          description="clientContractID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierCodeSystem",
 *          description="supplierCodeSystem",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="venderName",
 *          description="venderName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentTransCurrencyID",
 *          description="documentTransCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentTransCurrencyER",
 *          description="documentTransCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="documentTransAmount",
 *          description="documentTransAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="documentLocalCurrencyID",
 *          description="documentLocalCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentLocalCurrencyER",
 *          description="documentLocalCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="documentLocalAmount",
 *          description="documentLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="documentRptCurrencyID",
 *          description="documentRptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentRptCurrencyER",
 *          description="documentRptCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="documentRptAmount",
 *          description="documentRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="empID",
 *          description="empID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="employeePaymentYN",
 *          description="employeePaymentYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isRelatedPartyYN",
 *          description="isRelatedPartyYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="hideForTax",
 *          description="hideForTax",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentType",
 *          description="documentType",
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
 *          property="isPdcChequeYN",
 *          description="isPdcChequeYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAddon",
 *          description="isAddon",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="isAllocationJV",
 *          description="isAllocationJV",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserID",
 *          description="createdUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserPC",
 *          description="createdUserPC",
 *          type="string"
 *      )
 * )
 */
class GeneralLedger extends Model
{

    use Compoships;

    public $table = 'erp_generalledger';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'GeneralLedgerID';

    public $fillable = [
        'companySystemID',
        'companyID',
        'serviceLineSystemID',
        'serviceLineCode',
        'masterCompanyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'documentDate',
        'documentYear',
        'documentMonth',
        'chequeNumber',
        'invoiceNumber',
        'invoiceDate',
        'chartOfAccountSystemID',
        'glCode',
        'glAccountType',
        'holdingShareholder',
        'holdingPercentage',
        'nonHoldingPercentage',
        'documentConfirmedDate',
        'documentConfirmedBy',
        'documentConfirmedByEmpSystemID',
        'documentFinalApprovedDate',
        'documentFinalApprovedBy',
        'documentFinalApprovedByEmpSystemID',
        'documentNarration',
        'contractUID',
        'clientContractID',
        'supplierCodeSystem',
        'employeeSystemID',
        'venderName',
        'documentTransCurrencyID',
        'documentTransCurrencyER',
        'documentTransAmount',
        'documentLocalCurrencyID',
        'documentLocalCurrencyER',
        'documentLocalAmount',
        'documentRptCurrencyID',
        'documentRptCurrencyER',
        'documentRptAmount',
        'empID',
        'employeePaymentYN',
        'isRelatedPartyYN',
        'hideForTax',
        'documentType',
        'advancePaymentTypeID',
        'isPdcChequeYN',
        'isAddon',
        'isAllocationJV',
        'contraYN',
        'contracDocCode',
        'createdDateTime',
        'createdUserID',
        'createdUserSystemID',
        'createdUserPC',
        'timestamp',
        'pdcID',
        'glAccountTypeID',
        'matchDocumentMasterAutoID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'GeneralLedgerID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'serviceLineSystemID' => 'integer',
        'serviceLineCode' => 'string',
        'masterCompanyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'documentCode' => 'string',
        'documentYear' => 'integer',
        'documentMonth' => 'integer',
        'chequeNumber' => 'integer',
        'invoiceNumber' => 'string',
        'chartOfAccountSystemID' => 'integer',
        'glCode' => 'string',
        'glAccountType' => 'string',
        'holdingShareholder' => 'string',
        'holdingPercentage' => 'float',
        'nonHoldingPercentage' => 'float',
        'documentConfirmedBy' => 'string',
        'documentFinalApprovedBy' => 'string',
        'documentFinalApprovedByEmpSystemID' => 'integer',
        'documentConfirmedByEmpSystemID' => 'integer',
        'documentNarration' => 'string',
        'contractUID' => 'integer',
        'clientContractID' => 'string',
        'supplierCodeSystem' => 'integer',
        'employeeSystemID' => 'integer',
        'venderName' => 'string',
        'documentTransCurrencyID' => 'integer',
        'documentTransCurrencyER' => 'float',
        'documentTransAmount' => 'float',
        'documentLocalCurrencyID' => 'integer',
        'documentLocalCurrencyER' => 'float',
        'documentLocalAmount' => 'float',
        'documentRptCurrencyID' => 'integer',
        'documentRptCurrencyER' => 'float',
        'documentRptAmount' => 'float',
        'empID' => 'string',
        'employeePaymentYN' => 'integer',
        'isRelatedPartyYN' => 'integer',
        'hideForTax' => 'integer',
        'documentType' => 'integer',
        'advancePaymentTypeID' => 'integer',
        'isPdcChequeYN' => 'integer',
        'isAddon' => 'integer',
        'isAllocationJV' => 'integer',
        'contraYN' => 'integer',
        'contracDocCode' => 'string',
        'createdUserID' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserPC' => 'string',
        'glAccountTypeID' => 'integer',
        'pdcID' => 'integer',
        'matchDocumentMasterAutoID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function supplier(){
        return $this->belongsTo('App\Models\SupplierMaster', 'supplierCodeSystem','supplierCodeSystem');
    }

    public function customer(){
        return $this->belongsTo('App\Models\CustomerMaster', 'supplierCodeSystem','customerCodeSystem');
    }

    public function charofaccount(){
        return $this->belongsTo('App\Models\ChartOfAccount', 'chartOfAccountSystemID','chartOfAccountSystemID');
    }

    public function localcurrency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'documentLocalCurrencyID','currencyID');
    }

    public function transcurrency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'documentTransCurrencyID','currencyID');
    }

    public function rptcurrency(){
        return $this->belongsTo('App\Models\CurrencyMaster', 'documentRptCurrencyID','currencyID');
    }

    public function setDocumentDateAttribute($value)
    {
        $this->attributes['documentDate'] = Helper::dateAddTime($value);
    }

    public function confirm_by(){
        return $this->belongsTo('App\Models\Employee', 'documentConfirmedByEmpSystemID', 'employeeSystemID');
    }

    public function final_approved_by(){
        return $this->belongsTo('App\Models\Employee', 'documentFinalApprovedByEmpSystemID', 'employeeSystemID');
    }

    public function grv(){
        return $this->belongsTo('App\Models\GRVMaster', ['documentSystemCode','documentSystemID','companySystemID'], ['grvAutoID','documentSystemID','companySystemID']);
    }

    public function material_issue(){
        return $this->belongsTo('App\Models\ItemIssueMaster',['documentSystemCode','documentSystemID','companySystemID'], ['itemIssueAutoID','documentSystemID','companySystemID']);
    }

    public function stock_return(){
        return $this->belongsTo('App\Models\ItemReturnMaster',['documentSystemCode','documentSystemID','companySystemID'], ['itemReturnAutoID','documentSystemID','companySystemID']);
    }

    public function stock_transfer(){
        return $this->belongsTo('App\Models\StockTransfer',['documentSystemCode','documentSystemID','companySystemID'], ['stockTransferAutoID','documentSystemID','companySystemID']);
    }

    public function receive_stock(){
        return $this->belongsTo('App\Models\StockReceive',['documentSystemCode','documentSystemID','companySystemID'], ['stockReceiveAutoID','documentSystemID','companySystemID']);
    }

    public function stock_adjustment(){
        return $this->belongsTo('App\Models\StockAdjustment',['documentSystemCode','documentSystemID','companySystemID'], ['stockAdjustmentAutoID','documentSystemID','companySystemID']);
    }

    public function inventory_reclassification(){
        return $this->belongsTo('App\Models\InventoryReclassification',['documentSystemCode','documentSystemID','companySystemID'], ['inventoryreclassificationID','documentSystemID','companySystemID']);
    }

    public function purchase_return(){
        return $this->belongsTo('App\Models\PurchaseReturn',['documentSystemCode','documentSystemID','companySystemID'], ['purhaseReturnAutoID','documentSystemID','companySystemID']);
    }

    public function customer_invoice(){
        return $this->belongsTo('App\Models\CustomerInvoiceDirect',['documentSystemCode','documentSystemID','companySystemID'], ['custInvoiceDirectAutoID','documentSystemiD','companySystemID']);
    }

    public function supplier_invoice(){
        return $this->belongsTo('App\Models\BookInvSuppMaster',['documentSystemCode','documentSystemID','companySystemID'], ['bookingSuppMasInvAutoID','documentSystemID','companySystemID']);
    }

    public function debit_note(){
        return $this->belongsTo('App\Models\DebitNote',['documentSystemCode','documentSystemID','companySystemID'], ['debitNoteAutoID','documentSystemID','companySystemID']);
    }

    public function credit_note(){
        return $this->belongsTo('App\Models\CreditNote',['documentSystemCode','documentSystemID','companySystemID'], ['creditNoteAutoID','documentSystemiD','companySystemID']);
    }

    public function payment_voucher(){
        return $this->belongsTo('App\Models\PaySupplierInvoiceMaster',['documentSystemCode','documentSystemID','companySystemID'], ['PayMasterAutoId','documentSystemID','companySystemID']);
    }

    public function bank_receipt(){
        return $this->belongsTo('App\Models\CustomerReceivePayment',['documentSystemCode','documentSystemID','companySystemID'], ['custReceivePaymentAutoID','documentSystemID','companySystemID']);
    }

    public function journal_entries(){
        return $this->belongsTo('App\Models\JvMaster',['documentSystemCode','documentSystemID','companySystemID'], ['jvMasterAutoId','documentSystemID','companySystemID']);
    }

    public function fixed_asset(){
        return $this->belongsTo('App\Models\FixedAssetMaster',['documentSystemCode','documentSystemID','companySystemID'], ['faID','documentSystemID','companySystemID']);
    }

    public function fixed_asset_dep(){
        return $this->belongsTo('App\Models\FixedAssetDepreciationMaster',['documentSystemCode','documentSystemID','companySystemID'], ['depMasterAutoID','documentSystemID','companySystemID']);
    }

    public function fixed_asset_disposal(){
        return $this->belongsTo('App\Models\AssetDisposalMaster',['documentSystemCode','documentSystemID','companySystemID'], ['assetdisposalMasterAutoID','documentSystemID','companySystemID']);
    }

    public function delivery_order(){
        return $this->belongsTo('App\Models\DeliveryOrder',['documentSystemCode','documentSystemID','companySystemID'], ['deliveryOrderID','documentSystemID','companySystemID']);
    }

    public function sales_return(){
        return $this->belongsTo('App\Models\SalesReturn',['documentSystemCode','documentSystemID','companySystemID'], ['id','documentSystemID','companySystemID']);
    }

    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_generalledger.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_generalledger.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }

    public function scopeCurrencyJoin($q,$as = 'currencymaster' ,$column = 'supplierTransactionCurrencyID',$columnAs = 'CurrencyName'){
        return $q->leftJoin('currencymaster as '.$as,$as.'.currencyID','=','erp_generalledger.'.$column)
        ->addSelect($as.".CurrencyName as ".$columnAs);

    }

    public function scopeSegmentJoin($q,$as = 'serviceline', $column = 'serviceLineSystemID' , $columnAs = 'ServiceLineDes')
    {
        return $q->leftJoin('serviceline as '.$as,$as.'.serviceLineSystemID','erp_generalledger.'.$column)
        ->addSelect($as.".ServiceLineDes as ".$columnAs);
    }

    public function scopeChartOfAccountJoin($q,$as = 'chartOfAccount', $column = 'chartOfAccountSystemID' , $columnAs = 'AccountCode')
    {
        return $q->leftJoin('chartofaccounts as '.$as,$as.'.chartOfAccountSystemID','erp_generalledger.'.$column);
    }

}

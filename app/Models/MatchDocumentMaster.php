<?php
/**
 * =============================================
 * -- File Name : MatchDocumentMaster.php
 * -- Project Name : ERP
 * -- Module Name :  MatchDocumentMaster
 * -- Author : Nazir
 * -- Create date : 13 - September 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="MatchDocumentMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="matchDocumentMasterAutoID",
 *          description="matchDocumentMasterAutoID",
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
 *          property="documentSystemID",
 *          description="documentSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyID",
 *          description="companyID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="companySystemID",
 *          description="companySystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="documentID",
 *          description="documentID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="matchingDocCode",
 *          description="matchingDocCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="BPVcode",
 *          description="BPVcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="BPVNarration",
 *          description="BPVNarration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="directPaymentPayee",
 *          description="directPaymentPayee",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="directPayeeCurrency",
 *          description="directPayeeCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="BPVsupplierID",
 *          description="BPVsupplierID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierGLCode",
 *          description="supplierGLCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransCurrencyID",
 *          description="supplierTransCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierTransCurrencyER",
 *          description="supplierTransCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefCurrencyID",
 *          description="supplierDefCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="supplierDefCurrencyER",
 *          description="supplierDefCurrencyER",
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
 *          property="companyRptCurrencyID",
 *          description="companyRptCurrencyID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyRptCurrencyER",
 *          description="companyRptCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="payAmountBank",
 *          description="payAmountBank",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="payAmountSuppTrans",
 *          description="payAmountSuppTrans",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="payAmountSuppDef",
 *          description="payAmountSuppDef",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="suppAmountDocTotal",
 *          description="suppAmountDocTotal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="payAmountCompLocal",
 *          description="payAmountCompLocal",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="payAmountCompRpt",
 *          description="payAmountCompRpt",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="confirmedYN",
 *          description="confirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpID",
 *          description="confirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpSystemID",
 *          description="confirmedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByName",
 *          description="confirmedByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approved",
 *          description="approved",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="invoiceType",
 *          description="invoiceType",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="matchInvoice",
 *          description="matchInvoice",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="matchingConfirmedYN",
 *          description="matchingConfirmedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="matchingConfirmedByEmpSystemID",
 *          description="matchingConfirmedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="matchingConfirmedByEmpID",
 *          description="matchingConfirmedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="matchingConfirmedByName",
 *          description="matchingConfirmedByName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="matchingAmount",
 *          description="matchingAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="matchBalanceAmount",
 *          description="matchBalanceAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="matchedAmount",
 *          description="matchedAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="matchLocalAmount",
 *          description="matchLocalAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="matchRptAmount",
 *          description="matchRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="matchingType",
 *          description="matchingType",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="isExchangematch",
 *          description="isExchangematch",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
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
 *      ),
 *      @SWG\Property(
 *          property="modifiedUser",
 *          description="modifiedUser",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="modifiedPc",
 *          description="modifiedPc",
 *          type="string"
 *      )
 * )
 */
class MatchDocumentMaster extends Model
{

    public $table = 'erp_matchdocumentmaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'matchDocumentMasterAutoID';

    public $fillable = [
        'PayMasterAutoId',
        'documentSystemID',
        'companyID',
        'companySystemID',
        'documentID',
        'serialNo',
        'matchingDocCode',
        'matchingDocdate',
        'BPVcode',
        'BPVdate',
        'BPVNarration',
        'directPaymentPayeeSelectEmp',
        'directPaymentPayee',
        'directPayeeCurrency',
        'BPVsupplierID',
        'supplierGLCodeSystemID',
        'supplierGLCode',
        'supplierTransCurrencyID',
        'supplierTransCurrencyER',
        'supplierDefCurrencyID',
        'supplierDefCurrencyER',
        'localCurrencyID',
        'localCurrencyER',
        'companyRptCurrencyID',
        'companyRptCurrencyER',
        'payAmountBank',
        'payAmountSuppTrans',
        'payAmountSuppDef',
        'suppAmountDocTotal',
        'payAmountCompLocal',
        'payAmountCompRpt',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByEmpSystemID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'invoiceType',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpSystemID',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'matchingAmount',
        'matchBalanceAmount',
        'matchedAmount',
        'matchLocalAmount',
        'matchRptAmount',
        'matchingType',
        'tableType',
        'serviceLineSystemID',
        'matchingOption',
        'isExchangematch',
        'createdUserSystemID',
        'createdUserGroup',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp',
        'user_type',
        'employee_id',
        'employeeGLCodeSystemID',
        'employeeGLCode',
        'companyFinanceYearID',
        'companyFinancePeriodID',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'matchDocumentMasterAutoID' => 'integer',
        'PayMasterAutoId' => 'integer',
        'documentSystemID' => 'integer',
        'companyID' => 'string',
        'companySystemID' => 'integer',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'matchingDocCode' => 'string',
        'BPVcode' => 'string',
        'BPVNarration' => 'string',
        'directPaymentPayeeSelectEmp' => 'integer',
        'directPaymentPayee' => 'string',
        'directPayeeCurrency' => 'integer',
        'BPVsupplierID' => 'integer',
        'supplierGLCodeSystemID' => 'integer',
        'supplierGLCode' => 'string',
        'supplierTransCurrencyID' => 'integer',
        'supplierTransCurrencyER' => 'float',
        'supplierDefCurrencyID' => 'integer',
        'supplierDefCurrencyER' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'companyRptCurrencyID' => 'integer',
        'companyRptCurrencyER' => 'float',
        'payAmountBank' => 'float',
        'payAmountSuppTrans' => 'float',
        'payAmountSuppDef' => 'float',
        'suppAmountDocTotal' => 'float',
        'payAmountCompLocal' => 'float',
        'payAmountCompRpt' => 'float',
        'confirmedYN' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByName' => 'string',
        'approved' => 'integer',
        'invoiceType' => 'integer',
        'matchInvoice' => 'integer',
        'matchingConfirmedYN' => 'integer',
        'matchingConfirmedByEmpSystemID' => 'integer',
        'matchingConfirmedByEmpID' => 'string',
        'matchingConfirmedByName' => 'string',
        'matchingAmount' => 'float',
        'matchBalanceAmount' => 'float',
        'matchedAmount' => 'float',
        'matchLocalAmount' => 'float',
        'matchRptAmount' => 'float',
        'matchingType' => 'string',
        'isExchangematch' => 'integer',
        'createdUserSystemID' => 'integer',
        'createdUserGroup' => 'string',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

     public function credit_note()
    {
        return $this->belongsTo(CreditNote::class, 'PayMasterAutoId', 'creditNoteAutoID');
    }

     public function reciept_voucher()
    {
        return $this->belongsTo(CustomerReceivePayment::class, 'PayMasterAutoId', 'custReceivePaymentAutoID');
    }

    public function reciept_voucher_doc()
    {
        return $this->belongsTo(CustomerReceivePayment::class, 'PayMasterAutoId', 'custReceivePaymentAutoID')->where('documentSystemID', 21);
    }


    public function segment(){
        return $this->belongsTo('App\Models\SegmentMaster','serviceLineSystemID','serviceLineSystemID');
    }
    
    public function payment_voucher(){
        return $this->belongsTo('App\Models\PaySupplierInvoiceMaster','PayMasterAutoId','PayMasterAutoId')->where('documentSystemID', 4);
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'matchingConfirmedByEmpSystemID', 'employeeSystemID');
    }

    public function cancelled_by()
    {
        return $this->belongsTo('App\Models\Employee', 'cancelledByEmpSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'BPVsupplierID', 'supplierCodeSystem');
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'employee_id', 'employeeSystemID');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\CustomerMaster', 'BPVsupplierID', 'customerCodeSystem');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'matchDocumentMasterAutoID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function transactioncurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'supplierTransCurrencyID', 'currencyID');
    }

    public function localcurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID', 'currencyID');
    }

    public function rptcurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'companyRptCurrencyID', 'currencyID');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\PaySupplierInvoiceDetail', 'matchingDocID', 'matchDocumentMasterAutoID');
    }


}

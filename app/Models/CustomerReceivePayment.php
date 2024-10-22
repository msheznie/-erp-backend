<?php
/**
 * =============================================
 * -- File Name : CustomerReceivePayment.php
 * -- Project Name : ERP
 * -- Module Name :  Accounts receivable
 * -- Author : Mubashir
 * -- Create date : 24 - August 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */

namespace App\Models;

use App\helper\Helper;
use Awobaz\Compoships\Compoships;
use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerReceivePayment",
 *      required={""},
 *      @SWG\Property(
 *          property="custReceivePaymentAutoID",
 *          description="custReceivePaymentAutoID",
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
 *          property="serialNo",
 *          description="serialNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="companyFinanceYearID",
 *          description="companyFinanceYearID",
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
 *          property="intercompanyPaymentID",
 *          description="intercompanyPaymentID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="intercompanyPaymentCode",
 *          description="intercompanyPaymentCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="custPaymentReceiveCode",
 *          description="custPaymentReceiveCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="narration",
 *          description="narration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="customerID",
 *          description="customerID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerGLCodeSystemID",
 *          description="customerGLCodeSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="customerGLCode",
 *          description="customerGLCode",
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
 *          property="bankID",
 *          description="bankID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankAccount",
 *          description="bankAccount",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrency",
 *          description="bankCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankCurrencyER",
 *          description="bankCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="payeeYN",
 *          description="payeeYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="PayeeSelectEmp",
 *          description="PayeeSelectEmp",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="PayeeEmpID",
 *          description="PayeeEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="PayeeName",
 *          description="PayeeName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="PayeeCurrency",
 *          description="PayeeCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custChequeNo",
 *          description="custChequeNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="custChequeBank",
 *          description="custChequeBank",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="receivedAmount",
 *          description="receivedAmount",
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
 *          property="localAmount",
 *          description="localAmount",
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
 *          property="companyRptAmount",
 *          description="companyRptAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="bankAmount",
 *          description="bankAmount",
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
 *          property="confirmedByEmpSystemID",
 *          description="confirmedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="confirmedByEmpID",
 *          description="confirmedByEmpID",
 *          type="string"
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
 *          property="trsCollectedYN",
 *          description="trsCollectedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="trsCollectedByEmpID",
 *          description="trsCollectedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="trsCollectedByEmpName",
 *          description="trsCollectedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="trsClearedYN",
 *          description="trsClearedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="trsClearedByEmpID",
 *          description="trsClearedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="trsClearedByEmpName",
 *          description="trsClearedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="trsClearedAmount",
 *          description="trsClearedAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="bankClearedYN",
 *          description="bankClearedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankClearedAmount",
 *          description="bankClearedAmount",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="bankClearedByEmpID",
 *          description="bankClearedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bankClearedByEmpName",
 *          description="bankClearedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="documentType",
 *          description="documentType",
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
 *          property="RollLevForApp_curr",
 *          description="RollLevForApp_curr",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="expenseClaimOrPettyCash",
 *          description="expenseClaimOrPettyCash",
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
class CustomerReceivePayment extends Model
{
    use Compoships;
    public $table = 'erp_customerreceivepayment';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'custReceivePaymentAutoID';


    public $fillable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'projectID',
        'pdcChequeYN',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYPeriodDateFrom',
        'FYEnd',
        'FYPeriodDateTo',
        'PayMasterAutoId',
        'intercompanyPaymentID',
        'intercompanyPaymentCode',
        'custPaymentReceiveCode',
        'custPaymentReceiveDate',
        'narration',
        'customerID',
        'customerGLCodeSystemID',
        'customerGLCode',
        'custTransactionCurrencyID',
        'custTransactionCurrencyER',
        'bankID',
        'bankAccount',
        'bankCurrency',
        'bankCurrencyER',
        'payeeYN',
        'PayeeSelectEmp',
        'PayeeEmpID',
        'PayeeName',
        'payeeTypeID',
        'PayeeCurrency',
        'custChequeNo',
        'custChequeDate',
        'custChequeBank',
        'receivedAmount',
        'localCurrencyID',
        'localCurrencyER',
        'localAmount',
        'companyRptCurrencyID',
        'companyRptCurrencyER',
        'companyRptAmount',
        'bankAmount',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'approvedByUserID',
        'approvedByUserSystemID',
        'postedDate',
        'trsCollectedYN',
        'trsCollectedByEmpSystemID',
        'trsCollectedByEmpID',
        'trsCollectedByEmpName',
        'trsCollectedDate',
        'trsClearedYN',
        'trsClearedDate',
        'trsClearedByEmpID',
        'trsClearedByEmpName',
        'trsClearedAmount',
        'bankClearedYN',
        'bankClearedAmount',
        'bankReconciliationDate',
        'bankClearedDate',
        'bankClearedByEmpID',
        'bankClearedByEmpName',
        'documentType',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'RollLevForApp_curr',
        'expenseClaimOrPettyCash',
        'createdUserGroup',
        'createdUserID',
        'createdPcID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp',
        'modifiedUserSystemID',
        'createdUserSystemID',
        'refferedBackYN',
        'timesReferred',
        'cancelYN',
        'cancelComment',
        'cancelDate',
        'cancelledByEmpSystemID',
        'canceledByEmpID',
        'canceledByEmpName',
        'companyFinancePeriodID',
        'isVATApplicable',
        'VATPercentage',
        'VATAmount',
        'VATAmountLocal',
        'VATAmountRpt',
        'netAmount',
        'netAmountLocal',
        'netAmountRpt',
        'bankAccountBalance',
        'payment_type_id',
        'custAdvanceAccountSystemID',
        'custAdvanceAccount',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'custReceivePaymentAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'projectID' => 'integer',
        'serialNo' => 'integer',
        'companyFinanceYearID' => 'integer',
        'pdcChequeYN' => 'integer',
        'PayMasterAutoId' => 'integer',
        'intercompanyPaymentID' => 'integer',
        'intercompanyPaymentCode' => 'string',
        'custPaymentReceiveCode' => 'string',
        'narration' => 'string',
        'customerID' => 'integer',
        'customerGLCodeSystemID' => 'integer',
        'customerGLCode' => 'string',
        'custTransactionCurrencyID' => 'integer',
        'custTransactionCurrencyER' => 'float',
        'bankID' => 'integer',
        'bankAccount' => 'integer',
        'bankCurrency' => 'integer',
        'bankCurrencyER' => 'float',
        'payeeYN' => 'integer',
        'PayeeSelectEmp' => 'integer',
        'PayeeEmpID' => 'string',
        'PayeeName' => 'string',
        'PayeeCurrency' => 'integer',
        'custChequeNo' => 'integer',
        'custChequeBank' => 'string',
        'receivedAmount' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'localAmount' => 'float',
        'companyRptCurrencyID' => 'integer',
        'companyRptCurrencyER' => 'float',
        'companyRptAmount' => 'float',
        'bankAmount' => 'float',
        'confirmedYN' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'approved' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'trsCollectedYN' => 'integer',
        'trsCollectedByEmpSystemID' => 'integer',
        'trsCollectedByEmpID' => 'string',
        'trsCollectedByEmpName' => 'string',
        'trsClearedYN' => 'integer',
        'trsClearedByEmpID' => 'string',
        'trsClearedByEmpName' => 'string',
        'trsClearedAmount' => 'float',
        'bankClearedYN' => 'integer',
        'bankClearedAmount' => 'float',
        'bankClearedByEmpID' => 'string',
        'bankClearedByEmpName' => 'string',
        'documentType' => 'integer',
        'matchInvoice' => 'integer',
        'matchingConfirmedYN' => 'integer',
        'matchingConfirmedByEmpID' => 'string',
        'matchingConfirmedByName' => 'string',
        'RollLevForApp_curr' => 'integer',
        'expenseClaimOrPettyCash' => 'integer',
        'createdUserGroup' => 'string',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'modifiedUserSystemID' => 'integer',
        'createdUserSystemID' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'cancelYN' => 'integer',
        'cancelComment' => 'string',
        'cancelledByEmpSystemID' => 'integer',
        'canceledByEmpID' => 'string',
        'canceledByEmpName' => 'string',
        'companyFinancePeriodID' => 'integer',
        'isVATApplicable' => 'integer',
        'VATPercentage' => 'float',
        'VATAmount' => 'float',
        'VATAmountLocal' => 'float',
        'VATAmountRpt' => 'float',
        'netAmount' => 'float',
        'netAmountLocal' => 'float',
        'netAmountRpt' => 'float',
        'bankAccountBalance' => 'double',
        'payment_type_id' => 'integer'
    ];

    protected $appends = ['isFromApi'];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function project()
    {
        return $this->belongsTo('App\Models\ErpProjectMaster', 'projectID', 'id');
    }

    public function details()
    {
        return $this->hasMany('App\Models\CustomerReceivePaymentDetail', 'custReceivePaymentAutoID','custReceivePaymentAutoID')
                    ->where('matchingDocID',0);
    }

    public function directdetails()
    {
        return $this->hasMany('App\Models\DirectReceiptDetail', 'directReceiptAutoID', 'custReceivePaymentAutoID');
    }

    public function advance_receipt_details()
    {
        return $this->hasMany(AdvanceReceiptDetails::class, 'custReceivePaymentAutoID', 'custReceivePaymentAutoID');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\BankAccount', 'bankAccount', 'bankAccountAutoID');
    }

    public function payment_type()
    {
        return $this->belongsTo('App\Models\PaymentType', 'payment_type_id', 'id');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function finance_period_by()
    {
        return $this->belongsTo('App\Models\CompanyFinancePeriod', 'companyFinancePeriodID', 'companyFinancePeriodID');
    }

    public function finance_year_by()
    {
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'custTransactionCurrencyID', 'currencyID');
    }

    public function bank_currency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'bankCurrency', 'currencyID');
    }

    public function localCurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID', 'currencyID');
    }

    public function rptCurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'companyRptCurrencyID', 'currencyID');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function modified_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'custReceivePaymentAutoID');
    }

    public function cancelled_by()
    {
        return $this->belongsTo('App\Models\Employee', 'cancelledByEmpSystemID', 'employeeSystemID');
    }

    public function customer()
    {
        return $this->belongsTo('App\Models\CustomerMaster', 'customerID', 'customerCodeSystem');
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee', 'PayeeEmpID', 'employeeSystemID');
    }

    public function bankledger_by()
    {
        return $this->belongsTo('App\Models\BankLedger', 'custReceivePaymentAutoID', 'documentSystemCode');
    }

    public function pdc_cheque()
    {
        return $this->hasMany('App\Models\PdcLog', 'documentmasterAutoID', 'custReceivePaymentAutoID');
    }

    public function setCustPaymentReceiveDateAttribute($value)
    {
        $this->attributes['custPaymentReceiveDate'] = Helper::dateAddTime($value);
    }

    public function setPostedDateAttribute($value)
    {
        $this->attributes['postedDate'] = Helper::dateAddTime($value);
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'custReceivePaymentAutoID')->where('documentSystemID',21);
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_custreceivepaymentdet','erp_custreceivepaymentdet.custReceivePaymentAutoID','erp_customerreceivepayment.custReceivePaymentAutoID');
    }

    public function scopeCurrencyJoin($q,$as = 'currencymaster' ,$column = 'currency',$columnAs = 'currencyByName'){
        return $q->leftJoin('currencymaster as '.$as,$as.'.currencyID','=','erp_customerreceivepayment.'.$column)
        ->addSelect($as.".CurrencyName as ".$columnAs);

    }

    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_customerreceivepayment.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeCustomerJoin($q,$as = 'customermaster', $column = 'customerID' , $columnAs = 'CustomerName')
    {
        return $q->leftJoin('customermaster as '.$as,$as.'.customerCodeSystem','erp_customerreceivepayment.'.$column)
        ->addSelect($as.".CustomerName as ".$columnAs);
    }

    public function scopeBankJoin($q,$as = 'erp_bankmaster', $column = 'BPVbank' , $columnAs = 'bankName')
    {
        return $q->leftJoin('erp_bankmaster as '.$as,$as.'.bankmasterAutoID','erp_customerreceivepayment.'.$column)
        ->addSelect($as.".bankName as ".$columnAs);
    }

    public function getIsFromApiAttribute()
    {
        $master = DocumentSystemMapping::where('documentSystemId',$this->documentSystemID)->where('documentId',$this->custReceivePaymentAutoID)->first();

        if($master)
        {
            return true;
        }

        return false;
    }

}

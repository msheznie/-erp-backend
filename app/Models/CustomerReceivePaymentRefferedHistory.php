<?php

namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="CustomerReceivePaymentRefferedHistory",
 *      required={""},
 *      @SWG\Property(
 *          property="custReceivePaymentRefferedID",
 *          description="custReceivePaymentRefferedID",
 *          type="integer",
 *          format="int32"
 *      ),
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
 *          property="companyFinancePeriodID",
 *          description="companyFinancePeriodID",
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
 *          property="approvedByUserID",
 *          description="approvedByUserID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="approvedByUserSystemID",
 *          description="approvedByUserSystemID",
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
 *          property="trsCollectedByEmpSystemID",
 *          description="trsCollectedByEmpSystemID",
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
 *          property="trsClearedByEmpSystemID",
 *          description="trsClearedByEmpSystemID",
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
 *          property="bankClearedByEmpSystemID",
 *          description="bankClearedByEmpSystemID",
 *          type="integer",
 *          format="int32"
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
 *          property="refferedBackYN",
 *          description="refferedBackYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="createdUserGroup",
 *          description="createdUserGroup",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="createdUserSystemID",
 *          description="createdUserSystemID",
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
 *      ),
 *      @SWG\Property(
 *          property="modifiedUserSystemID",
 *          description="modifiedUserSystemID",
 *          type="integer",
 *          format="int32"
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
class CustomerReceivePaymentRefferedHistory extends Model
{

    public $table = 'erp_customerreceivepaymentrefferedhistory';

    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey = 'custReceivePaymentRefferedID';

    public $fillable = [
        'custReceivePaymentAutoID',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYPeriodDateFrom',
        'companyFinancePeriodID',
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
        'trsClearedByEmpSystemID',
        'trsClearedByEmpID',
        'trsClearedByEmpName',
        'trsClearedAmount',
        'bankClearedYN',
        'bankClearedAmount',
        'bankReconciliationDate',
        'bankClearedDate',
        'bankClearedByEmpSystemID',
        'bankClearedByEmpID',
        'bankClearedByEmpName',
        'documentType',
        'matchInvoice',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpSystemID',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'RollLevForApp_curr',
        'expenseClaimOrPettyCash',
        'refferedBackYN',
        'timesReferred',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp',
        'bankAccountBalance',
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
        'custReceivePaymentRefferedID' => 'integer',
        'custReceivePaymentAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'serialNo' => 'integer',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
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
        'trsClearedByEmpSystemID' => 'integer',
        'trsClearedByEmpID' => 'string',
        'trsClearedByEmpName' => 'string',
        'trsClearedAmount' => 'float',
        'bankClearedYN' => 'integer',
        'bankClearedAmount' => 'float',
        'bankClearedByEmpSystemID' => 'integer',
        'bankClearedByEmpID' => 'string',
        'bankClearedByEmpName' => 'string',
        'documentType' => 'integer',
        'matchInvoice' => 'integer',
        'matchingConfirmedYN' => 'integer',
        'matchingConfirmedByEmpSystemID' => 'integer',
        'matchingConfirmedByEmpID' => 'string',
        'matchingConfirmedByName' => 'string',
        'RollLevForApp_curr' => 'integer',
        'expenseClaimOrPettyCash' => 'integer',
        'refferedBackYN' => 'integer',
        'timesReferred' => 'integer',
        'createdUserGroup' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'bankAccountBalance' => "double"
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    public function details()
    {
        return $this->hasMany('App\Models\CustomerReceivePaymentDetail', 'custReceivePaymentAutoID', 'custReceivePaymentAutoID');
    }

    public function directdetails()
    {
        return $this->hasMany('App\Models\DirectReceiptDetail', 'directReceiptAutoID', 'custReceivePaymentAutoID');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\BankAccount', 'bankAccount', 'bankAccountAutoID');
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

    public function localCurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID', 'currencyID');
    }

    public function rptCurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'companyRptCurrencyID', 'currencyID');
    }

    public function bankcurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'bankCurrency', 'currencyID');
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

    public function customer()
    {
        return $this->belongsTo('App\Models\CustomerMaster', 'customerID', 'customerCodeSystem');
    }


}

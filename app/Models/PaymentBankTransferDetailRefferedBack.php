<?php
/**
 * =============================================
 * -- File Name : PaymentBankTransferDetailRefferedBack.php
 * -- Project Name : ERP
 * -- Module Name :  Payment Bank Transfer Detail Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 11 - December 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 */
namespace App\Models;

use Eloquent as Model;

/**
 * @SWG\Definition(
 *      definition="PaymentBankTransferDetailRefferedBack",
 *      required={""},
 *      @SWG\Property(
 *          property="refferedbackAutoID",
 *          description="refferedbackAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankLedgerAutoID",
 *          description="bankLedgerAutoID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankRecAutoID",
 *          description="bankRecAutoID",
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
 *          property="documentNarration",
 *          description="documentNarration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="bankID",
 *          description="bankID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankAccountID",
 *          description="bankAccountID",
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
 *          property="documentChequeNo",
 *          description="documentChequeNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="payeeID",
 *          description="payeeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="payeeCode",
 *          description="payeeCode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="payeeName",
 *          description="payeeName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="payeeGLCodeID",
 *          description="payeeGLCodeID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="payeeGLCode",
 *          description="payeeGLCode",
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
 *          property="invoiceType",
 *          description="invoiceType",
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
 *          property="bankRecYear",
 *          description="bankRecYear",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="bankRecMonth",
 *          description="bankRecMonth",
 *          type="integer",
 *          format="int32"
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
 *          property="paymentBankTransferID",
 *          description="paymentBankTransferID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="pulledToBankTransferYN",
 *          description="pulledToBankTransferYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chequePaymentYN",
 *          description="chequePaymentYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chequePrintedYN",
 *          description="chequePrintedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chequePrintedByEmpSystemID",
 *          description="chequePrintedByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chequePrintedByEmpID",
 *          description="chequePrintedByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chequePrintedByEmpName",
 *          description="chequePrintedByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chequeSentToTreasury",
 *          description="chequeSentToTreasury",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chequeSentToTreasuryByEmpSystemID",
 *          description="chequeSentToTreasuryByEmpSystemID",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chequeSentToTreasuryByEmpID",
 *          description="chequeSentToTreasuryByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chequeSentToTreasuryByEmpName",
 *          description="chequeSentToTreasuryByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
 *          type="integer",
 *          format="int32"
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
class PaymentBankTransferDetailRefferedBack extends Model
{

    public $table = 'erp_paymentbanktransfer_detail_refferedback';


    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';
    protected $primaryKey = 'refferedbackAutoID';



    public $fillable = [
        'bankLedgerAutoID',
        'bankRecAutoID',
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'documentSystemCode',
        'documentCode',
        'documentDate',
        'postedDate',
        'documentNarration',
        'bankID',
        'bankAccountID',
        'bankCurrency',
        'bankCurrencyER',
        'documentChequeNo',
        'documentChequeDate',
        'payeeID',
        'payeeCode',
        'payeeName',
        'payeeGLCodeID',
        'payeeGLCode',
        'supplierTransCurrencyID',
        'supplierTransCurrencyER',
        'localCurrencyID',
        'localCurrencyER',
        'companyRptCurrencyID',
        'companyRptCurrencyER',
        'payAmountBank',
        'payAmountSuppTrans',
        'payAmountCompLocal',
        'payAmountCompRpt',
        'invoiceType',
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
        'bankRecYear',
        'bankRecMonth',
        'bankClearedDate',
        'bankClearedByEmpSystemID',
        'bankClearedByEmpID',
        'bankClearedByEmpName',
        'paymentBankTransferID',
        'pulledToBankTransferYN',
        'chequePaymentYN',
        'chequePrintedYN',
        'chequePrintedDateTime',
        'chequePrintedByEmpSystemID',
        'chequePrintedByEmpID',
        'chequePrintedByEmpName',
        'chequeSentToTreasury',
        'chequeSentToTreasuryDate',
        'chequeSentToTreasuryByEmpSystemID',
        'chequeSentToTreasuryByEmpID',
        'chequeSentToTreasuryByEmpName',
        'timesReferred',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp',
        'pdcID'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'refferedbackAutoID' => 'integer',
        'bankLedgerAutoID' => 'integer',
        'bankRecAutoID' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'documentID' => 'string',
        'documentSystemCode' => 'integer',
        'documentCode' => 'string',
        'documentNarration' => 'string',
        'bankID' => 'integer',
        'bankAccountID' => 'integer',
        'bankCurrency' => 'integer',
        'bankCurrencyER' => 'float',
        'documentChequeNo' => 'integer',
        'payeeID' => 'integer',
        'payeeCode' => 'string',
        'payeeName' => 'string',
        'payeeGLCodeID' => 'integer',
        'payeeGLCode' => 'string',
        'supplierTransCurrencyID' => 'integer',
        'supplierTransCurrencyER' => 'float',
        'localCurrencyID' => 'integer',
        'localCurrencyER' => 'float',
        'companyRptCurrencyID' => 'integer',
        'companyRptCurrencyER' => 'float',
        'payAmountBank' => 'float',
        'payAmountSuppTrans' => 'float',
        'payAmountCompLocal' => 'float',
        'payAmountCompRpt' => 'float',
        'invoiceType' => 'integer',
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
        'bankRecYear' => 'integer',
        'bankRecMonth' => 'integer',
        'bankClearedByEmpSystemID' => 'integer',
        'bankClearedByEmpID' => 'string',
        'bankClearedByEmpName' => 'string',
        'paymentBankTransferID' => 'integer',
        'pulledToBankTransferYN' => 'integer',
        'chequePaymentYN' => 'integer',
        'chequePrintedYN' => 'integer',
        'chequePrintedByEmpSystemID' => 'integer',
        'chequePrintedByEmpID' => 'string',
        'chequePrintedByEmpName' => 'string',
        'chequeSentToTreasury' => 'integer',
        'chequeSentToTreasuryByEmpSystemID' => 'integer',
        'chequeSentToTreasuryByEmpID' => 'string',
        'chequeSentToTreasuryByEmpName' => 'string',
        'timesReferred' => 'integer',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'pdcID' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function supplier_by()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'payeeID', 'supplierCodeSystem');
    }

    public function bank_account()
    {
        return $this->belongsTo('App\Models\BankAccount', 'bankAccountID', 'bankAccountAutoID');
    }

    public function bank_cleared_by()
    {
        return $this->belongsTo('App\Models\Employee', 'modifiedUserSystemID', 'employeeSystemID');
    }

    public function bank_currency_by()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'bankCurrency', 'currencyID');
    }

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function payee_bank_memos()
    {
        return $this->hasMany('App\Models\BankMemoPayee','documentSystemCode','documentSystemCode');
    }
}

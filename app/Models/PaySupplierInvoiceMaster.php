<?php
/**
 * =============================================
 * -- File Name : PaySupplierInvoiceMaster.php
 * -- Project Name : ERP
 * -- Module Name :  PaySupplierInvoiceMaster
 * -- Author : Nazir
 * -- Create date : 09 - August 2018
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
 *      definition="PaySupplierInvoiceMaster",
 *      required={""},
 *      @SWG\Property(
 *          property="PayMasterAutoId",
 *          description="PayMasterAutoId",
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
 *          property="BPVcode",
 *          description="BPVcode",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="BPVbank",
 *          description="BPVbank",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="BPVAccount",
 *          description="BPVAccount",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="BPVchequeNo",
 *          description="BPVchequeNo",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="BPVNarration",
 *          description="BPVNarration",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="BPVbankCurrency",
 *          description="BPVbankCurrency",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="BPVbankCurrencyER",
 *          description="BPVbankCurrencyER",
 *          type="number",
 *          format="float"
 *      ),
 *      @SWG\Property(
 *          property="directPaymentpayeeYN",
 *          description="directPaymentpayeeYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="directPaymentPayeeSelectEmp",
 *          description="directPaymentPayeeSelectEmp",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="directPaymentPayeeEmpID",
 *          description="directPaymentPayeeEmpID",
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
 *          property="directPayeeBankMemo",
 *          description="directPayeeBankMemo",
 *          type="string"
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
 *          property="suppAmountDocTotal",
 *          description="suppAmountDocTotal",
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
 *          property="chequeReceivedByTreasury",
 *          description="chequeReceivedByTreasury",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="chequeReceivedByTreasuryByEmpID",
 *          description="chequeReceivedByTreasuryByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="chequeReceivedByTreasuryByEmpName",
 *          description="chequeReceivedByTreasuryByEmpName",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="timesReferred",
 *          description="timesReferred",
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
 *          property="noOfApprovalLevels",
 *          description="noOfApprovalLevels",
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
 *          property="finalSettlementYN",
 *          description="finalSettlementYN",
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
 *          property="interCompanyToID",
 *          description="interCompanyToID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="ReversedYN",
 *          description="ReversedYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cancelYN",
 *          description="cancelYN",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @SWG\Property(
 *          property="cancelComment",
 *          description="cancelComment",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="canceledByEmpID",
 *          description="canceledByEmpID",
 *          type="string"
 *      ),
 *      @SWG\Property(
 *          property="canceledByEmpName",
 *          description="canceledByEmpName",
 *          type="string"
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
class PaySupplierInvoiceMaster extends Model
{
    use Compoships;
    public $table = 'erp_paysupplierinvoicemaster';
    
    const CREATED_AT = 'createdDateTime';
    const UPDATED_AT = 'timestamp';

    protected $primaryKey  = 'PayMasterAutoId';

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
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'BPVcode',
        'BPVdate',
        'BPVbank',
        'BPVAccount',
        'BPVchequeNo',
        'BPVchequeDate',
        'BPVNarration',
        'BPVbankCurrency',
        'BPVbankCurrencyER',
        'directPaymentpayeeYN',
        'directPaymentPayeeSelectEmp',
        'directPaymentPayeeEmpID',
        'directPaymentPayee',
        'directPayeeCurrency',
        'directPayeeBankMemo',
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
        'payAmountCompLocal',
        'payAmountCompRpt',
        'suppAmountDocTotal',
        'createMonthlyDeduction',
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
        'invoiceType',
        'matchInvoice',
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
        'chequePaymentYN',
        'chequePrintedYN',
        'chequePrintedDateTime',
        'chequePrintedByEmpSystemID',
        'chequePrintedByEmpID',
        'chequePrintedByEmpName',
        'chequeSentToTreasury',
        'chequeSentToTreasuryByEmpSystemID',
        'chequeSentToTreasuryByEmpID',
        'chequeSentToTreasuryByEmpName',
        'chequeSentToTreasuryDate',
        'chequeReceivedByTreasury',
        'chequeReceivedByTreasuryByEmpSystemID',
        'chequeReceivedByTreasuryByEmpID',
        'chequeReceivedByTreasuryByEmpName',
        'chequeReceivedByTreasuryDate',
        'timesReferred',
        'refferedBackYN',
        'matchingConfirmedYN',
        'matchingConfirmedByEmpSystemID',
        'matchingConfirmedByEmpID',
        'matchingConfirmedByName',
        'matchingConfirmedDate',
        'RollLevForApp_curr',
        'noOfApprovalLevels',
        'isRelatedPartyYN',
        'advancePaymentTypeID',
        'isPdcChequeYN',
        'finalSettlementYN',
        'expenseClaimOrPettyCash',
        'interCompanyToSystemID',
        'interCompanyToID',
        'ReversedYN',
        'cancelYN',
        'cancelComment',
        'cancelDate',
        'cancelledByEmpSystemID',
        'canceledByEmpID',
        'canceledByEmpName',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'timestamp',
        'rcmActivated',
        'retentionVatAmount',
        'VATAmount',
        'VATAmountBank',
        'VATAmountLocal',
        'VATAmountRpt',
        'netAmount',
        'netAmountLocal',
        'netAmountRpt',
        'bankAccountBalance',
        'payment_mode',
        'advanceAccountSystemID',
        'applyVAT',
        'AdvanceAccount',
        'employeeAdvanceAccountSystemID',
        'employeeAdvanceAccount',
        'isDelegation'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'PayMasterAutoId' => 'integer',
        'companySystemID' => 'integer',
        'companyID' => 'string',
        'documentSystemID' => 'integer',
        'pdcChequeYN' => 'integer',
        'documentID' => 'string',
        'projectID' => 'integer',
        'serialNo' => 'integer',
        'companyFinanceYearID' => 'integer',
        'companyFinancePeriodID' => 'integer',
        'BPVcode' => 'string',
        'BPVbank' => 'integer',
        'BPVAccount' => 'integer',
        'BPVchequeNo' => 'integer',
        'BPVNarration' => 'string',
        'BPVbankCurrency' => 'integer',
        'BPVbankCurrencyER' => 'float',
        'directPaymentpayeeYN' => 'integer',
        'directPaymentPayeeSelectEmp' => 'integer',
        'directPaymentPayeeEmpID' => 'string',
        'directPaymentPayee' => 'string',
        'directPayeeCurrency' => 'integer',
        'directPayeeBankMemo' => 'string',
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
        'payAmountCompLocal' => 'float',
        'payAmountCompRpt' => 'float',
        'suppAmountDocTotal' => 'float',
        'confirmedYN' => 'integer',
        'createMonthlyDeduction' => 'integer',
        'confirmedByEmpSystemID' => 'integer',
        'confirmedByEmpID' => 'string',
        'confirmedByName' => 'string',
        'approved' => 'integer',
        'approvedByUserID' => 'string',
        'approvedByUserSystemID' => 'integer',
        'invoiceType' => 'integer',
        'matchInvoice' => 'integer',
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
        'chequePaymentYN' => 'integer',
        'chequePrintedYN' => 'integer',
        'chequePrintedByEmpSystemID' => 'integer',
        'chequePrintedByEmpID' => 'string',
        'chequePrintedByEmpName' => 'string',
        'chequeSentToTreasury' => 'integer',
        'chequeSentToTreasuryByEmpSystemID' => 'integer',
        'chequeSentToTreasuryByEmpID' => 'string',
        'chequeSentToTreasuryByEmpName' => 'string',
        'chequeReceivedByTreasury' => 'integer',
        'chequeReceivedByTreasuryByEmpSystemID' => 'integer',
        'chequeReceivedByTreasuryByEmpID' => 'string',
        'chequeReceivedByTreasuryByEmpName' => 'string',
        'timesReferred' => 'integer',
        'refferedBackYN' => 'integer',
        'matchingConfirmedYN' => 'integer',
        'matchingConfirmedByEmpSystemID' => 'integer',
        'matchingConfirmedByEmpID' => 'string',
        'matchingConfirmedByName' => 'string',
        'RollLevForApp_curr' => 'integer',
        'noOfApprovalLevels' => 'integer',
        'isRelatedPartyYN' => 'integer',
        'advancePaymentTypeID' => 'integer',
        'isPdcChequeYN' => 'integer',
        'finalSettlementYN' => 'integer',
        'expenseClaimOrPettyCash' => 'integer',
        'interCompanyToSystemID' => 'integer',
        'interCompanyToID' => 'string',
        'ReversedYN' => 'integer',
        'cancelYN' => 'integer',
        'cancelComment' => 'string',
        'cancelledByEmpSystemID' => 'integer',
        'canceledByEmpID' => 'string',
        'canceledByEmpName' => 'string',
        'createdUserGroup' => 'string',
        'createdUserSystemID' => 'integer',
        'createdUserID' => 'string',
        'createdPcID' => 'string',
        'modifiedUserSystemID' => 'integer',
        'modifiedUser' => 'string',
        'modifiedPc' => 'string',
        'bankAccountBalance' => 'float',
        'applyVAT' => 'integer',
        'payment_mode' => 'integer'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    public function company()
    {
        return $this->belongsTo('App\Models\Company', 'companySystemID', 'companySystemID');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\ErpProjectMaster', 'projectID', 'id');
    }

    public function paymentmode()
    {
        return $this->belongsTo('App\Models\PaymentType', 'payment_mode', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\SupplierMaster', 'BPVsupplierID', 'supplierCodeSystem');
    }

    public function bankaccount()
    {
        return $this->belongsTo('App\Models\BankAccount', 'BPVAccount', 'bankAccountAutoID');
    }

    public function transactioncurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'supplierTransCurrencyID', 'currencyID');
    }

    public function supplierdetail()
    {
        return $this->hasMany('App\Models\PaySupplierInvoiceDetail', 'PayMasterAutoId', 'PayMasterAutoId');
    }

    public function directdetail()
    {
        return $this->hasMany('App\Models\DirectPaymentDetails', 'directPaymentAutoID', 'PayMasterAutoId');
    }

    public function localcurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'localCurrencyID', 'currencyID');
    }

    public function rptcurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'companyRptCurrencyID', 'currencyID');
    }

    public function advancedetail()
    {
        return $this->hasMany('App\Models\AdvancePaymentDetails', 'PayMasterAutoId', 'PayMasterAutoId');
    }

    public function approved_by()
    {
        return $this->hasMany('App\Models\DocumentApproved', 'documentSystemCode', 'PayMasterAutoId');
    }

    public function confirmed_by()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmedByEmpSystemID', 'employeeSystemID');
    }

    public function expense_claim_type()
    {
        return $this->belongsTo('App\Models\ExpenseClaimType', 'expenseClaimOrPettyCash', 'expenseClaimTypeID');
    }

    public function created_by()
    {
        return $this->belongsTo('App\Models\Employee', 'createdUserSystemID', 'employeeSystemID');
    }

    public function cheque_treasury_by()
    {
        return $this->belongsTo('App\Models\Employee', 'chequeSentToTreasuryByEmpSystemID', 'employeeSystemID');
    }

    public function bank()
    {
        return $this->belongsTo('App\Models\BankAccount', 'BPVAccount', 'bankAccountAutoID');
    }

    public function suppliercurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'supplierTransCurrencyID', 'currencyID');
    }

    public function bankcurrency()
    {
        return $this->belongsTo('App\Models\CurrencyMaster', 'BPVbankCurrency', 'currencyID');
    }

    public function financeperiod_by()
    {
        return $this->belongsTo('App\Models\CompanyFinancePeriod', 'companyFinancePeriodID', 'companyFinancePeriodID');
    }

    public function financeyear_by()
    {
        return $this->belongsTo('App\Models\CompanyFinanceYear', 'companyFinanceYearID', 'companyFinanceYearID');
    }

    public function cancelled_by()
    {
        return $this->belongsTo('App\Models\Employee', 'cancelledByEmpSystemID', 'employeeSystemID');
    }

    public function bankledger_by()
    {
        return $this->belongsTo('App\Models\BankLedger', 'PayMasterAutoId', 'documentSystemCode');
    }

    public function modified_by(){
        return $this->belongsTo('App\Models\Employee','modifiedUserSystemID','employeeSystemID');
    }

    public function setBPVdateAttribute($value)
    {
        $this->attributes['BPVdate'] = Helper::dateAddTime($value);
    }

    public function setPostedDateAttribute($value)
    {
        $this->attributes['postedDate'] = Helper::dateAddTime($value);
    }

    public function bankledgers()
    {
        return $this->hasMany('App\Models\BankLedger', 'documentSystemCode', 'PayMasterAutoId');
    }

    public function payee_memo()
    {
        return $this->hasMany('App\Models\BankMemoPayee', 'documentSystemCode', 'PayMasterAutoId');
    }

    public function audit_trial()
    {
        return $this->hasMany('App\Models\AuditTrail', 'documentSystemCode', 'PayMasterAutoId')->where('documentSystemID',4);
    }

    public function payee()
    {
        return $this->belongsTo('App\Models\Employee', 'directPaymentPayeeEmpID', 'employeeSystemID');
    }

    public function company_to(){
        return $this->belongsTo('App\Models\Company','interCompanyToSystemID','companySystemID');
    }

    
    public function scopeEmployeeJoin($q,$as = 'employees' ,$column = 'createdUserSystemID',$columnAs = 'empName'){
        $q->leftJoin('employees as '. $as, $as.'.employeeSystemID', '=', 'erp_paysupplierinvoicemaster.'.$column)
            ->addSelect($as.".empName as ".$columnAs);
    }

    public function scopeCurrencyJoin($q,$as = 'currencymaster' ,$column = 'supplierTransactionCurrencyID',$columnAs = 'CurrencyName'){
        return $q->leftJoin('currencymaster as '.$as,$as.'.currencyID','=','erp_paysupplierinvoicemaster.'.$column)
        ->addSelect($as.".CurrencyName as ".$columnAs);

    }

    public function scopeSupplierJoin($q,$as = 'supplier', $column = 'BPVsupplierID' , $columnAs = 'primarySupplierCode')
    {
        return $q->leftJoin('suppliermaster as '.$as,$as.'.supplierCodeSystem','erp_paysupplierinvoicemaster.'.$column)
        ->addSelect($as.".supplierName as ".$columnAs);
    }

        public function scopeCompanyJoin($q,$as = 'companymaster', $column = 'companySystemID' , $columnAs = 'CompanyName')
    {
        return $q->leftJoin('companymaster as '.$as,$as.'.companySystemID','erp_paysupplierinvoicemaster.'.$column)
        ->addSelect($as.".CompanyName as ".$columnAs);
    }

    public function scopeDetailJoin($q)
    {
        return $q->join('erp_paysupplierinvoicedetail','erp_paysupplierinvoicedetail.PayMasterAutoId','erp_paysupplierinvoicemaster.PayMasterAutoId');
    }

    public function scopeBankJoin($q,$as = 'erp_bankmaster', $column = 'BPVbank' , $columnAs = 'bankName')
    {
        return $q->leftJoin('erp_bankmaster as '.$as,$as.'.bankmasterAutoID','erp_paysupplierinvoicemaster.'.$column)
        ->addSelect($as.".bankName as ".$columnAs);
    }


    public function pdc_cheque()
    {
        return $this->hasMany('App\Models\PdcLog', 'documentmasterAutoID', 'PayMasterAutoId');
    }

    public function bank_charge()
    {
        return $this->hasMany('App\Models\PaymentVoucherBankChargeDetails', 'payMasterAutoID', 'PayMasterAutoId');
    }
    
}

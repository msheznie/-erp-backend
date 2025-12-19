<?php

use Faker\Factory as Faker;
use App\Models\PaySupplierInvoiceMasterReferback;
use App\Repositories\PaySupplierInvoiceMasterReferbackRepository;

trait MakePaySupplierInvoiceMasterReferbackTrait
{
    /**
     * Create fake instance of PaySupplierInvoiceMasterReferback and save it in database
     *
     * @param array $paySupplierInvoiceMasterReferbackFields
     * @return PaySupplierInvoiceMasterReferback
     */
    public function makePaySupplierInvoiceMasterReferback($paySupplierInvoiceMasterReferbackFields = [])
    {
        /** @var PaySupplierInvoiceMasterReferbackRepository $paySupplierInvoiceMasterReferbackRepo */
        $paySupplierInvoiceMasterReferbackRepo = App::make(PaySupplierInvoiceMasterReferbackRepository::class);
        $theme = $this->fakePaySupplierInvoiceMasterReferbackData($paySupplierInvoiceMasterReferbackFields);
        return $paySupplierInvoiceMasterReferbackRepo->create($theme);
    }

    /**
     * Get fake instance of PaySupplierInvoiceMasterReferback
     *
     * @param array $paySupplierInvoiceMasterReferbackFields
     * @return PaySupplierInvoiceMasterReferback
     */
    public function fakePaySupplierInvoiceMasterReferback($paySupplierInvoiceMasterReferbackFields = [])
    {
        return new PaySupplierInvoiceMasterReferback($this->fakePaySupplierInvoiceMasterReferbackData($paySupplierInvoiceMasterReferbackFields));
    }

    /**
     * Get fake data of PaySupplierInvoiceMasterReferback
     *
     * @param array $postFields
     * @return array
     */
    public function fakePaySupplierInvoiceMasterReferbackData($paySupplierInvoiceMasterReferbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'PayMasterAutoId' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'companyFinancePeriodID' => $fake->randomDigitNotNull,
            'FYPeriodDateFrom' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateTo' => $fake->date('Y-m-d H:i:s'),
            'BPVcode' => $fake->word,
            'BPVdate' => $fake->date('Y-m-d H:i:s'),
            'BPVbank' => $fake->randomDigitNotNull,
            'BPVAccount' => $fake->randomDigitNotNull,
            'BPVchequeNo' => $fake->randomDigitNotNull,
            'BPVchequeDate' => $fake->date('Y-m-d H:i:s'),
            'BPVNarration' => $fake->word,
            'BPVbankCurrency' => $fake->randomDigitNotNull,
            'BPVbankCurrencyER' => $fake->randomDigitNotNull,
            'directPaymentpayeeYN' => $fake->randomDigitNotNull,
            'directPaymentPayeeSelectEmp' => $fake->randomDigitNotNull,
            'directPaymentPayeeEmpID' => $fake->word,
            'directPaymentPayee' => $fake->word,
            'directPayeeCurrency' => $fake->randomDigitNotNull,
            'directPayeeBankMemo' => $fake->text,
            'BPVsupplierID' => $fake->randomDigitNotNull,
            'supplierGLCodeSystemID' => $fake->randomDigitNotNull,
            'supplierGLCode' => $fake->word,
            'supplierTransCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransCurrencyER' => $fake->randomDigitNotNull,
            'supplierDefCurrencyID' => $fake->randomDigitNotNull,
            'supplierDefCurrencyER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'companyRptCurrencyID' => $fake->randomDigitNotNull,
            'companyRptCurrencyER' => $fake->randomDigitNotNull,
            'payAmountBank' => $fake->randomDigitNotNull,
            'payAmountSuppTrans' => $fake->randomDigitNotNull,
            'payAmountSuppDef' => $fake->randomDigitNotNull,
            'payAmountCompLocal' => $fake->randomDigitNotNull,
            'payAmountCompRpt' => $fake->randomDigitNotNull,
            'suppAmountDocTotal' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'invoiceType' => $fake->randomDigitNotNull,
            'matchInvoice' => $fake->randomDigitNotNull,
            'trsCollectedYN' => $fake->randomDigitNotNull,
            'trsCollectedByEmpSystemID' => $fake->randomDigitNotNull,
            'trsCollectedByEmpID' => $fake->word,
            'trsCollectedByEmpName' => $fake->word,
            'trsCollectedDate' => $fake->date('Y-m-d H:i:s'),
            'trsClearedYN' => $fake->randomDigitNotNull,
            'trsClearedDate' => $fake->date('Y-m-d H:i:s'),
            'trsClearedByEmpSystemID' => $fake->randomDigitNotNull,
            'trsClearedByEmpID' => $fake->word,
            'trsClearedByEmpName' => $fake->word,
            'trsClearedAmount' => $fake->randomDigitNotNull,
            'bankClearedYN' => $fake->randomDigitNotNull,
            'bankClearedAmount' => $fake->randomDigitNotNull,
            'bankReconciliationDate' => $fake->date('Y-m-d H:i:s'),
            'bankClearedDate' => $fake->date('Y-m-d H:i:s'),
            'bankClearedByEmpSystemID' => $fake->randomDigitNotNull,
            'bankClearedByEmpID' => $fake->word,
            'bankClearedByEmpName' => $fake->word,
            'chequePaymentYN' => $fake->randomDigitNotNull,
            'chequePrintedYN' => $fake->randomDigitNotNull,
            'chequePrintedDateTime' => $fake->date('Y-m-d H:i:s'),
            'chequePrintedByEmpSystemID' => $fake->randomDigitNotNull,
            'chequePrintedByEmpID' => $fake->word,
            'chequePrintedByEmpName' => $fake->word,
            'chequeSentToTreasury' => $fake->randomDigitNotNull,
            'chequeSentToTreasuryByEmpSystemID' => $fake->randomDigitNotNull,
            'chequeSentToTreasuryByEmpID' => $fake->word,
            'chequeSentToTreasuryByEmpName' => $fake->word,
            'chequeSentToTreasuryDate' => $fake->date('Y-m-d H:i:s'),
            'chequeReceivedByTreasury' => $fake->randomDigitNotNull,
            'chequeReceivedByTreasuryByEmpSystemID' => $fake->randomDigitNotNull,
            'chequeReceivedByTreasuryByEmpID' => $fake->word,
            'chequeReceivedByTreasuryByEmpName' => $fake->word,
            'chequeReceivedByTreasuryDate' => $fake->date('Y-m-d H:i:s'),
            'timesReferred' => $fake->randomDigitNotNull,
            'matchingConfirmedYN' => $fake->randomDigitNotNull,
            'matchingConfirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'matchingConfirmedByEmpID' => $fake->word,
            'matchingConfirmedByName' => $fake->word,
            'matchingConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'refferedBackYN' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'noOfApprovalLevels' => $fake->randomDigitNotNull,
            'isRelatedPartyYN' => $fake->randomDigitNotNull,
            'advancePaymentTypeID' => $fake->randomDigitNotNull,
            'isPdcChequeYN' => $fake->randomDigitNotNull,
            'finalSettlementYN' => $fake->randomDigitNotNull,
            'expenseClaimOrPettyCash' => $fake->randomDigitNotNull,
            'interCompanyToSystemID' => $fake->randomDigitNotNull,
            'interCompanyToID' => $fake->word,
            'ReversedYN' => $fake->randomDigitNotNull,
            'cancelYN' => $fake->randomDigitNotNull,
            'cancelComment' => $fake->text,
            'cancelDate' => $fake->date('Y-m-d H:i:s'),
            'cancelledByEmpSystemID' => $fake->randomDigitNotNull,
            'canceledByEmpID' => $fake->word,
            'canceledByEmpName' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $paySupplierInvoiceMasterReferbackFields);
    }
}

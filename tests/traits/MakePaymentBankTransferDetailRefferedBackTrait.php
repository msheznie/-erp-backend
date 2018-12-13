<?php

use Faker\Factory as Faker;
use App\Models\PaymentBankTransferDetailRefferedBack;
use App\Repositories\PaymentBankTransferDetailRefferedBackRepository;

trait MakePaymentBankTransferDetailRefferedBackTrait
{
    /**
     * Create fake instance of PaymentBankTransferDetailRefferedBack and save it in database
     *
     * @param array $paymentBankTransferDetailRefferedBackFields
     * @return PaymentBankTransferDetailRefferedBack
     */
    public function makePaymentBankTransferDetailRefferedBack($paymentBankTransferDetailRefferedBackFields = [])
    {
        /** @var PaymentBankTransferDetailRefferedBackRepository $paymentBankTransferDetailRefferedBackRepo */
        $paymentBankTransferDetailRefferedBackRepo = App::make(PaymentBankTransferDetailRefferedBackRepository::class);
        $theme = $this->fakePaymentBankTransferDetailRefferedBackData($paymentBankTransferDetailRefferedBackFields);
        return $paymentBankTransferDetailRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of PaymentBankTransferDetailRefferedBack
     *
     * @param array $paymentBankTransferDetailRefferedBackFields
     * @return PaymentBankTransferDetailRefferedBack
     */
    public function fakePaymentBankTransferDetailRefferedBack($paymentBankTransferDetailRefferedBackFields = [])
    {
        return new PaymentBankTransferDetailRefferedBack($this->fakePaymentBankTransferDetailRefferedBackData($paymentBankTransferDetailRefferedBackFields));
    }

    /**
     * Get fake data of PaymentBankTransferDetailRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakePaymentBankTransferDetailRefferedBackData($paymentBankTransferDetailRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'bankLedgerAutoID' => $fake->randomDigitNotNull,
            'bankRecAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'documentSystemCode' => $fake->randomDigitNotNull,
            'documentCode' => $fake->word,
            'documentDate' => $fake->date('Y-m-d H:i:s'),
            'postedDate' => $fake->date('Y-m-d H:i:s'),
            'documentNarration' => $fake->text,
            'bankID' => $fake->randomDigitNotNull,
            'bankAccountID' => $fake->randomDigitNotNull,
            'bankCurrency' => $fake->randomDigitNotNull,
            'bankCurrencyER' => $fake->randomDigitNotNull,
            'documentChequeNo' => $fake->randomDigitNotNull,
            'documentChequeDate' => $fake->date('Y-m-d H:i:s'),
            'payeeID' => $fake->randomDigitNotNull,
            'payeeCode' => $fake->word,
            'payeeName' => $fake->word,
            'payeeGLCodeID' => $fake->randomDigitNotNull,
            'payeeGLCode' => $fake->word,
            'supplierTransCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransCurrencyER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'companyRptCurrencyID' => $fake->randomDigitNotNull,
            'companyRptCurrencyER' => $fake->randomDigitNotNull,
            'payAmountBank' => $fake->randomDigitNotNull,
            'payAmountSuppTrans' => $fake->randomDigitNotNull,
            'payAmountCompLocal' => $fake->randomDigitNotNull,
            'payAmountCompRpt' => $fake->randomDigitNotNull,
            'invoiceType' => $fake->randomDigitNotNull,
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
            'bankRecYear' => $fake->randomDigitNotNull,
            'bankRecMonth' => $fake->randomDigitNotNull,
            'bankClearedDate' => $fake->date('Y-m-d H:i:s'),
            'bankClearedByEmpSystemID' => $fake->randomDigitNotNull,
            'bankClearedByEmpID' => $fake->word,
            'bankClearedByEmpName' => $fake->word,
            'paymentBankTransferID' => $fake->randomDigitNotNull,
            'pulledToBankTransferYN' => $fake->randomDigitNotNull,
            'chequePaymentYN' => $fake->randomDigitNotNull,
            'chequePrintedYN' => $fake->randomDigitNotNull,
            'chequePrintedDateTime' => $fake->date('Y-m-d H:i:s'),
            'chequePrintedByEmpSystemID' => $fake->randomDigitNotNull,
            'chequePrintedByEmpID' => $fake->word,
            'chequePrintedByEmpName' => $fake->word,
            'chequeSentToTreasury' => $fake->randomDigitNotNull,
            'chequeSentToTreasuryDate' => $fake->date('Y-m-d H:i:s'),
            'chequeSentToTreasuryByEmpSystemID' => $fake->randomDigitNotNull,
            'chequeSentToTreasuryByEmpID' => $fake->word,
            'chequeSentToTreasuryByEmpName' => $fake->word,
            'timesReferred' => $fake->randomDigitNotNull,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $paymentBankTransferDetailRefferedBackFields);
    }
}

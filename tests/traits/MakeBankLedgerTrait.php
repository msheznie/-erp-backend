<?php

use Faker\Factory as Faker;
use App\Models\BankLedger;
use App\Repositories\BankLedgerRepository;

trait MakeBankLedgerTrait
{
    /**
     * Create fake instance of BankLedger and save it in database
     *
     * @param array $bankLedgerFields
     * @return BankLedger
     */
    public function makeBankLedger($bankLedgerFields = [])
    {
        /** @var BankLedgerRepository $bankLedgerRepo */
        $bankLedgerRepo = App::make(BankLedgerRepository::class);
        $theme = $this->fakeBankLedgerData($bankLedgerFields);
        return $bankLedgerRepo->create($theme);
    }

    /**
     * Get fake instance of BankLedger
     *
     * @param array $bankLedgerFields
     * @return BankLedger
     */
    public function fakeBankLedger($bankLedgerFields = [])
    {
        return new BankLedger($this->fakeBankLedgerData($bankLedgerFields));
    }

    /**
     * Get fake data of BankLedger
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBankLedgerData($bankLedgerFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'documentSystemCode' => $fake->randomDigitNotNull,
            'documentCode' => $fake->word,
            'documentDate' => $fake->date('Y-m-d H:i:s'),
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
            'bankClearedDate' => $fake->date('Y-m-d H:i:s'),
            'bankClearedByEmpSystemID' => $fake->randomDigitNotNull,
            'bankClearedByEmpID' => $fake->word,
            'bankClearedByEmpName' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $bankLedgerFields);
    }
}

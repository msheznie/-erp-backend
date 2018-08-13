<?php

use Faker\Factory as Faker;
use App\Models\AccountsPayableLedger;
use App\Repositories\AccountsPayableLedgerRepository;

trait MakeAccountsPayableLedgerTrait
{
    /**
     * Create fake instance of AccountsPayableLedger and save it in database
     *
     * @param array $accountsPayableLedgerFields
     * @return AccountsPayableLedger
     */
    public function makeAccountsPayableLedger($accountsPayableLedgerFields = [])
    {
        /** @var AccountsPayableLedgerRepository $accountsPayableLedgerRepo */
        $accountsPayableLedgerRepo = App::make(AccountsPayableLedgerRepository::class);
        $theme = $this->fakeAccountsPayableLedgerData($accountsPayableLedgerFields);
        return $accountsPayableLedgerRepo->create($theme);
    }

    /**
     * Get fake instance of AccountsPayableLedger
     *
     * @param array $accountsPayableLedgerFields
     * @return AccountsPayableLedger
     */
    public function fakeAccountsPayableLedger($accountsPayableLedgerFields = [])
    {
        return new AccountsPayableLedger($this->fakeAccountsPayableLedgerData($accountsPayableLedgerFields));
    }

    /**
     * Get fake data of AccountsPayableLedger
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAccountsPayableLedgerData($accountsPayableLedgerFields = [])
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
            'supplierCodeSystem' => $fake->randomDigitNotNull,
            'supplierInvoiceNo' => $fake->word,
            'supplierInvoiceDate' => $fake->date('Y-m-d H:i:s'),
            'supplierTransCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransER' => $fake->randomDigitNotNull,
            'supplierInvoiceAmount' => $fake->randomDigitNotNull,
            'supplierDefaultCurrencyID' => $fake->randomDigitNotNull,
            'supplierDefaultCurrencyER' => $fake->randomDigitNotNull,
            'supplierDefaultAmount' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'comRptCurrencyID' => $fake->randomDigitNotNull,
            'comRptER' => $fake->randomDigitNotNull,
            'comRptAmount' => $fake->randomDigitNotNull,
            'isInvoiceLockedYN' => $fake->randomDigitNotNull,
            'lockedBy' => $fake->word,
            'lockedByEmpName' => $fake->word,
            'lockedDate' => $fake->date('Y-m-d H:i:s'),
            'lockedComments' => $fake->text,
            'invoiceType' => $fake->randomDigitNotNull,
            'selectedToPaymentInv' => $fake->randomDigitNotNull,
            'fullyInvoice' => $fake->randomDigitNotNull,
            'advancePaymentTypeID' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $accountsPayableLedgerFields);
    }
}

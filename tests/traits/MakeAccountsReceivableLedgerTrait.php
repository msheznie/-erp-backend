<?php

use Faker\Factory as Faker;
use App\Models\AccountsReceivableLedger;
use App\Repositories\AccountsReceivableLedgerRepository;

trait MakeAccountsReceivableLedgerTrait
{
    /**
     * Create fake instance of AccountsReceivableLedger and save it in database
     *
     * @param array $accountsReceivableLedgerFields
     * @return AccountsReceivableLedger
     */
    public function makeAccountsReceivableLedger($accountsReceivableLedgerFields = [])
    {
        /** @var AccountsReceivableLedgerRepository $accountsReceivableLedgerRepo */
        $accountsReceivableLedgerRepo = App::make(AccountsReceivableLedgerRepository::class);
        $theme = $this->fakeAccountsReceivableLedgerData($accountsReceivableLedgerFields);
        return $accountsReceivableLedgerRepo->create($theme);
    }

    /**
     * Get fake instance of AccountsReceivableLedger
     *
     * @param array $accountsReceivableLedgerFields
     * @return AccountsReceivableLedger
     */
    public function fakeAccountsReceivableLedger($accountsReceivableLedgerFields = [])
    {
        return new AccountsReceivableLedger($this->fakeAccountsReceivableLedgerData($accountsReceivableLedgerFields));
    }

    /**
     * Get fake data of AccountsReceivableLedger
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAccountsReceivableLedgerData($accountsReceivableLedgerFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyID' => $fake->word,
            'documentID' => $fake->word,
            'documentCodeSystem' => $fake->randomDigitNotNull,
            'documentCode' => $fake->word,
            'documentDate' => $fake->date('Y-m-d H:i:s'),
            'customerID' => $fake->randomDigitNotNull,
            'InvoiceNo' => $fake->word,
            'InvoiceDate' => $fake->date('Y-m-d H:i:s'),
            'custTransCurrencyID' => $fake->randomDigitNotNull,
            'custTransER' => $fake->randomDigitNotNull,
            'custInvoiceAmount' => $fake->randomDigitNotNull,
            'custDefaultCurrencyID' => $fake->randomDigitNotNull,
            'custDefaultCurrencyER' => $fake->randomDigitNotNull,
            'custDefaultAmount' => $fake->randomDigitNotNull,
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
            'selectedToPaymentInv' => $fake->randomDigitNotNull,
            'fullyInvoiced' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'documentType' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $accountsReceivableLedgerFields);
    }
}

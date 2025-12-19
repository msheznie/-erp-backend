<?php

use Faker\Factory as Faker;
use App\Models\BookInvSuppDetRefferedBack;
use App\Repositories\BookInvSuppDetRefferedBackRepository;

trait MakeBookInvSuppDetRefferedBackTrait
{
    /**
     * Create fake instance of BookInvSuppDetRefferedBack and save it in database
     *
     * @param array $bookInvSuppDetRefferedBackFields
     * @return BookInvSuppDetRefferedBack
     */
    public function makeBookInvSuppDetRefferedBack($bookInvSuppDetRefferedBackFields = [])
    {
        /** @var BookInvSuppDetRefferedBackRepository $bookInvSuppDetRefferedBackRepo */
        $bookInvSuppDetRefferedBackRepo = App::make(BookInvSuppDetRefferedBackRepository::class);
        $theme = $this->fakeBookInvSuppDetRefferedBackData($bookInvSuppDetRefferedBackFields);
        return $bookInvSuppDetRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of BookInvSuppDetRefferedBack
     *
     * @param array $bookInvSuppDetRefferedBackFields
     * @return BookInvSuppDetRefferedBack
     */
    public function fakeBookInvSuppDetRefferedBack($bookInvSuppDetRefferedBackFields = [])
    {
        return new BookInvSuppDetRefferedBack($this->fakeBookInvSuppDetRefferedBackData($bookInvSuppDetRefferedBackFields));
    }

    /**
     * Get fake data of BookInvSuppDetRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBookInvSuppDetRefferedBackData($bookInvSuppDetRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'bookingSupInvoiceDetAutoID' => $fake->randomDigitNotNull,
            'bookingSuppMasInvAutoID' => $fake->randomDigitNotNull,
            'unbilledgrvAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'supplierID' => $fake->randomDigitNotNull,
            'purchaseOrderID' => $fake->randomDigitNotNull,
            'grvAutoID' => $fake->randomDigitNotNull,
            'grvType' => $fake->word,
            'supplierTransactionCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransactionCurrencyER' => $fake->randomDigitNotNull,
            'companyReportingCurrencyID' => $fake->randomDigitNotNull,
            'companyReportingER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'supplierInvoOrderedAmount' => $fake->randomDigitNotNull,
            'supplierInvoAmount' => $fake->randomDigitNotNull,
            'transSupplierInvoAmount' => $fake->randomDigitNotNull,
            'localSupplierInvoAmount' => $fake->randomDigitNotNull,
            'rptSupplierInvoAmount' => $fake->randomDigitNotNull,
            'totTransactionAmount' => $fake->randomDigitNotNull,
            'totLocalAmount' => $fake->randomDigitNotNull,
            'totRptAmount' => $fake->randomDigitNotNull,
            'isAddon' => $fake->randomDigitNotNull,
            'invoiceBeforeGRVYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $bookInvSuppDetRefferedBackFields);
    }
}

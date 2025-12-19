<?php

use Faker\Factory as Faker;
use App\Models\BookInvSuppDet;
use App\Repositories\BookInvSuppDetRepository;

trait MakeBookInvSuppDetTrait
{
    /**
     * Create fake instance of BookInvSuppDet and save it in database
     *
     * @param array $bookInvSuppDetFields
     * @return BookInvSuppDet
     */
    public function makeBookInvSuppDet($bookInvSuppDetFields = [])
    {
        /** @var BookInvSuppDetRepository $bookInvSuppDetRepo */
        $bookInvSuppDetRepo = App::make(BookInvSuppDetRepository::class);
        $theme = $this->fakeBookInvSuppDetData($bookInvSuppDetFields);
        return $bookInvSuppDetRepo->create($theme);
    }

    /**
     * Get fake instance of BookInvSuppDet
     *
     * @param array $bookInvSuppDetFields
     * @return BookInvSuppDet
     */
    public function fakeBookInvSuppDet($bookInvSuppDetFields = [])
    {
        return new BookInvSuppDet($this->fakeBookInvSuppDetData($bookInvSuppDetFields));
    }

    /**
     * Get fake data of BookInvSuppDet
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBookInvSuppDetData($bookInvSuppDetFields = [])
    {
        $fake = Faker::create();

        return array_merge([
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
        ], $bookInvSuppDetFields);
    }
}

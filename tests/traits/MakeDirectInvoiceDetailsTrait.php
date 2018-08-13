<?php

use Faker\Factory as Faker;
use App\Models\DirectInvoiceDetails;
use App\Repositories\DirectInvoiceDetailsRepository;

trait MakeDirectInvoiceDetailsTrait
{
    /**
     * Create fake instance of DirectInvoiceDetails and save it in database
     *
     * @param array $directInvoiceDetailsFields
     * @return DirectInvoiceDetails
     */
    public function makeDirectInvoiceDetails($directInvoiceDetailsFields = [])
    {
        /** @var DirectInvoiceDetailsRepository $directInvoiceDetailsRepo */
        $directInvoiceDetailsRepo = App::make(DirectInvoiceDetailsRepository::class);
        $theme = $this->fakeDirectInvoiceDetailsData($directInvoiceDetailsFields);
        return $directInvoiceDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of DirectInvoiceDetails
     *
     * @param array $directInvoiceDetailsFields
     * @return DirectInvoiceDetails
     */
    public function fakeDirectInvoiceDetails($directInvoiceDetailsFields = [])
    {
        return new DirectInvoiceDetails($this->fakeDirectInvoiceDetailsData($directInvoiceDetailsFields));
    }

    /**
     * Get fake data of DirectInvoiceDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDirectInvoiceDetailsData($directInvoiceDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'directInvoiceAutoID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineCode' => $fake->word,
            'glCode' => $fake->word,
            'glCodeDes' => $fake->word,
            'comments' => $fake->word,
            'percentage' => $fake->randomDigitNotNull,
            'DIAmountCurrency' => $fake->randomDigitNotNull,
            'DIAmountCurrencyER' => $fake->randomDigitNotNull,
            'DIAmount' => $fake->randomDigitNotNull,
            'localCurrency' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'comRptCurrency' => $fake->randomDigitNotNull,
            'comRptCurrencyER' => $fake->randomDigitNotNull,
            'comRptAmount' => $fake->randomDigitNotNull,
            'budgetYear' => $fake->randomDigitNotNull,
            'isExtraAddon' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $directInvoiceDetailsFields);
    }
}

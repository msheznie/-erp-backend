<?php

use Faker\Factory as Faker;
use App\Models\AdvancePaymentDetails;
use App\Repositories\AdvancePaymentDetailsRepository;

trait MakeAdvancePaymentDetailsTrait
{
    /**
     * Create fake instance of AdvancePaymentDetails and save it in database
     *
     * @param array $advancePaymentDetailsFields
     * @return AdvancePaymentDetails
     */
    public function makeAdvancePaymentDetails($advancePaymentDetailsFields = [])
    {
        /** @var AdvancePaymentDetailsRepository $advancePaymentDetailsRepo */
        $advancePaymentDetailsRepo = App::make(AdvancePaymentDetailsRepository::class);
        $theme = $this->fakeAdvancePaymentDetailsData($advancePaymentDetailsFields);
        return $advancePaymentDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of AdvancePaymentDetails
     *
     * @param array $advancePaymentDetailsFields
     * @return AdvancePaymentDetails
     */
    public function fakeAdvancePaymentDetails($advancePaymentDetailsFields = [])
    {
        return new AdvancePaymentDetails($this->fakeAdvancePaymentDetailsData($advancePaymentDetailsFields));
    }

    /**
     * Get fake data of AdvancePaymentDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAdvancePaymentDetailsData($advancePaymentDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'PayMasterAutoId' => $fake->randomDigitNotNull,
            'poAdvPaymentID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'purchaseOrderID' => $fake->randomDigitNotNull,
            'purchaseOrderCode' => $fake->word,
            'comments' => $fake->text,
            'paymentAmount' => $fake->randomDigitNotNull,
            'supplierTransCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransER' => $fake->randomDigitNotNull,
            'supplierDefaultCurrencyID' => $fake->randomDigitNotNull,
            'supplierDefaultCurrencyER' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localER' => $fake->randomDigitNotNull,
            'comRptCurrencyID' => $fake->randomDigitNotNull,
            'comRptER' => $fake->randomDigitNotNull,
            'supplierDefaultAmount' => $fake->randomDigitNotNull,
            'supplierTransAmount' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'comRptAmount' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $advancePaymentDetailsFields);
    }
}

<?php

use Faker\Factory as Faker;
use App\Models\AdvancePaymentReferback;
use App\Repositories\AdvancePaymentReferbackRepository;

trait MakeAdvancePaymentReferbackTrait
{
    /**
     * Create fake instance of AdvancePaymentReferback and save it in database
     *
     * @param array $advancePaymentReferbackFields
     * @return AdvancePaymentReferback
     */
    public function makeAdvancePaymentReferback($advancePaymentReferbackFields = [])
    {
        /** @var AdvancePaymentReferbackRepository $advancePaymentReferbackRepo */
        $advancePaymentReferbackRepo = App::make(AdvancePaymentReferbackRepository::class);
        $theme = $this->fakeAdvancePaymentReferbackData($advancePaymentReferbackFields);
        return $advancePaymentReferbackRepo->create($theme);
    }

    /**
     * Get fake instance of AdvancePaymentReferback
     *
     * @param array $advancePaymentReferbackFields
     * @return AdvancePaymentReferback
     */
    public function fakeAdvancePaymentReferback($advancePaymentReferbackFields = [])
    {
        return new AdvancePaymentReferback($this->fakeAdvancePaymentReferbackData($advancePaymentReferbackFields));
    }

    /**
     * Get fake data of AdvancePaymentReferback
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAdvancePaymentReferbackData($advancePaymentReferbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'advancePaymentDetailAutoID' => $fake->randomDigitNotNull,
            'PayMasterAutoId' => $fake->randomDigitNotNull,
            'poAdvPaymentID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
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
        ], $advancePaymentReferbackFields);
    }
}

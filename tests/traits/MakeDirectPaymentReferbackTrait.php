<?php

use Faker\Factory as Faker;
use App\Models\DirectPaymentReferback;
use App\Repositories\DirectPaymentReferbackRepository;

trait MakeDirectPaymentReferbackTrait
{
    /**
     * Create fake instance of DirectPaymentReferback and save it in database
     *
     * @param array $directPaymentReferbackFields
     * @return DirectPaymentReferback
     */
    public function makeDirectPaymentReferback($directPaymentReferbackFields = [])
    {
        /** @var DirectPaymentReferbackRepository $directPaymentReferbackRepo */
        $directPaymentReferbackRepo = App::make(DirectPaymentReferbackRepository::class);
        $theme = $this->fakeDirectPaymentReferbackData($directPaymentReferbackFields);
        return $directPaymentReferbackRepo->create($theme);
    }

    /**
     * Get fake instance of DirectPaymentReferback
     *
     * @param array $directPaymentReferbackFields
     * @return DirectPaymentReferback
     */
    public function fakeDirectPaymentReferback($directPaymentReferbackFields = [])
    {
        return new DirectPaymentReferback($this->fakeDirectPaymentReferbackData($directPaymentReferbackFields));
    }

    /**
     * Get fake data of DirectPaymentReferback
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDirectPaymentReferbackData($directPaymentReferbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'directPaymentDetailsID' => $fake->randomDigitNotNull,
            'directPaymentAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'supplierID' => $fake->randomDigitNotNull,
            'expenseClaimMasterAutoID' => $fake->randomDigitNotNull,
            'chartOfAccountSystemID' => $fake->randomDigitNotNull,
            'glCode' => $fake->word,
            'glCodeDes' => $fake->word,
            'glCodeIsBank' => $fake->randomDigitNotNull,
            'comments' => $fake->word,
            'supplierTransCurrencyID' => $fake->randomDigitNotNull,
            'supplierTransER' => $fake->randomDigitNotNull,
            'DPAmountCurrency' => $fake->randomDigitNotNull,
            'DPAmountCurrencyER' => $fake->randomDigitNotNull,
            'DPAmount' => $fake->randomDigitNotNull,
            'bankAmount' => $fake->randomDigitNotNull,
            'bankCurrencyID' => $fake->randomDigitNotNull,
            'bankCurrencyER' => $fake->randomDigitNotNull,
            'localCurrency' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'comRptCurrency' => $fake->randomDigitNotNull,
            'comRptCurrencyER' => $fake->randomDigitNotNull,
            'comRptAmount' => $fake->randomDigitNotNull,
            'budgetYear' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'relatedPartyYN' => $fake->randomDigitNotNull,
            'pettyCashYN' => $fake->randomDigitNotNull,
            'glCompanySystemID' => $fake->randomDigitNotNull,
            'glCompanyID' => $fake->word,
            'toBankID' => $fake->randomDigitNotNull,
            'toBankAccountID' => $fake->randomDigitNotNull,
            'toBankCurrencyID' => $fake->randomDigitNotNull,
            'toBankCurrencyER' => $fake->randomDigitNotNull,
            'toBankAmount' => $fake->randomDigitNotNull,
            'toBankGlCodeSystemID' => $fake->randomDigitNotNull,
            'toBankGlCode' => $fake->word,
            'toBankGLDescription' => $fake->word,
            'toCompanyLocalCurrencyID' => $fake->randomDigitNotNull,
            'toCompanyLocalCurrencyER' => $fake->randomDigitNotNull,
            'toCompanyLocalCurrencyAmount' => $fake->randomDigitNotNull,
            'toCompanyRptCurrencyID' => $fake->randomDigitNotNull,
            'toCompanyRptCurrencyER' => $fake->randomDigitNotNull,
            'toCompanyRptCurrencyAmount' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $directPaymentReferbackFields);
    }
}

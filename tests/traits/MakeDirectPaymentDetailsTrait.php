<?php

use Faker\Factory as Faker;
use App\Models\DirectPaymentDetails;
use App\Repositories\DirectPaymentDetailsRepository;

trait MakeDirectPaymentDetailsTrait
{
    /**
     * Create fake instance of DirectPaymentDetails and save it in database
     *
     * @param array $directPaymentDetailsFields
     * @return DirectPaymentDetails
     */
    public function makeDirectPaymentDetails($directPaymentDetailsFields = [])
    {
        /** @var DirectPaymentDetailsRepository $directPaymentDetailsRepo */
        $directPaymentDetailsRepo = App::make(DirectPaymentDetailsRepository::class);
        $theme = $this->fakeDirectPaymentDetailsData($directPaymentDetailsFields);
        return $directPaymentDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of DirectPaymentDetails
     *
     * @param array $directPaymentDetailsFields
     * @return DirectPaymentDetails
     */
    public function fakeDirectPaymentDetails($directPaymentDetailsFields = [])
    {
        return new DirectPaymentDetails($this->fakeDirectPaymentDetailsData($directPaymentDetailsFields));
    }

    /**
     * Get fake data of DirectPaymentDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDirectPaymentDetailsData($directPaymentDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'directPaymentAutoID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineCode' => $fake->word,
            'supplierID' => $fake->randomDigitNotNull,
            'expenseClaimMasterAutoID' => $fake->randomDigitNotNull,
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
            'toBankGlCode' => $fake->word,
            'toBankGLDescription' => $fake->word,
            'toCompanyLocalCurrencyID' => $fake->randomDigitNotNull,
            'toCompanyLocalCurrencyER' => $fake->randomDigitNotNull,
            'toCompanyLocalCurrencyAmount' => $fake->randomDigitNotNull,
            'toCompanyRptCurrencyID' => $fake->randomDigitNotNull,
            'toCompanyRptCurrencyER' => $fake->randomDigitNotNull,
            'toCompanyRptCurrencyAmount' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $directPaymentDetailsFields);
    }
}

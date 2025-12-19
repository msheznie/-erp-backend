<?php

use Faker\Factory as Faker;
use App\Models\PoPaymentTermsRefferedback;
use App\Repositories\PoPaymentTermsRefferedbackRepository;

trait MakePoPaymentTermsRefferedbackTrait
{
    /**
     * Create fake instance of PoPaymentTermsRefferedback and save it in database
     *
     * @param array $poPaymentTermsRefferedbackFields
     * @return PoPaymentTermsRefferedback
     */
    public function makePoPaymentTermsRefferedback($poPaymentTermsRefferedbackFields = [])
    {
        /** @var PoPaymentTermsRefferedbackRepository $poPaymentTermsRefferedbackRepo */
        $poPaymentTermsRefferedbackRepo = App::make(PoPaymentTermsRefferedbackRepository::class);
        $theme = $this->fakePoPaymentTermsRefferedbackData($poPaymentTermsRefferedbackFields);
        return $poPaymentTermsRefferedbackRepo->create($theme);
    }

    /**
     * Get fake instance of PoPaymentTermsRefferedback
     *
     * @param array $poPaymentTermsRefferedbackFields
     * @return PoPaymentTermsRefferedback
     */
    public function fakePoPaymentTermsRefferedback($poPaymentTermsRefferedbackFields = [])
    {
        return new PoPaymentTermsRefferedback($this->fakePoPaymentTermsRefferedbackData($poPaymentTermsRefferedbackFields));
    }

    /**
     * Get fake data of PoPaymentTermsRefferedback
     *
     * @param array $postFields
     * @return array
     */
    public function fakePoPaymentTermsRefferedbackData($poPaymentTermsRefferedbackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'paymentTermID' => $fake->randomDigitNotNull,
            'paymentTermsCategory' => $fake->randomDigitNotNull,
            'poID' => $fake->randomDigitNotNull,
            'paymentTemDes' => $fake->word,
            'comAmount' => $fake->randomDigitNotNull,
            'comPercentage' => $fake->randomDigitNotNull,
            'inDays' => $fake->randomDigitNotNull,
            'comDate' => $fake->date('Y-m-d H:i:s'),
            'LCPaymentYN' => $fake->randomDigitNotNull,
            'isRequested' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $poPaymentTermsRefferedbackFields);
    }
}

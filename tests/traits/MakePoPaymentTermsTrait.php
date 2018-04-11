<?php

use Faker\Factory as Faker;
use App\Models\PoPaymentTerms;
use App\Repositories\PoPaymentTermsRepository;

trait MakePoPaymentTermsTrait
{
    /**
     * Create fake instance of PoPaymentTerms and save it in database
     *
     * @param array $poPaymentTermsFields
     * @return PoPaymentTerms
     */
    public function makePoPaymentTerms($poPaymentTermsFields = [])
    {
        /** @var PoPaymentTermsRepository $poPaymentTermsRepo */
        $poPaymentTermsRepo = App::make(PoPaymentTermsRepository::class);
        $theme = $this->fakePoPaymentTermsData($poPaymentTermsFields);
        return $poPaymentTermsRepo->create($theme);
    }

    /**
     * Get fake instance of PoPaymentTerms
     *
     * @param array $poPaymentTermsFields
     * @return PoPaymentTerms
     */
    public function fakePoPaymentTerms($poPaymentTermsFields = [])
    {
        return new PoPaymentTerms($this->fakePoPaymentTermsData($poPaymentTermsFields));
    }

    /**
     * Get fake data of PoPaymentTerms
     *
     * @param array $postFields
     * @return array
     */
    public function fakePoPaymentTermsData($poPaymentTermsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'paymentTermsCategory' => $fake->randomDigitNotNull,
            'poID' => $fake->randomDigitNotNull,
            'paymentTemDes' => $fake->word,
            'comAmount' => $fake->randomDigitNotNull,
            'comPercentage' => $fake->randomDigitNotNull,
            'inDays' => $fake->randomDigitNotNull,
            'comDate' => $fake->date('Y-m-d H:i:s'),
            'LCPaymentYN' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $poPaymentTermsFields);
    }
}

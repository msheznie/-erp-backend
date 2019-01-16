<?php

use Faker\Factory as Faker;
use App\Models\CurrencyDenomination;
use App\Repositories\CurrencyDenominationRepository;

trait MakeCurrencyDenominationTrait
{
    /**
     * Create fake instance of CurrencyDenomination and save it in database
     *
     * @param array $currencyDenominationFields
     * @return CurrencyDenomination
     */
    public function makeCurrencyDenomination($currencyDenominationFields = [])
    {
        /** @var CurrencyDenominationRepository $currencyDenominationRepo */
        $currencyDenominationRepo = App::make(CurrencyDenominationRepository::class);
        $theme = $this->fakeCurrencyDenominationData($currencyDenominationFields);
        return $currencyDenominationRepo->create($theme);
    }

    /**
     * Get fake instance of CurrencyDenomination
     *
     * @param array $currencyDenominationFields
     * @return CurrencyDenomination
     */
    public function fakeCurrencyDenomination($currencyDenominationFields = [])
    {
        return new CurrencyDenomination($this->fakeCurrencyDenominationData($currencyDenominationFields));
    }

    /**
     * Get fake data of CurrencyDenomination
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCurrencyDenominationData($currencyDenominationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'currencyID' => $fake->randomDigitNotNull,
            'currencyCode' => $fake->word,
            'amount' => $fake->randomDigitNotNull,
            'value' => $fake->randomDigitNotNull,
            'isNote' => $fake->word,
            'caption' => $fake->word
        ], $currencyDenominationFields);
    }
}

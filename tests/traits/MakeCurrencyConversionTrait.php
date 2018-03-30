<?php

use Faker\Factory as Faker;
use App\Models\CurrencyConversion;
use App\Repositories\CurrencyConversionRepository;

trait MakeCurrencyConversionTrait
{
    /**
     * Create fake instance of CurrencyConversion and save it in database
     *
     * @param array $currencyConversionFields
     * @return CurrencyConversion
     */
    public function makeCurrencyConversion($currencyConversionFields = [])
    {
        /** @var CurrencyConversionRepository $currencyConversionRepo */
        $currencyConversionRepo = App::make(CurrencyConversionRepository::class);
        $theme = $this->fakeCurrencyConversionData($currencyConversionFields);
        return $currencyConversionRepo->create($theme);
    }

    /**
     * Get fake instance of CurrencyConversion
     *
     * @param array $currencyConversionFields
     * @return CurrencyConversion
     */
    public function fakeCurrencyConversion($currencyConversionFields = [])
    {
        return new CurrencyConversion($this->fakeCurrencyConversionData($currencyConversionFields));
    }

    /**
     * Get fake data of CurrencyConversion
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCurrencyConversionData($currencyConversionFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'masterCurrencyID' => $fake->randomDigitNotNull,
            'subCurrencyID' => $fake->randomDigitNotNull,
            'conversion' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $currencyConversionFields);
    }
}

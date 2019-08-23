<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\CurrencyConversionHistory;
use App\Repositories\CurrencyConversionHistoryRepository;

trait MakeCurrencyConversionHistoryTrait
{
    /**
     * Create fake instance of CurrencyConversionHistory and save it in database
     *
     * @param array $currencyConversionHistoryFields
     * @return CurrencyConversionHistory
     */
    public function makeCurrencyConversionHistory($currencyConversionHistoryFields = [])
    {
        /** @var CurrencyConversionHistoryRepository $currencyConversionHistoryRepo */
        $currencyConversionHistoryRepo = \App::make(CurrencyConversionHistoryRepository::class);
        $theme = $this->fakeCurrencyConversionHistoryData($currencyConversionHistoryFields);
        return $currencyConversionHistoryRepo->create($theme);
    }

    /**
     * Get fake instance of CurrencyConversionHistory
     *
     * @param array $currencyConversionHistoryFields
     * @return CurrencyConversionHistory
     */
    public function fakeCurrencyConversionHistory($currencyConversionHistoryFields = [])
    {
        return new CurrencyConversionHistory($this->fakeCurrencyConversionHistoryData($currencyConversionHistoryFields));
    }

    /**
     * Get fake data of CurrencyConversionHistory
     *
     * @param array $currencyConversionHistoryFields
     * @return array
     */
    public function fakeCurrencyConversionHistoryData($currencyConversionHistoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'serialNo' => $fake->randomDigitNotNull,
            'masterCurrencyID' => $fake->randomDigitNotNull,
            'subCurrencyID' => $fake->randomDigitNotNull,
            'conversion' => $fake->randomDigitNotNull,
            'createdBy' => $fake->word,
            'createdUserID' => $fake->randomDigitNotNull,
            'createdpc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $currencyConversionHistoryFields);
    }
}

<?php

use Faker\Factory as Faker;
use App\Models\CurrencyMaster;
use App\Repositories\CurrencyMasterRepository;

trait MakeCurrencyMasterTrait
{
    /**
     * Create fake instance of CurrencyMaster and save it in database
     *
     * @param array $currencyMasterFields
     * @return CurrencyMaster
     */
    public function makeCurrencyMaster($currencyMasterFields = [])
    {
        /** @var CurrencyMasterRepository $currencyMasterRepo */
        $currencyMasterRepo = App::make(CurrencyMasterRepository::class);
        $theme = $this->fakeCurrencyMasterData($currencyMasterFields);
        return $currencyMasterRepo->create($theme);
    }

    /**
     * Get fake instance of CurrencyMaster
     *
     * @param array $currencyMasterFields
     * @return CurrencyMaster
     */
    public function fakeCurrencyMaster($currencyMasterFields = [])
    {
        return new CurrencyMaster($this->fakeCurrencyMasterData($currencyMasterFields));
    }

    /**
     * Get fake data of CurrencyMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCurrencyMasterData($currencyMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'CurrencyName' => $fake->word,
            'CurrencyCode' => $fake->word,
            'DecimalPlaces' => $fake->randomDigitNotNull,
            'ExchangeRate' => $fake->randomDigitNotNull,
            'isLocal' => $fake->randomDigitNotNull,
            'DateModified' => $fake->date('Y-m-d H:i:s'),
            'ModifiedBy' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $currencyMasterFields);
    }
}

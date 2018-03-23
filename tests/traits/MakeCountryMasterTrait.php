<?php

use Faker\Factory as Faker;
use App\Models\CountryMaster;
use App\Repositories\CountryMasterRepository;

trait MakeCountryMasterTrait
{
    /**
     * Create fake instance of CountryMaster and save it in database
     *
     * @param array $countryMasterFields
     * @return CountryMaster
     */
    public function makeCountryMaster($countryMasterFields = [])
    {
        /** @var CountryMasterRepository $countryMasterRepo */
        $countryMasterRepo = App::make(CountryMasterRepository::class);
        $theme = $this->fakeCountryMasterData($countryMasterFields);
        return $countryMasterRepo->create($theme);
    }

    /**
     * Get fake instance of CountryMaster
     *
     * @param array $countryMasterFields
     * @return CountryMaster
     */
    public function fakeCountryMaster($countryMasterFields = [])
    {
        return new CountryMaster($this->fakeCountryMasterData($countryMasterFields));
    }

    /**
     * Get fake data of CountryMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCountryMasterData($countryMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'countryCode' => $fake->word,
            'countryName' => $fake->word,
            'countryName_O' => $fake->word,
            'nationality' => $fake->word,
            'isLocal' => $fake->randomDigitNotNull,
            'countryFlag' => $fake->word
        ], $countryMasterFields);
    }
}

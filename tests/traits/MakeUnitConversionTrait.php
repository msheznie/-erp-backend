<?php

use Faker\Factory as Faker;
use App\Models\UnitConversion;
use App\Repositories\UnitConversionRepository;

trait MakeUnitConversionTrait
{
    /**
     * Create fake instance of UnitConversion and save it in database
     *
     * @param array $unitConversionFields
     * @return UnitConversion
     */
    public function makeUnitConversion($unitConversionFields = [])
    {
        /** @var UnitConversionRepository $unitConversionRepo */
        $unitConversionRepo = App::make(UnitConversionRepository::class);
        $theme = $this->fakeUnitConversionData($unitConversionFields);
        return $unitConversionRepo->create($theme);
    }

    /**
     * Get fake instance of UnitConversion
     *
     * @param array $unitConversionFields
     * @return UnitConversion
     */
    public function fakeUnitConversion($unitConversionFields = [])
    {
        return new UnitConversion($this->fakeUnitConversionData($unitConversionFields));
    }

    /**
     * Get fake data of UnitConversion
     *
     * @param array $postFields
     * @return array
     */
    public function fakeUnitConversionData($unitConversionFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'masterUnitID' => $fake->randomDigitNotNull,
            'subUnitID' => $fake->randomDigitNotNull,
            'conversion' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $unitConversionFields);
    }
}

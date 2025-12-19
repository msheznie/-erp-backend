<?php

use Faker\Factory as Faker;
use App\Models\ErpLocation;
use App\Repositories\ErpLocationRepository;

trait MakeErpLocationTrait
{
    /**
     * Create fake instance of ErpLocation and save it in database
     *
     * @param array $erpLocationFields
     * @return ErpLocation
     */
    public function makeErpLocation($erpLocationFields = [])
    {
        /** @var ErpLocationRepository $erpLocationRepo */
        $erpLocationRepo = App::make(ErpLocationRepository::class);
        $theme = $this->fakeErpLocationData($erpLocationFields);
        return $erpLocationRepo->create($theme);
    }

    /**
     * Get fake instance of ErpLocation
     *
     * @param array $erpLocationFields
     * @return ErpLocation
     */
    public function fakeErpLocation($erpLocationFields = [])
    {
        return new ErpLocation($this->fakeErpLocationData($erpLocationFields));
    }

    /**
     * Get fake data of ErpLocation
     *
     * @param array $postFields
     * @return array
     */
    public function fakeErpLocationData($erpLocationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'locationName' => $fake->word
        ], $erpLocationFields);
    }
}

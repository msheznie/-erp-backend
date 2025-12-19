<?php

use Faker\Factory as Faker;
use App\Models\WarehouseBinLocation;
use App\Repositories\WarehouseBinLocationRepository;

trait MakeWarehouseBinLocationTrait
{
    /**
     * Create fake instance of WarehouseBinLocation and save it in database
     *
     * @param array $warehouseBinLocationFields
     * @return WarehouseBinLocation
     */
    public function makeWarehouseBinLocation($warehouseBinLocationFields = [])
    {
        /** @var WarehouseBinLocationRepository $warehouseBinLocationRepo */
        $warehouseBinLocationRepo = App::make(WarehouseBinLocationRepository::class);
        $theme = $this->fakeWarehouseBinLocationData($warehouseBinLocationFields);
        return $warehouseBinLocationRepo->create($theme);
    }

    /**
     * Get fake instance of WarehouseBinLocation
     *
     * @param array $warehouseBinLocationFields
     * @return WarehouseBinLocation
     */
    public function fakeWarehouseBinLocation($warehouseBinLocationFields = [])
    {
        return new WarehouseBinLocation($this->fakeWarehouseBinLocationData($warehouseBinLocationFields));
    }

    /**
     * Get fake data of WarehouseBinLocation
     *
     * @param array $postFields
     * @return array
     */
    public function fakeWarehouseBinLocationData($warehouseBinLocationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'binLocationDes' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'wareHouseSystemCode' => $fake->randomDigitNotNull,
            'createdBy' => $fake->word,
            'dateCreated' => $fake->date('Y-m-d H:i:s'),
            'isActive' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $warehouseBinLocationFields);
    }
}

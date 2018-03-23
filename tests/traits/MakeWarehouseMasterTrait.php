<?php

use Faker\Factory as Faker;
use App\Models\WarehouseMaster;
use App\Repositories\WarehouseMasterRepository;

trait MakeWarehouseMasterTrait
{
    /**
     * Create fake instance of WarehouseMaster and save it in database
     *
     * @param array $warehouseMasterFields
     * @return WarehouseMaster
     */
    public function makeWarehouseMaster($warehouseMasterFields = [])
    {
        /** @var WarehouseMasterRepository $warehouseMasterRepo */
        $warehouseMasterRepo = App::make(WarehouseMasterRepository::class);
        $theme = $this->fakeWarehouseMasterData($warehouseMasterFields);
        return $warehouseMasterRepo->create($theme);
    }

    /**
     * Get fake instance of WarehouseMaster
     *
     * @param array $warehouseMasterFields
     * @return WarehouseMaster
     */
    public function fakeWarehouseMaster($warehouseMasterFields = [])
    {
        return new WarehouseMaster($this->fakeWarehouseMasterData($warehouseMasterFields));
    }

    /**
     * Get fake data of WarehouseMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeWarehouseMasterData($warehouseMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'wareHouseCode' => $fake->word,
            'wareHouseDescription' => $fake->word,
            'wareHouseLocation' => $fake->randomDigitNotNull,
            'isActive' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $warehouseMasterFields);
    }
}

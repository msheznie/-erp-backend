<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\WarehouseRights;
use App\Repositories\WarehouseRightsRepository;

trait MakeWarehouseRightsTrait
{
    /**
     * Create fake instance of WarehouseRights and save it in database
     *
     * @param array $warehouseRightsFields
     * @return WarehouseRights
     */
    public function makeWarehouseRights($warehouseRightsFields = [])
    {
        /** @var WarehouseRightsRepository $warehouseRightsRepo */
        $warehouseRightsRepo = \App::make(WarehouseRightsRepository::class);
        $theme = $this->fakeWarehouseRightsData($warehouseRightsFields);
        return $warehouseRightsRepo->create($theme);
    }

    /**
     * Get fake instance of WarehouseRights
     *
     * @param array $warehouseRightsFields
     * @return WarehouseRights
     */
    public function fakeWarehouseRights($warehouseRightsFields = [])
    {
        return new WarehouseRights($this->fakeWarehouseRightsData($warehouseRightsFields));
    }

    /**
     * Get fake data of WarehouseRights
     *
     * @param array $warehouseRightsFields
     * @return array
     */
    public function fakeWarehouseRightsData($warehouseRightsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'modifiedDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdPcID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'wareHouseSystemCode' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'employeeSystemID' => $fake->randomDigitNotNull,
            'companyrightsID' => $fake->randomDigitNotNull
        ], $warehouseRightsFields);
    }
}

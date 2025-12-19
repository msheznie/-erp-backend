<?php

use Faker\Factory as Faker;
use App\Models\WarehouseItems;
use App\Repositories\WarehouseItemsRepository;

trait MakeWarehouseItemsTrait
{
    /**
     * Create fake instance of WarehouseItems and save it in database
     *
     * @param array $warehouseItemsFields
     * @return WarehouseItems
     */
    public function makeWarehouseItems($warehouseItemsFields = [])
    {
        /** @var WarehouseItemsRepository $warehouseItemsRepo */
        $warehouseItemsRepo = App::make(WarehouseItemsRepository::class);
        $theme = $this->fakeWarehouseItemsData($warehouseItemsFields);
        return $warehouseItemsRepo->create($theme);
    }

    /**
     * Get fake instance of WarehouseItems
     *
     * @param array $warehouseItemsFields
     * @return WarehouseItems
     */
    public function fakeWarehouseItems($warehouseItemsFields = [])
    {
        return new WarehouseItems($this->fakeWarehouseItemsData($warehouseItemsFields));
    }

    /**
     * Get fake data of WarehouseItems
     *
     * @param array $postFields
     * @return array
     */
    public function fakeWarehouseItemsData($warehouseItemsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'warehouseSystemCode' => $fake->randomDigitNotNull,
            'itemSystemCode' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'unitOfMeasure' => $fake->randomDigitNotNull,
            'stockQty' => $fake->randomDigitNotNull,
            'maximunQty' => $fake->randomDigitNotNull,
            'minimumQty' => $fake->randomDigitNotNull,
            'rolQuantity' => $fake->randomDigitNotNull,
            'wacValueLocalCurrencyID' => $fake->randomDigitNotNull,
            'wacValueLocal' => $fake->randomDigitNotNull,
            'wacValueReportingCurrencyID' => $fake->randomDigitNotNull,
            'wacValueReporting' => $fake->randomDigitNotNull,
            'totalQty' => $fake->randomDigitNotNull,
            'totalValueLocal' => $fake->randomDigitNotNull,
            'totalValueRpt' => $fake->randomDigitNotNull,
            'financeCategoryMaster' => $fake->randomDigitNotNull,
            'financeCategorySub' => $fake->randomDigitNotNull,
            'binNumber' => $fake->randomDigitNotNull,
            'toDelete' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $warehouseItemsFields);
    }
}

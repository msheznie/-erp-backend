<?php

use Faker\Factory as Faker;
use App\Models\InventoryReclassificationDetail;
use App\Repositories\InventoryReclassificationDetailRepository;

trait MakeInventoryReclassificationDetailTrait
{
    /**
     * Create fake instance of InventoryReclassificationDetail and save it in database
     *
     * @param array $inventoryReclassificationDetailFields
     * @return InventoryReclassificationDetail
     */
    public function makeInventoryReclassificationDetail($inventoryReclassificationDetailFields = [])
    {
        /** @var InventoryReclassificationDetailRepository $inventoryReclassificationDetailRepo */
        $inventoryReclassificationDetailRepo = App::make(InventoryReclassificationDetailRepository::class);
        $theme = $this->fakeInventoryReclassificationDetailData($inventoryReclassificationDetailFields);
        return $inventoryReclassificationDetailRepo->create($theme);
    }

    /**
     * Get fake instance of InventoryReclassificationDetail
     *
     * @param array $inventoryReclassificationDetailFields
     * @return InventoryReclassificationDetail
     */
    public function fakeInventoryReclassificationDetail($inventoryReclassificationDetailFields = [])
    {
        return new InventoryReclassificationDetail($this->fakeInventoryReclassificationDetailData($inventoryReclassificationDetailFields));
    }

    /**
     * Get fake data of InventoryReclassificationDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeInventoryReclassificationDetailData($inventoryReclassificationDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'inventoryreclassificationID' => $fake->randomDigitNotNull,
            'itemSystemCode' => $fake->randomDigitNotNull,
            'itemPrimaryCode' => $fake->word,
            'itemDescription' => $fake->text,
            'unitOfMeasure' => $fake->randomDigitNotNull,
            'itemFinanceCategoryID' => $fake->randomDigitNotNull,
            'itemFinanceCategorySubID' => $fake->randomDigitNotNull,
            'financeGLcodebBSSystemID' => $fake->randomDigitNotNull,
            'financeGLcodebBS' => $fake->word,
            'financeGLcodePLSystemID' => $fake->randomDigitNotNull,
            'financeGLcodePL' => $fake->word,
            'includePLForGRVYN' => $fake->randomDigitNotNull,
            'currentStockQty' => $fake->randomDigitNotNull,
            'unitCostLocal' => $fake->randomDigitNotNull,
            'unitCostRpt' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $inventoryReclassificationDetailFields);
    }
}

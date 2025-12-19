<?php

use Faker\Factory as Faker;
use App\Models\PurchaseOrderStatus;
use App\Repositories\PurchaseOrderStatusRepository;

trait MakePurchaseOrderStatusTrait
{
    /**
     * Create fake instance of PurchaseOrderStatus and save it in database
     *
     * @param array $purchaseOrderStatusFields
     * @return PurchaseOrderStatus
     */
    public function makePurchaseOrderStatus($purchaseOrderStatusFields = [])
    {
        /** @var PurchaseOrderStatusRepository $purchaseOrderStatusRepo */
        $purchaseOrderStatusRepo = App::make(PurchaseOrderStatusRepository::class);
        $theme = $this->fakePurchaseOrderStatusData($purchaseOrderStatusFields);
        return $purchaseOrderStatusRepo->create($theme);
    }

    /**
     * Get fake instance of PurchaseOrderStatus
     *
     * @param array $purchaseOrderStatusFields
     * @return PurchaseOrderStatus
     */
    public function fakePurchaseOrderStatus($purchaseOrderStatusFields = [])
    {
        return new PurchaseOrderStatus($this->fakePurchaseOrderStatusData($purchaseOrderStatusFields));
    }

    /**
     * Get fake data of PurchaseOrderStatus
     *
     * @param array $postFields
     * @return array
     */
    public function fakePurchaseOrderStatusData($purchaseOrderStatusFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'purchaseOrderID' => $fake->randomDigitNotNull,
            'purchaseOrderCode' => $fake->word,
            'POCategoryID' => $fake->randomDigitNotNull,
            'comments' => $fake->word,
            'updatedByEmpSystemID' => $fake->randomDigitNotNull,
            'updatedByEmpID' => $fake->randomDigitNotNull,
            'updatedByEmpName' => $fake->word,
            'updatedDate' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $purchaseOrderStatusFields);
    }
}

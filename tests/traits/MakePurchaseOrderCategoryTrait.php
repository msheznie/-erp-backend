<?php

use Faker\Factory as Faker;
use App\Models\PurchaseOrderCategory;
use App\Repositories\PurchaseOrderCategoryRepository;

trait MakePurchaseOrderCategoryTrait
{
    /**
     * Create fake instance of PurchaseOrderCategory and save it in database
     *
     * @param array $purchaseOrderCategoryFields
     * @return PurchaseOrderCategory
     */
    public function makePurchaseOrderCategory($purchaseOrderCategoryFields = [])
    {
        /** @var PurchaseOrderCategoryRepository $purchaseOrderCategoryRepo */
        $purchaseOrderCategoryRepo = App::make(PurchaseOrderCategoryRepository::class);
        $theme = $this->fakePurchaseOrderCategoryData($purchaseOrderCategoryFields);
        return $purchaseOrderCategoryRepo->create($theme);
    }

    /**
     * Get fake instance of PurchaseOrderCategory
     *
     * @param array $purchaseOrderCategoryFields
     * @return PurchaseOrderCategory
     */
    public function fakePurchaseOrderCategory($purchaseOrderCategoryFields = [])
    {
        return new PurchaseOrderCategory($this->fakePurchaseOrderCategoryData($purchaseOrderCategoryFields));
    }

    /**
     * Get fake data of PurchaseOrderCategory
     *
     * @param array $postFields
     * @return array
     */
    public function fakePurchaseOrderCategoryData($purchaseOrderCategoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'description' => $fake->word
        ], $purchaseOrderCategoryFields);
    }
}

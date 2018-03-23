<?php

use Faker\Factory as Faker;
use App\Models\SupplierSubCategoryAssign;
use App\Repositories\SupplierSubCategoryAssignRepository;

trait MakeSupplierSubCategoryAssignTrait
{
    /**
     * Create fake instance of SupplierSubCategoryAssign and save it in database
     *
     * @param array $supplierSubCategoryAssignFields
     * @return SupplierSubCategoryAssign
     */
    public function makeSupplierSubCategoryAssign($supplierSubCategoryAssignFields = [])
    {
        /** @var SupplierSubCategoryAssignRepository $supplierSubCategoryAssignRepo */
        $supplierSubCategoryAssignRepo = App::make(SupplierSubCategoryAssignRepository::class);
        $theme = $this->fakeSupplierSubCategoryAssignData($supplierSubCategoryAssignFields);
        return $supplierSubCategoryAssignRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierSubCategoryAssign
     *
     * @param array $supplierSubCategoryAssignFields
     * @return SupplierSubCategoryAssign
     */
    public function fakeSupplierSubCategoryAssign($supplierSubCategoryAssignFields = [])
    {
        return new SupplierSubCategoryAssign($this->fakeSupplierSubCategoryAssignData($supplierSubCategoryAssignFields));
    }

    /**
     * Get fake data of SupplierSubCategoryAssign
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupplierSubCategoryAssignData($supplierSubCategoryAssignFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'supplierID' => $fake->randomDigitNotNull,
            'supSubCategoryID' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $supplierSubCategoryAssignFields);
    }
}

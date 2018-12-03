<?php

use Faker\Factory as Faker;
use App\Models\SupplierCategoryICVSub;
use App\Repositories\SupplierCategoryICVSubRepository;

trait MakeSupplierCategoryICVSubTrait
{
    /**
     * Create fake instance of SupplierCategoryICVSub and save it in database
     *
     * @param array $supplierCategoryICVSubFields
     * @return SupplierCategoryICVSub
     */
    public function makeSupplierCategoryICVSub($supplierCategoryICVSubFields = [])
    {
        /** @var SupplierCategoryICVSubRepository $supplierCategoryICVSubRepo */
        $supplierCategoryICVSubRepo = App::make(SupplierCategoryICVSubRepository::class);
        $theme = $this->fakeSupplierCategoryICVSubData($supplierCategoryICVSubFields);
        return $supplierCategoryICVSubRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierCategoryICVSub
     *
     * @param array $supplierCategoryICVSubFields
     * @return SupplierCategoryICVSub
     */
    public function fakeSupplierCategoryICVSub($supplierCategoryICVSubFields = [])
    {
        return new SupplierCategoryICVSub($this->fakeSupplierCategoryICVSubData($supplierCategoryICVSubFields));
    }

    /**
     * Get fake data of SupplierCategoryICVSub
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupplierCategoryICVSubData($supplierCategoryICVSubFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'supCategoryICVMasterID' => $fake->randomDigitNotNull,
            'subCategoryCode' => $fake->word,
            'categoryDescription' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'createdDateTime' => $fake->date('Y-m-d H:i:s')
        ], $supplierCategoryICVSubFields);
    }
}

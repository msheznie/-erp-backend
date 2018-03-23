<?php

use Faker\Factory as Faker;
use App\Models\SupplierCategorySub;
use App\Repositories\SupplierCategorySubRepository;

trait MakeSupplierCategorySubTrait
{
    /**
     * Create fake instance of SupplierCategorySub and save it in database
     *
     * @param array $supplierCategorySubFields
     * @return SupplierCategorySub
     */
    public function makeSupplierCategorySub($supplierCategorySubFields = [])
    {
        /** @var SupplierCategorySubRepository $supplierCategorySubRepo */
        $supplierCategorySubRepo = App::make(SupplierCategorySubRepository::class);
        $theme = $this->fakeSupplierCategorySubData($supplierCategorySubFields);
        return $supplierCategorySubRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierCategorySub
     *
     * @param array $supplierCategorySubFields
     * @return SupplierCategorySub
     */
    public function fakeSupplierCategorySub($supplierCategorySubFields = [])
    {
        return new SupplierCategorySub($this->fakeSupplierCategorySubData($supplierCategorySubFields));
    }

    /**
     * Get fake data of SupplierCategorySub
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupplierCategorySubData($supplierCategorySubFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'supMasterCategoryID' => $fake->randomDigitNotNull,
            'subCategoryCode' => $fake->word,
            'categoryDescription' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s')
        ], $supplierCategorySubFields);
    }
}

<?php

use Faker\Factory as Faker;
use App\Models\SupplierCategoryMaster;
use App\Repositories\SupplierCategoryMasterRepository;

trait MakeSupplierCategoryMasterTrait
{
    /**
     * Create fake instance of SupplierCategoryMaster and save it in database
     *
     * @param array $supplierCategoryMasterFields
     * @return SupplierCategoryMaster
     */
    public function makeSupplierCategoryMaster($supplierCategoryMasterFields = [])
    {
        /** @var SupplierCategoryMasterRepository $supplierCategoryMasterRepo */
        $supplierCategoryMasterRepo = App::make(SupplierCategoryMasterRepository::class);
        $theme = $this->fakeSupplierCategoryMasterData($supplierCategoryMasterFields);
        return $supplierCategoryMasterRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierCategoryMaster
     *
     * @param array $supplierCategoryMasterFields
     * @return SupplierCategoryMaster
     */
    public function fakeSupplierCategoryMaster($supplierCategoryMasterFields = [])
    {
        return new SupplierCategoryMaster($this->fakeSupplierCategoryMasterData($supplierCategoryMasterFields));
    }

    /**
     * Get fake data of SupplierCategoryMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupplierCategoryMasterData($supplierCategoryMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'categoryCode' => $fake->word,
            'categoryDescription' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $supplierCategoryMasterFields);
    }
}

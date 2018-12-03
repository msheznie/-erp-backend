<?php

use Faker\Factory as Faker;
use App\Models\SupplierCategoryICVMaster;
use App\Repositories\SupplierCategoryICVMasterRepository;

trait MakeSupplierCategoryICVMasterTrait
{
    /**
     * Create fake instance of SupplierCategoryICVMaster and save it in database
     *
     * @param array $supplierCategoryICVMasterFields
     * @return SupplierCategoryICVMaster
     */
    public function makeSupplierCategoryICVMaster($supplierCategoryICVMasterFields = [])
    {
        /** @var SupplierCategoryICVMasterRepository $supplierCategoryICVMasterRepo */
        $supplierCategoryICVMasterRepo = App::make(SupplierCategoryICVMasterRepository::class);
        $theme = $this->fakeSupplierCategoryICVMasterData($supplierCategoryICVMasterFields);
        return $supplierCategoryICVMasterRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierCategoryICVMaster
     *
     * @param array $supplierCategoryICVMasterFields
     * @return SupplierCategoryICVMaster
     */
    public function fakeSupplierCategoryICVMaster($supplierCategoryICVMasterFields = [])
    {
        return new SupplierCategoryICVMaster($this->fakeSupplierCategoryICVMasterData($supplierCategoryICVMasterFields));
    }

    /**
     * Get fake data of SupplierCategoryICVMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupplierCategoryICVMasterData($supplierCategoryICVMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'categoryCode' => $fake->word,
            'categoryDescription' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $supplierCategoryICVMasterFields);
    }
}

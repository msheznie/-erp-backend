<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\SupplierCatalogMaster;
use App\Repositories\SupplierCatalogMasterRepository;

trait MakeSupplierCatalogMasterTrait
{
    /**
     * Create fake instance of SupplierCatalogMaster and save it in database
     *
     * @param array $supplierCatalogMasterFields
     * @return SupplierCatalogMaster
     */
    public function makeSupplierCatalogMaster($supplierCatalogMasterFields = [])
    {
        /** @var SupplierCatalogMasterRepository $supplierCatalogMasterRepo */
        $supplierCatalogMasterRepo = \App::make(SupplierCatalogMasterRepository::class);
        $theme = $this->fakeSupplierCatalogMasterData($supplierCatalogMasterFields);
        return $supplierCatalogMasterRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierCatalogMaster
     *
     * @param array $supplierCatalogMasterFields
     * @return SupplierCatalogMaster
     */
    public function fakeSupplierCatalogMaster($supplierCatalogMasterFields = [])
    {
        return new SupplierCatalogMaster($this->fakeSupplierCatalogMasterData($supplierCatalogMasterFields));
    }

    /**
     * Get fake data of SupplierCatalogMaster
     *
     * @param array $supplierCatalogMasterFields
     * @return array
     */
    public function fakeSupplierCatalogMasterData($supplierCatalogMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'catalogID' => $fake->word,
            'catalogName' => $fake->word,
            'fromDate' => $fake->date('Y-m-d H:i:s'),
            'toDate' => $fake->date('Y-m-d H:i:s'),
            'supplierID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'documentSystemID' => $fake->randomDigitNotNull,
            'createdBy' => $fake->randomDigitNotNull,
            'createdDate' => $fake->date('Y-m-d H:i:s'),
            'modifiedBy' => $fake->word,
            'modifiedDate' => $fake->date('Y-m-d H:i:s'),
            'isDelete' => $fake->word,
            'isActive' => $fake->word
        ], $supplierCatalogMasterFields);
    }
}

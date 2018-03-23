<?php

use Faker\Factory as Faker;
use App\Models\SupplierContactType;
use App\Repositories\SupplierContactTypeRepository;

trait MakeSupplierContactTypeTrait
{
    /**
     * Create fake instance of SupplierContactType and save it in database
     *
     * @param array $supplierContactTypeFields
     * @return SupplierContactType
     */
    public function makeSupplierContactType($supplierContactTypeFields = [])
    {
        /** @var SupplierContactTypeRepository $supplierContactTypeRepo */
        $supplierContactTypeRepo = App::make(SupplierContactTypeRepository::class);
        $theme = $this->fakeSupplierContactTypeData($supplierContactTypeFields);
        return $supplierContactTypeRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierContactType
     *
     * @param array $supplierContactTypeFields
     * @return SupplierContactType
     */
    public function fakeSupplierContactType($supplierContactTypeFields = [])
    {
        return new SupplierContactType($this->fakeSupplierContactTypeData($supplierContactTypeFields));
    }

    /**
     * Get fake data of SupplierContactType
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupplierContactTypeData($supplierContactTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'supplierContactDescription' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $supplierContactTypeFields);
    }
}

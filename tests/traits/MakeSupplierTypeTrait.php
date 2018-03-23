<?php

use Faker\Factory as Faker;
use App\Models\SupplierType;
use App\Repositories\SupplierTypeRepository;

trait MakeSupplierTypeTrait
{
    /**
     * Create fake instance of SupplierType and save it in database
     *
     * @param array $supplierTypeFields
     * @return SupplierType
     */
    public function makeSupplierType($supplierTypeFields = [])
    {
        /** @var SupplierTypeRepository $supplierTypeRepo */
        $supplierTypeRepo = App::make(SupplierTypeRepository::class);
        $theme = $this->fakeSupplierTypeData($supplierTypeFields);
        return $supplierTypeRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierType
     *
     * @param array $supplierTypeFields
     * @return SupplierType
     */
    public function fakeSupplierType($supplierTypeFields = [])
    {
        return new SupplierType($this->fakeSupplierTypeData($supplierTypeFields));
    }

    /**
     * Get fake data of SupplierType
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupplierTypeData($supplierTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'typeDescription' => $fake->word
        ], $supplierTypeFields);
    }
}

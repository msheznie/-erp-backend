<?php

use Faker\Factory as Faker;
use App\Models\SupplierImportance;
use App\Repositories\SupplierImportanceRepository;

trait MakeSupplierImportanceTrait
{
    /**
     * Create fake instance of SupplierImportance and save it in database
     *
     * @param array $supplierImportanceFields
     * @return SupplierImportance
     */
    public function makeSupplierImportance($supplierImportanceFields = [])
    {
        /** @var SupplierImportanceRepository $supplierImportanceRepo */
        $supplierImportanceRepo = App::make(SupplierImportanceRepository::class);
        $theme = $this->fakeSupplierImportanceData($supplierImportanceFields);
        return $supplierImportanceRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierImportance
     *
     * @param array $supplierImportanceFields
     * @return SupplierImportance
     */
    public function fakeSupplierImportance($supplierImportanceFields = [])
    {
        return new SupplierImportance($this->fakeSupplierImportanceData($supplierImportanceFields));
    }

    /**
     * Get fake data of SupplierImportance
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupplierImportanceData($supplierImportanceFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'importanceDescription' => $fake->word
        ], $supplierImportanceFields);
    }
}

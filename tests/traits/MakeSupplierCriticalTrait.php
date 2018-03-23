<?php

use Faker\Factory as Faker;
use App\Models\SupplierCritical;
use App\Repositories\SupplierCriticalRepository;

trait MakeSupplierCriticalTrait
{
    /**
     * Create fake instance of SupplierCritical and save it in database
     *
     * @param array $supplierCriticalFields
     * @return SupplierCritical
     */
    public function makeSupplierCritical($supplierCriticalFields = [])
    {
        /** @var SupplierCriticalRepository $supplierCriticalRepo */
        $supplierCriticalRepo = App::make(SupplierCriticalRepository::class);
        $theme = $this->fakeSupplierCriticalData($supplierCriticalFields);
        return $supplierCriticalRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierCritical
     *
     * @param array $supplierCriticalFields
     * @return SupplierCritical
     */
    public function fakeSupplierCritical($supplierCriticalFields = [])
    {
        return new SupplierCritical($this->fakeSupplierCriticalData($supplierCriticalFields));
    }

    /**
     * Get fake data of SupplierCritical
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupplierCriticalData($supplierCriticalFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'description' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $supplierCriticalFields);
    }
}

<?php

use Faker\Factory as Faker;
use App\Models\SupplierCurrency;
use App\Repositories\SupplierCurrencyRepository;

trait MakeSupplierCurrencyTrait
{
    /**
     * Create fake instance of SupplierCurrency and save it in database
     *
     * @param array $supplierCurrencyFields
     * @return SupplierCurrency
     */
    public function makeSupplierCurrency($supplierCurrencyFields = [])
    {
        /** @var SupplierCurrencyRepository $supplierCurrencyRepo */
        $supplierCurrencyRepo = App::make(SupplierCurrencyRepository::class);
        $theme = $this->fakeSupplierCurrencyData($supplierCurrencyFields);
        return $supplierCurrencyRepo->create($theme);
    }

    /**
     * Get fake instance of SupplierCurrency
     *
     * @param array $supplierCurrencyFields
     * @return SupplierCurrency
     */
    public function fakeSupplierCurrency($supplierCurrencyFields = [])
    {
        return new SupplierCurrency($this->fakeSupplierCurrencyData($supplierCurrencyFields));
    }

    /**
     * Get fake data of SupplierCurrency
     *
     * @param array $postFields
     * @return array
     */
    public function fakeSupplierCurrencyData($supplierCurrencyFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'supplierCodeSystem' => $fake->randomDigitNotNull,
            'currencyID' => $fake->randomDigitNotNull,
            'bankMemo' => $fake->text,
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'isAssigned' => $fake->randomDigitNotNull,
            'isDefault' => $fake->randomDigitNotNull
        ], $supplierCurrencyFields);
    }
}

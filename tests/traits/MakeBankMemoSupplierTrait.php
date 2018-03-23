<?php

use Faker\Factory as Faker;
use App\Models\BankMemoSupplier;
use App\Repositories\BankMemoSupplierRepository;

trait MakeBankMemoSupplierTrait
{
    /**
     * Create fake instance of BankMemoSupplier and save it in database
     *
     * @param array $bankMemoSupplierFields
     * @return BankMemoSupplier
     */
    public function makeBankMemoSupplier($bankMemoSupplierFields = [])
    {
        /** @var BankMemoSupplierRepository $bankMemoSupplierRepo */
        $bankMemoSupplierRepo = App::make(BankMemoSupplierRepository::class);
        $theme = $this->fakeBankMemoSupplierData($bankMemoSupplierFields);
        return $bankMemoSupplierRepo->create($theme);
    }

    /**
     * Get fake instance of BankMemoSupplier
     *
     * @param array $bankMemoSupplierFields
     * @return BankMemoSupplier
     */
    public function fakeBankMemoSupplier($bankMemoSupplierFields = [])
    {
        return new BankMemoSupplier($this->fakeBankMemoSupplierData($bankMemoSupplierFields));
    }

    /**
     * Get fake data of BankMemoSupplier
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBankMemoSupplierData($bankMemoSupplierFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'memoHeader' => $fake->word,
            'memoDetail' => $fake->word,
            'supplierCodeSystem' => $fake->randomDigitNotNull,
            'supplierCurrencyID' => $fake->randomDigitNotNull,
            'updatedByUserID' => $fake->word,
            'updatedByUserName' => $fake->word,
            'updatedDate' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $bankMemoSupplierFields);
    }
}

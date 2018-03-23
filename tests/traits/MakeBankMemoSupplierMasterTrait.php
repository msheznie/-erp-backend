<?php

use Faker\Factory as Faker;
use App\Models\BankMemoSupplierMaster;
use App\Repositories\BankMemoSupplierMasterRepository;

trait MakeBankMemoSupplierMasterTrait
{
    /**
     * Create fake instance of BankMemoSupplierMaster and save it in database
     *
     * @param array $bankMemoSupplierMasterFields
     * @return BankMemoSupplierMaster
     */
    public function makeBankMemoSupplierMaster($bankMemoSupplierMasterFields = [])
    {
        /** @var BankMemoSupplierMasterRepository $bankMemoSupplierMasterRepo */
        $bankMemoSupplierMasterRepo = App::make(BankMemoSupplierMasterRepository::class);
        $theme = $this->fakeBankMemoSupplierMasterData($bankMemoSupplierMasterFields);
        return $bankMemoSupplierMasterRepo->create($theme);
    }

    /**
     * Get fake instance of BankMemoSupplierMaster
     *
     * @param array $bankMemoSupplierMasterFields
     * @return BankMemoSupplierMaster
     */
    public function fakeBankMemoSupplierMaster($bankMemoSupplierMasterFields = [])
    {
        return new BankMemoSupplierMaster($this->fakeBankMemoSupplierMasterData($bankMemoSupplierMasterFields));
    }

    /**
     * Get fake data of BankMemoSupplierMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBankMemoSupplierMasterData($bankMemoSupplierMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'memoHeader' => $fake->word,
            'memoDetail' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $bankMemoSupplierMasterFields);
    }
}

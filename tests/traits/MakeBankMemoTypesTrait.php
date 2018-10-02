<?php

use Faker\Factory as Faker;
use App\Models\BankMemoTypes;
use App\Repositories\BankMemoTypesRepository;

trait MakeBankMemoTypesTrait
{
    /**
     * Create fake instance of BankMemoTypes and save it in database
     *
     * @param array $bankMemoTypesFields
     * @return BankMemoTypes
     */
    public function makeBankMemoTypes($bankMemoTypesFields = [])
    {
        /** @var BankMemoTypesRepository $bankMemoTypesRepo */
        $bankMemoTypesRepo = App::make(BankMemoTypesRepository::class);
        $theme = $this->fakeBankMemoTypesData($bankMemoTypesFields);
        return $bankMemoTypesRepo->create($theme);
    }

    /**
     * Get fake instance of BankMemoTypes
     *
     * @param array $bankMemoTypesFields
     * @return BankMemoTypes
     */
    public function fakeBankMemoTypes($bankMemoTypesFields = [])
    {
        return new BankMemoTypes($this->fakeBankMemoTypesData($bankMemoTypesFields));
    }

    /**
     * Get fake data of BankMemoTypes
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBankMemoTypesData($bankMemoTypesFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'bankMemoHeader' => $fake->word,
            'sortOrder' => $fake->randomDigitNotNull
        ], $bankMemoTypesFields);
    }
}

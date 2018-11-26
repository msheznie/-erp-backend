<?php

use Faker\Factory as Faker;
use App\Models\BankMemoPayee;
use App\Repositories\BankMemoPayeeRepository;

trait MakeBankMemoPayeeTrait
{
    /**
     * Create fake instance of BankMemoPayee and save it in database
     *
     * @param array $bankMemoPayeeFields
     * @return BankMemoPayee
     */
    public function makeBankMemoPayee($bankMemoPayeeFields = [])
    {
        /** @var BankMemoPayeeRepository $bankMemoPayeeRepo */
        $bankMemoPayeeRepo = App::make(BankMemoPayeeRepository::class);
        $theme = $this->fakeBankMemoPayeeData($bankMemoPayeeFields);
        return $bankMemoPayeeRepo->create($theme);
    }

    /**
     * Get fake instance of BankMemoPayee
     *
     * @param array $bankMemoPayeeFields
     * @return BankMemoPayee
     */
    public function fakeBankMemoPayee($bankMemoPayeeFields = [])
    {
        return new BankMemoPayee($this->fakeBankMemoPayeeData($bankMemoPayeeFields));
    }

    /**
     * Get fake data of BankMemoPayee
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBankMemoPayeeData($bankMemoPayeeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'documentSystemCode' => $fake->randomDigitNotNull,
            'bankMemoTypeID' => $fake->randomDigitNotNull,
            'memoHeader' => $fake->word,
            'memoDetail' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $bankMemoPayeeFields);
    }
}

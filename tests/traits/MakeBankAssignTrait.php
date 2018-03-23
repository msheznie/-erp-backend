<?php

use Faker\Factory as Faker;
use App\Models\BankAssign;
use App\Repositories\BankAssignRepository;

trait MakeBankAssignTrait
{
    /**
     * Create fake instance of BankAssign and save it in database
     *
     * @param array $bankAssignFields
     * @return BankAssign
     */
    public function makeBankAssign($bankAssignFields = [])
    {
        /** @var BankAssignRepository $bankAssignRepo */
        $bankAssignRepo = App::make(BankAssignRepository::class);
        $theme = $this->fakeBankAssignData($bankAssignFields);
        return $bankAssignRepo->create($theme);
    }

    /**
     * Get fake instance of BankAssign
     *
     * @param array $bankAssignFields
     * @return BankAssign
     */
    public function fakeBankAssign($bankAssignFields = [])
    {
        return new BankAssign($this->fakeBankAssignData($bankAssignFields));
    }

    /**
     * Get fake data of BankAssign
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBankAssignData($bankAssignFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'bankmasterAutoID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'bankShortCode' => $fake->word,
            'bankName' => $fake->word,
            'isAssigned' => $fake->randomDigitNotNull,
            'isDefault' => $fake->randomDigitNotNull,
            'isActive' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdByEmpID' => $fake->word,
            'TimeStamp' => $fake->date('Y-m-d H:i:s')
        ], $bankAssignFields);
    }
}

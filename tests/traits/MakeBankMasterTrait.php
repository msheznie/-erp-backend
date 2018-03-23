<?php

use Faker\Factory as Faker;
use App\Models\BankMaster;
use App\Repositories\BankMasterRepository;

trait MakeBankMasterTrait
{
    /**
     * Create fake instance of BankMaster and save it in database
     *
     * @param array $bankMasterFields
     * @return BankMaster
     */
    public function makeBankMaster($bankMasterFields = [])
    {
        /** @var BankMasterRepository $bankMasterRepo */
        $bankMasterRepo = App::make(BankMasterRepository::class);
        $theme = $this->fakeBankMasterData($bankMasterFields);
        return $bankMasterRepo->create($theme);
    }

    /**
     * Get fake instance of BankMaster
     *
     * @param array $bankMasterFields
     * @return BankMaster
     */
    public function fakeBankMaster($bankMasterFields = [])
    {
        return new BankMaster($this->fakeBankMasterData($bankMasterFields));
    }

    /**
     * Get fake data of BankMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBankMasterData($bankMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'bankShortCode' => $fake->word,
            'bankName' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdByEmpID' => $fake->word,
            'TimeStamp' => $fake->date('Y-m-d H:i:s')
        ], $bankMasterFields);
    }
}

<?php

use Faker\Factory as Faker;
use App\Models\BankReconciliationRefferedBack;
use App\Repositories\BankReconciliationRefferedBackRepository;

trait MakeBankReconciliationRefferedBackTrait
{
    /**
     * Create fake instance of BankReconciliationRefferedBack and save it in database
     *
     * @param array $bankReconciliationRefferedBackFields
     * @return BankReconciliationRefferedBack
     */
    public function makeBankReconciliationRefferedBack($bankReconciliationRefferedBackFields = [])
    {
        /** @var BankReconciliationRefferedBackRepository $bankReconciliationRefferedBackRepo */
        $bankReconciliationRefferedBackRepo = App::make(BankReconciliationRefferedBackRepository::class);
        $theme = $this->fakeBankReconciliationRefferedBackData($bankReconciliationRefferedBackFields);
        return $bankReconciliationRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of BankReconciliationRefferedBack
     *
     * @param array $bankReconciliationRefferedBackFields
     * @return BankReconciliationRefferedBack
     */
    public function fakeBankReconciliationRefferedBack($bankReconciliationRefferedBackFields = [])
    {
        return new BankReconciliationRefferedBack($this->fakeBankReconciliationRefferedBackData($bankReconciliationRefferedBackFields));
    }

    /**
     * Get fake data of BankReconciliationRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBankReconciliationRefferedBackData($bankReconciliationRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'bankRecAutoID' => $fake->randomDigitNotNull,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'bankMasterID' => $fake->randomDigitNotNull,
            'bankAccountAutoID' => $fake->randomDigitNotNull,
            'bankGLAutoID' => $fake->randomDigitNotNull,
            'month' => $fake->randomDigitNotNull,
            'serialNo' => $fake->randomDigitNotNull,
            'bankRecPrimaryCode' => $fake->word,
            'year' => $fake->randomDigitNotNull,
            'bankRecAsOf' => $fake->date('Y-m-d H:i:s'),
            'openingBalance' => $fake->randomDigitNotNull,
            'closingBalance' => $fake->randomDigitNotNull,
            'description' => $fake->word,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'createdPcID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $bankReconciliationRefferedBackFields);
    }
}

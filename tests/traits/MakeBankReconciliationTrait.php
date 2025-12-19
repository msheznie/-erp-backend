<?php

use Faker\Factory as Faker;
use App\Models\BankReconciliation;
use App\Repositories\BankReconciliationRepository;

trait MakeBankReconciliationTrait
{
    /**
     * Create fake instance of BankReconciliation and save it in database
     *
     * @param array $bankReconciliationFields
     * @return BankReconciliation
     */
    public function makeBankReconciliation($bankReconciliationFields = [])
    {
        /** @var BankReconciliationRepository $bankReconciliationRepo */
        $bankReconciliationRepo = App::make(BankReconciliationRepository::class);
        $theme = $this->fakeBankReconciliationData($bankReconciliationFields);
        return $bankReconciliationRepo->create($theme);
    }

    /**
     * Get fake instance of BankReconciliation
     *
     * @param array $bankReconciliationFields
     * @return BankReconciliation
     */
    public function fakeBankReconciliation($bankReconciliationFields = [])
    {
        return new BankReconciliation($this->fakeBankReconciliationData($bankReconciliationFields));
    }

    /**
     * Get fake data of BankReconciliation
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBankReconciliationData($bankReconciliationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'bankGLAutoID' => $fake->randomDigitNotNull,
            'month' => $fake->randomDigitNotNull,
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
            'createdPcID' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'modifiedPc' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $bankReconciliationFields);
    }
}

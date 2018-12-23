<?php

use Faker\Factory as Faker;
use App\Models\BankAccountRefferedBack;
use App\Repositories\BankAccountRefferedBackRepository;

trait MakeBankAccountRefferedBackTrait
{
    /**
     * Create fake instance of BankAccountRefferedBack and save it in database
     *
     * @param array $bankAccountRefferedBackFields
     * @return BankAccountRefferedBack
     */
    public function makeBankAccountRefferedBack($bankAccountRefferedBackFields = [])
    {
        /** @var BankAccountRefferedBackRepository $bankAccountRefferedBackRepo */
        $bankAccountRefferedBackRepo = App::make(BankAccountRefferedBackRepository::class);
        $theme = $this->fakeBankAccountRefferedBackData($bankAccountRefferedBackFields);
        return $bankAccountRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of BankAccountRefferedBack
     *
     * @param array $bankAccountRefferedBackFields
     * @return BankAccountRefferedBack
     */
    public function fakeBankAccountRefferedBack($bankAccountRefferedBackFields = [])
    {
        return new BankAccountRefferedBack($this->fakeBankAccountRefferedBackData($bankAccountRefferedBackFields));
    }

    /**
     * Get fake data of BankAccountRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBankAccountRefferedBackData($bankAccountRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'bankAccountAutoID' => $fake->randomDigitNotNull,
            'bankAssignedAutoID' => $fake->randomDigitNotNull,
            'bankmasterAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'bankShortCode' => $fake->word,
            'bankName' => $fake->word,
            'bankBranch' => $fake->word,
            'BranchCode' => $fake->word,
            'BranchAddress' => $fake->text,
            'BranchContactPerson' => $fake->word,
            'BranchTel' => $fake->word,
            'BranchFax' => $fake->word,
            'BranchEmail' => $fake->word,
            'AccountNo' => $fake->word,
            'accountCurrencyID' => $fake->randomDigitNotNull,
            'accountSwiftCode' => $fake->word,
            'accountIBAN#' => $fake->word,
            'chqueManualStartingNo' => $fake->randomDigitNotNull,
            'isManualActive' => $fake->randomDigitNotNull,
            'chquePrintedStartingNo' => $fake->randomDigitNotNull,
            'isPrintedActive' => $fake->randomDigitNotNull,
            'chartOfAccountSystemID' => $fake->randomDigitNotNull,
            'glCodeLinked' => $fake->word,
            'extraNote' => $fake->text,
            'isAccountActive' => $fake->randomDigitNotNull,
            'isDefault' => $fake->randomDigitNotNull,
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedByEmpID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'approvedEmpName' => $fake->word,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedComments' => $fake->text,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdEmpID' => $fake->word,
            'createdPCID' => $fake->word,
            'modifedDateTime' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedByEmpID' => $fake->word,
            'modifiedPCID' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s'),
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull
        ], $bankAccountRefferedBackFields);
    }
}

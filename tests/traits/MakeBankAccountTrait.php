<?php

use Faker\Factory as Faker;
use App\Models\BankAccount;
use App\Repositories\BankAccountRepository;

trait MakeBankAccountTrait
{
    /**
     * Create fake instance of BankAccount and save it in database
     *
     * @param array $bankAccountFields
     * @return BankAccount
     */
    public function makeBankAccount($bankAccountFields = [])
    {
        /** @var BankAccountRepository $bankAccountRepo */
        $bankAccountRepo = App::make(BankAccountRepository::class);
        $theme = $this->fakeBankAccountData($bankAccountFields);
        return $bankAccountRepo->create($theme);
    }

    /**
     * Get fake instance of BankAccount
     *
     * @param array $bankAccountFields
     * @return BankAccount
     */
    public function fakeBankAccount($bankAccountFields = [])
    {
        return new BankAccount($this->fakeBankAccountData($bankAccountFields));
    }

    /**
     * Get fake data of BankAccount
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBankAccountData($bankAccountFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'bankAssignedAutoID' => $fake->randomDigitNotNull,
            'bankmasterAutoID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
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
            'glCodeLinked' => $fake->word,
            'extraNote' => $fake->text,
            'isAccountActive' => $fake->randomDigitNotNull,
            'isDefault' => $fake->randomDigitNotNull,
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedByEmpID' => $fake->word,
            'approvedEmpName' => $fake->word,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedComments' => $fake->text,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdEmpID' => $fake->word,
            'createdPCID' => $fake->word,
            'modifedDateTime' => $fake->word,
            'modifiedByEmpID' => $fake->word,
            'modifiedPCID' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $bankAccountFields);
    }
}

<?php

use Faker\Factory as Faker;
use App\Models\BudgetTransferForm;
use App\Repositories\BudgetTransferFormRepository;

trait MakeBudgetTransferFormTrait
{
    /**
     * Create fake instance of BudgetTransferForm and save it in database
     *
     * @param array $budgetTransferFormFields
     * @return BudgetTransferForm
     */
    public function makeBudgetTransferForm($budgetTransferFormFields = [])
    {
        /** @var BudgetTransferFormRepository $budgetTransferFormRepo */
        $budgetTransferFormRepo = App::make(BudgetTransferFormRepository::class);
        $theme = $this->fakeBudgetTransferFormData($budgetTransferFormFields);
        return $budgetTransferFormRepo->create($theme);
    }

    /**
     * Get fake instance of BudgetTransferForm
     *
     * @param array $budgetTransferFormFields
     * @return BudgetTransferForm
     */
    public function fakeBudgetTransferForm($budgetTransferFormFields = [])
    {
        return new BudgetTransferForm($this->fakeBudgetTransferFormData($budgetTransferFormFields));
    }

    /**
     * Get fake data of BudgetTransferForm
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBudgetTransferFormData($budgetTransferFormFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'year' => $fake->randomDigitNotNull,
            'transferVoucherNo' => $fake->word,
            'createdDate' => $fake->date('Y-m-d H:i:s'),
            'comments' => $fake->text,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByEmpName' => $fake->word,
            'approvedYN' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'approvedEmpID' => $fake->word,
            'approvedEmpName' => $fake->word,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $budgetTransferFormFields);
    }
}

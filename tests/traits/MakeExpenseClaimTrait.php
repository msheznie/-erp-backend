<?php

use Faker\Factory as Faker;
use App\Models\ExpenseClaim;
use App\Repositories\ExpenseClaimRepository;

trait MakeExpenseClaimTrait
{
    /**
     * Create fake instance of ExpenseClaim and save it in database
     *
     * @param array $expenseClaimFields
     * @return ExpenseClaim
     */
    public function makeExpenseClaim($expenseClaimFields = [])
    {
        /** @var ExpenseClaimRepository $expenseClaimRepo */
        $expenseClaimRepo = App::make(ExpenseClaimRepository::class);
        $theme = $this->fakeExpenseClaimData($expenseClaimFields);
        return $expenseClaimRepo->create($theme);
    }

    /**
     * Get fake instance of ExpenseClaim
     *
     * @param array $expenseClaimFields
     * @return ExpenseClaim
     */
    public function fakeExpenseClaim($expenseClaimFields = [])
    {
        return new ExpenseClaim($this->fakeExpenseClaimData($expenseClaimFields));
    }

    /**
     * Get fake data of ExpenseClaim
     *
     * @param array $postFields
     * @return array
     */
    public function fakeExpenseClaimData($expenseClaimFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyID' => $fake->word,
            'departmentID' => $fake->word,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'expenseClaimCode' => $fake->word,
            'expenseClaimDate' => $fake->date('Y-m-d H:i:s'),
            'clamiedByName' => $fake->word,
            'comments' => $fake->text,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'glCodeAssignedYN' => $fake->randomDigitNotNull,
            'addedForPayment' => $fake->randomDigitNotNull,
            'rejectedYN' => $fake->randomDigitNotNull,
            'rejectedComment' => $fake->text,
            'seniorManager' => $fake->word,
            'pettyCashYN' => $fake->randomDigitNotNull,
            'addedToSalary' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $expenseClaimFields);
    }
}

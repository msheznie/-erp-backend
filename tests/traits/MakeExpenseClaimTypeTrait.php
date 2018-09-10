<?php

use Faker\Factory as Faker;
use App\Models\ExpenseClaimType;
use App\Repositories\ExpenseClaimTypeRepository;

trait MakeExpenseClaimTypeTrait
{
    /**
     * Create fake instance of ExpenseClaimType and save it in database
     *
     * @param array $expenseClaimTypeFields
     * @return ExpenseClaimType
     */
    public function makeExpenseClaimType($expenseClaimTypeFields = [])
    {
        /** @var ExpenseClaimTypeRepository $expenseClaimTypeRepo */
        $expenseClaimTypeRepo = App::make(ExpenseClaimTypeRepository::class);
        $theme = $this->fakeExpenseClaimTypeData($expenseClaimTypeFields);
        return $expenseClaimTypeRepo->create($theme);
    }

    /**
     * Get fake instance of ExpenseClaimType
     *
     * @param array $expenseClaimTypeFields
     * @return ExpenseClaimType
     */
    public function fakeExpenseClaimType($expenseClaimTypeFields = [])
    {
        return new ExpenseClaimType($this->fakeExpenseClaimTypeData($expenseClaimTypeFields));
    }

    /**
     * Get fake data of ExpenseClaimType
     *
     * @param array $postFields
     * @return array
     */
    public function fakeExpenseClaimTypeData($expenseClaimTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'expenseClaimTypeDescription' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $expenseClaimTypeFields);
    }
}

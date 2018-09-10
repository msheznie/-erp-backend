<?php

use Faker\Factory as Faker;
use App\Models\ExpenseClaimCategories;
use App\Repositories\ExpenseClaimCategoriesRepository;

trait MakeExpenseClaimCategoriesTrait
{
    /**
     * Create fake instance of ExpenseClaimCategories and save it in database
     *
     * @param array $expenseClaimCategoriesFields
     * @return ExpenseClaimCategories
     */
    public function makeExpenseClaimCategories($expenseClaimCategoriesFields = [])
    {
        /** @var ExpenseClaimCategoriesRepository $expenseClaimCategoriesRepo */
        $expenseClaimCategoriesRepo = App::make(ExpenseClaimCategoriesRepository::class);
        $theme = $this->fakeExpenseClaimCategoriesData($expenseClaimCategoriesFields);
        return $expenseClaimCategoriesRepo->create($theme);
    }

    /**
     * Get fake instance of ExpenseClaimCategories
     *
     * @param array $expenseClaimCategoriesFields
     * @return ExpenseClaimCategories
     */
    public function fakeExpenseClaimCategories($expenseClaimCategoriesFields = [])
    {
        return new ExpenseClaimCategories($this->fakeExpenseClaimCategoriesData($expenseClaimCategoriesFields));
    }

    /**
     * Get fake data of ExpenseClaimCategories
     *
     * @param array $postFields
     * @return array
     */
    public function fakeExpenseClaimCategoriesData($expenseClaimCategoriesFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'claimcategoriesDescription' => $fake->text,
            'glCode' => $fake->word,
            'glCodeDescription' => $fake->text,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $expenseClaimCategoriesFields);
    }
}

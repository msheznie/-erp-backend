<?php

use Faker\Factory as Faker;
use App\Models\ExpenseClaimDetails;
use App\Repositories\ExpenseClaimDetailsRepository;

trait MakeExpenseClaimDetailsTrait
{
    /**
     * Create fake instance of ExpenseClaimDetails and save it in database
     *
     * @param array $expenseClaimDetailsFields
     * @return ExpenseClaimDetails
     */
    public function makeExpenseClaimDetails($expenseClaimDetailsFields = [])
    {
        /** @var ExpenseClaimDetailsRepository $expenseClaimDetailsRepo */
        $expenseClaimDetailsRepo = App::make(ExpenseClaimDetailsRepository::class);
        $theme = $this->fakeExpenseClaimDetailsData($expenseClaimDetailsFields);
        return $expenseClaimDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of ExpenseClaimDetails
     *
     * @param array $expenseClaimDetailsFields
     * @return ExpenseClaimDetails
     */
    public function fakeExpenseClaimDetails($expenseClaimDetailsFields = [])
    {
        return new ExpenseClaimDetails($this->fakeExpenseClaimDetailsData($expenseClaimDetailsFields));
    }

    /**
     * Get fake data of ExpenseClaimDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeExpenseClaimDetailsData($expenseClaimDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'expenseClaimMasterAutoID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineCode' => $fake->word,
            'expenseClaimCategoriesAutoID' => $fake->randomDigitNotNull,
            'description' => $fake->text,
            'docRef' => $fake->word,
            'amount' => $fake->randomDigitNotNull,
            'comments' => $fake->text,
            'glCode' => $fake->word,
            'glCodeDescription' => $fake->word,
            'currencyID' => $fake->randomDigitNotNull,
            'currencyER' => $fake->randomDigitNotNull,
            'localCurrency' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'comRptCurrency' => $fake->randomDigitNotNull,
            'comRptCurrencyER' => $fake->randomDigitNotNull,
            'comRptAmount' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $expenseClaimDetailsFields);
    }
}

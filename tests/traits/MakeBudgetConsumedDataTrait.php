<?php

use Faker\Factory as Faker;
use App\Models\BudgetConsumedData;
use App\Repositories\BudgetConsumedDataRepository;

trait MakeBudgetConsumedDataTrait
{
    /**
     * Create fake instance of BudgetConsumedData and save it in database
     *
     * @param array $budgetConsumedDataFields
     * @return BudgetConsumedData
     */
    public function makeBudgetConsumedData($budgetConsumedDataFields = [])
    {
        /** @var BudgetConsumedDataRepository $budgetConsumedDataRepo */
        $budgetConsumedDataRepo = App::make(BudgetConsumedDataRepository::class);
        $theme = $this->fakeBudgetConsumedDataData($budgetConsumedDataFields);
        return $budgetConsumedDataRepo->create($theme);
    }

    /**
     * Get fake instance of BudgetConsumedData
     *
     * @param array $budgetConsumedDataFields
     * @return BudgetConsumedData
     */
    public function fakeBudgetConsumedData($budgetConsumedDataFields = [])
    {
        return new BudgetConsumedData($this->fakeBudgetConsumedDataData($budgetConsumedDataFields));
    }

    /**
     * Get fake data of BudgetConsumedData
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBudgetConsumedDataData($budgetConsumedDataFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'documentSystemCode' => $fake->randomDigitNotNull,
            'documentCode' => $fake->word,
            'chartOfAccountID' => $fake->randomDigitNotNull,
            'GLCode' => $fake->word,
            'year' => $fake->randomDigitNotNull,
            'month' => $fake->randomDigitNotNull,
            'consumedLocalCurrencyID' => $fake->randomDigitNotNull,
            'consumedLocalAmount' => $fake->randomDigitNotNull,
            'consumedRptCurrencyID' => $fake->randomDigitNotNull,
            'consumedRptAmount' => $fake->randomDigitNotNull,
            'consumeYN' => $fake->randomDigitNotNull,
            'timestamp' => $fake->word
        ], $budgetConsumedDataFields);
    }
}

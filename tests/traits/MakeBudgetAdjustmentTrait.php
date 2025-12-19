<?php

use Faker\Factory as Faker;
use App\Models\BudgetAdjustment;
use App\Repositories\BudgetAdjustmentRepository;

trait MakeBudgetAdjustmentTrait
{
    /**
     * Create fake instance of BudgetAdjustment and save it in database
     *
     * @param array $budgetAdjustmentFields
     * @return BudgetAdjustment
     */
    public function makeBudgetAdjustment($budgetAdjustmentFields = [])
    {
        /** @var BudgetAdjustmentRepository $budgetAdjustmentRepo */
        $budgetAdjustmentRepo = App::make(BudgetAdjustmentRepository::class);
        $theme = $this->fakeBudgetAdjustmentData($budgetAdjustmentFields);
        return $budgetAdjustmentRepo->create($theme);
    }

    /**
     * Get fake instance of BudgetAdjustment
     *
     * @param array $budgetAdjustmentFields
     * @return BudgetAdjustment
     */
    public function fakeBudgetAdjustment($budgetAdjustmentFields = [])
    {
        return new BudgetAdjustment($this->fakeBudgetAdjustmentData($budgetAdjustmentFields));
    }

    /**
     * Get fake data of BudgetAdjustment
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBudgetAdjustmentData($budgetAdjustmentFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyId' => $fake->word,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLine' => $fake->word,
            'adjustedGLCodeSystemID' => $fake->randomDigitNotNull,
            'adjustedGLCode' => $fake->word,
            'fromGLCodeSystemID' => $fake->randomDigitNotNull,
            'fromGLCode' => $fake->word,
            'toGLCodeSystemID' => $fake->randomDigitNotNull,
            'toGLCode' => $fake->word,
            'Year' => $fake->randomDigitNotNull,
            'adjustmedLocalAmount' => $fake->randomDigitNotNull,
            'adjustmentRptAmount' => $fake->randomDigitNotNull,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdByUserID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedByUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $budgetAdjustmentFields);
    }
}

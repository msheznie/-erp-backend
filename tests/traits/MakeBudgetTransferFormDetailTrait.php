<?php

use Faker\Factory as Faker;
use App\Models\BudgetTransferFormDetail;
use App\Repositories\BudgetTransferFormDetailRepository;

trait MakeBudgetTransferFormDetailTrait
{
    /**
     * Create fake instance of BudgetTransferFormDetail and save it in database
     *
     * @param array $budgetTransferFormDetailFields
     * @return BudgetTransferFormDetail
     */
    public function makeBudgetTransferFormDetail($budgetTransferFormDetailFields = [])
    {
        /** @var BudgetTransferFormDetailRepository $budgetTransferFormDetailRepo */
        $budgetTransferFormDetailRepo = App::make(BudgetTransferFormDetailRepository::class);
        $theme = $this->fakeBudgetTransferFormDetailData($budgetTransferFormDetailFields);
        return $budgetTransferFormDetailRepo->create($theme);
    }

    /**
     * Get fake instance of BudgetTransferFormDetail
     *
     * @param array $budgetTransferFormDetailFields
     * @return BudgetTransferFormDetail
     */
    public function fakeBudgetTransferFormDetail($budgetTransferFormDetailFields = [])
    {
        return new BudgetTransferFormDetail($this->fakeBudgetTransferFormDetailData($budgetTransferFormDetailFields));
    }

    /**
     * Get fake data of BudgetTransferFormDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBudgetTransferFormDetailData($budgetTransferFormDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'budgetTransferFormAutoID' => $fake->randomDigitNotNull,
            'year' => $fake->randomDigitNotNull,
            'fromTemplateDetailID' => $fake->randomDigitNotNull,
            'fromServiceLineSystemID' => $fake->randomDigitNotNull,
            'fromServiceLineCode' => $fake->word,
            'fromChartOfAccountSystemID' => $fake->randomDigitNotNull,
            'FromGLCode' => $fake->word,
            'FromGLCodeDescription' => $fake->word,
            'toTemplateDetailID' => $fake->randomDigitNotNull,
            'toServiceLineSystemID' => $fake->randomDigitNotNull,
            'toServiceLineCode' => $fake->word,
            'toChartOfAccountSystemID' => $fake->randomDigitNotNull,
            'toGLCode' => $fake->word,
            'toGLCodeDescription' => $fake->word,
            'adjustmentAmountLocal' => $fake->randomDigitNotNull,
            'adjustmentAmountRpt' => $fake->randomDigitNotNull,
            'remarks' => $fake->text,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $budgetTransferFormDetailFields);
    }
}

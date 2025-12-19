<?php

use Faker\Factory as Faker;
use App\Models\BudgetMaster;
use App\Repositories\BudgetMasterRepository;

trait MakeBudgetMasterTrait
{
    /**
     * Create fake instance of BudgetMaster and save it in database
     *
     * @param array $budgetMasterFields
     * @return BudgetMaster
     */
    public function makeBudgetMaster($budgetMasterFields = [])
    {
        /** @var BudgetMasterRepository $budgetMasterRepo */
        $budgetMasterRepo = App::make(BudgetMasterRepository::class);
        $theme = $this->fakeBudgetMasterData($budgetMasterFields);
        return $budgetMasterRepo->create($theme);
    }

    /**
     * Get fake instance of BudgetMaster
     *
     * @param array $budgetMasterFields
     * @return BudgetMaster
     */
    public function fakeBudgetMaster($budgetMasterFields = [])
    {
        return new BudgetMaster($this->fakeBudgetMasterData($budgetMasterFields));
    }

    /**
     * Get fake data of BudgetMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBudgetMasterData($budgetMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'templateMasterID' => $fake->randomDigitNotNull,
            'Year' => $fake->randomDigitNotNull,
            'month' => $fake->randomDigitNotNull,
            'createdByUserSystemID' => $fake->randomDigitNotNull,
            'createdByUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $budgetMasterFields);
    }
}

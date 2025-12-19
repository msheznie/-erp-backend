<?php

use Faker\Factory as Faker;
use App\Models\MonthlyAdditionDetail;
use App\Repositories\MonthlyAdditionDetailRepository;

trait MakeMonthlyAdditionDetailTrait
{
    /**
     * Create fake instance of MonthlyAdditionDetail and save it in database
     *
     * @param array $monthlyAdditionDetailFields
     * @return MonthlyAdditionDetail
     */
    public function makeMonthlyAdditionDetail($monthlyAdditionDetailFields = [])
    {
        /** @var MonthlyAdditionDetailRepository $monthlyAdditionDetailRepo */
        $monthlyAdditionDetailRepo = App::make(MonthlyAdditionDetailRepository::class);
        $theme = $this->fakeMonthlyAdditionDetailData($monthlyAdditionDetailFields);
        return $monthlyAdditionDetailRepo->create($theme);
    }

    /**
     * Get fake instance of MonthlyAdditionDetail
     *
     * @param array $monthlyAdditionDetailFields
     * @return MonthlyAdditionDetail
     */
    public function fakeMonthlyAdditionDetail($monthlyAdditionDetailFields = [])
    {
        return new MonthlyAdditionDetail($this->fakeMonthlyAdditionDetailData($monthlyAdditionDetailFields));
    }

    /**
     * Get fake data of MonthlyAdditionDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeMonthlyAdditionDetailData($monthlyAdditionDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'monthlyAdditionsMasterID' => $fake->randomDigitNotNull,
            'expenseClaimMasterAutoID' => $fake->randomDigitNotNull,
            'empSystemID' => $fake->randomDigitNotNull,
            'empID' => $fake->word,
            'empdepartment' => $fake->randomDigitNotNull,
            'description' => $fake->word,
            'declareCurrency' => $fake->randomDigitNotNull,
            'declareAmount' => $fake->randomDigitNotNull,
            'amountMA' => $fake->randomDigitNotNull,
            'currencyMAID' => $fake->randomDigitNotNull,
            'approvedYN' => $fake->randomDigitNotNull,
            'glCode' => $fake->randomDigitNotNull,
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localCurrencyER' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'rptCurrencyID' => $fake->randomDigitNotNull,
            'rptCurrencyER' => $fake->randomDigitNotNull,
            'rptAmount' => $fake->randomDigitNotNull,
            'IsSSO' => $fake->randomDigitNotNull,
            'IsTax' => $fake->randomDigitNotNull,
            'createdpc' => $fake->word,
            'createdUserGroup' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifieduser' => $fake->word,
            'modifiedpc' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $monthlyAdditionDetailFields);
    }
}

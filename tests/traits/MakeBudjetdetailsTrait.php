<?php

use Faker\Factory as Faker;
use App\Models\Budjetdetails;
use App\Repositories\BudjetdetailsRepository;

trait MakeBudjetdetailsTrait
{
    /**
     * Create fake instance of Budjetdetails and save it in database
     *
     * @param array $budjetdetailsFields
     * @return Budjetdetails
     */
    public function makeBudjetdetails($budjetdetailsFields = [])
    {
        /** @var BudjetdetailsRepository $budjetdetailsRepo */
        $budjetdetailsRepo = App::make(BudjetdetailsRepository::class);
        $theme = $this->fakeBudjetdetailsData($budjetdetailsFields);
        return $budjetdetailsRepo->create($theme);
    }

    /**
     * Get fake instance of Budjetdetails
     *
     * @param array $budjetdetailsFields
     * @return Budjetdetails
     */
    public function fakeBudjetdetails($budjetdetailsFields = [])
    {
        return new Budjetdetails($this->fakeBudjetdetailsData($budjetdetailsFields));
    }

    /**
     * Get fake data of Budjetdetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeBudjetdetailsData($budjetdetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'budgetmasterID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyId' => $fake->word,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLine' => $fake->word,
            'templateDetailID' => $fake->randomDigitNotNull,
            'chartOfAccountID' => $fake->randomDigitNotNull,
            'glCode' => $fake->word,
            'glCodeType' => $fake->word,
            'Year' => $fake->randomDigitNotNull,
            'month' => $fake->randomDigitNotNull,
            'budjetAmtLocal' => $fake->randomDigitNotNull,
            'budjetAmtRpt' => $fake->randomDigitNotNull,
            'createdByUserSystemID' => $fake->randomDigitNotNull,
            'createdByUserID' => $fake->word,
            'modifiedByUserSystemID' => $fake->randomDigitNotNull,
            'modifiedByUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $budjetdetailsFields);
    }
}

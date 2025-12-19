<?php

use Faker\Factory as Faker;
use App\Models\CompanyFinancePeriod;
use App\Repositories\CompanyFinancePeriodRepository;

trait MakeCompanyFinancePeriodTrait
{
    /**
     * Create fake instance of CompanyFinancePeriod and save it in database
     *
     * @param array $companyFinancePeriodFields
     * @return CompanyFinancePeriod
     */
    public function makeCompanyFinancePeriod($companyFinancePeriodFields = [])
    {
        /** @var CompanyFinancePeriodRepository $companyFinancePeriodRepo */
        $companyFinancePeriodRepo = App::make(CompanyFinancePeriodRepository::class);
        $theme = $this->fakeCompanyFinancePeriodData($companyFinancePeriodFields);
        return $companyFinancePeriodRepo->create($theme);
    }

    /**
     * Get fake instance of CompanyFinancePeriod
     *
     * @param array $companyFinancePeriodFields
     * @return CompanyFinancePeriod
     */
    public function fakeCompanyFinancePeriod($companyFinancePeriodFields = [])
    {
        return new CompanyFinancePeriod($this->fakeCompanyFinancePeriodData($companyFinancePeriodFields));
    }

    /**
     * Get fake data of CompanyFinancePeriod
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCompanyFinancePeriodData($companyFinancePeriodFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'departmentSystemID' => $fake->randomDigitNotNull,
            'departmentID' => $fake->word,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'dateFrom' => $fake->date('Y-m-d H:i:s'),
            'dateTo' => $fake->date('Y-m-d H:i:s'),
            'isActive' => $fake->randomDigitNotNull,
            'isCurrent' => $fake->randomDigitNotNull,
            'isClosed' => $fake->randomDigitNotNull,
            'closedByEmpID' => $fake->word,
            'closedByEmpSystemID' => $fake->randomDigitNotNull,
            'closedByEmpName' => $fake->word,
            'closedDate' => $fake->date('Y-m-d H:i:s'),
            'comments' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $companyFinancePeriodFields);
    }
}

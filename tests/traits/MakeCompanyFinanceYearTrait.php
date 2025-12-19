<?php

use Faker\Factory as Faker;
use App\Models\CompanyFinanceYear;
use App\Repositories\CompanyFinanceYearRepository;

trait MakeCompanyFinanceYearTrait
{
    /**
     * Create fake instance of CompanyFinanceYear and save it in database
     *
     * @param array $companyFinanceYearFields
     * @return CompanyFinanceYear
     */
    public function makeCompanyFinanceYear($companyFinanceYearFields = [])
    {
        /** @var CompanyFinanceYearRepository $companyFinanceYearRepo */
        $companyFinanceYearRepo = App::make(CompanyFinanceYearRepository::class);
        $theme = $this->fakeCompanyFinanceYearData($companyFinanceYearFields);
        return $companyFinanceYearRepo->create($theme);
    }

    /**
     * Get fake instance of CompanyFinanceYear
     *
     * @param array $companyFinanceYearFields
     * @return CompanyFinanceYear
     */
    public function fakeCompanyFinanceYear($companyFinanceYearFields = [])
    {
        return new CompanyFinanceYear($this->fakeCompanyFinanceYearData($companyFinanceYearFields));
    }

    /**
     * Get fake data of CompanyFinanceYear
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCompanyFinanceYearData($companyFinanceYearFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'bigginingDate' => $fake->date('Y-m-d H:i:s'),
            'endingDate' => $fake->date('Y-m-d H:i:s'),
            'isActive' => $fake->randomDigitNotNull,
            'isCurrent' => $fake->randomDigitNotNull,
            'isClosed' => $fake->randomDigitNotNull,
            'closedByEmpSystemID' => $fake->randomDigitNotNull,
            'closedByEmpID' => $fake->word,
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
        ], $companyFinanceYearFields);
    }
}

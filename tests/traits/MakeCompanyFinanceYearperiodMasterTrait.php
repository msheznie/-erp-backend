<?php

use Faker\Factory as Faker;
use App\Models\CompanyFinanceYearperiodMaster;
use App\Repositories\CompanyFinanceYearperiodMasterRepository;

trait MakeCompanyFinanceYearperiodMasterTrait
{
    /**
     * Create fake instance of CompanyFinanceYearperiodMaster and save it in database
     *
     * @param array $companyFinanceYearperiodMasterFields
     * @return CompanyFinanceYearperiodMaster
     */
    public function makeCompanyFinanceYearperiodMaster($companyFinanceYearperiodMasterFields = [])
    {
        /** @var CompanyFinanceYearperiodMasterRepository $companyFinanceYearperiodMasterRepo */
        $companyFinanceYearperiodMasterRepo = App::make(CompanyFinanceYearperiodMasterRepository::class);
        $theme = $this->fakeCompanyFinanceYearperiodMasterData($companyFinanceYearperiodMasterFields);
        return $companyFinanceYearperiodMasterRepo->create($theme);
    }

    /**
     * Get fake instance of CompanyFinanceYearperiodMaster
     *
     * @param array $companyFinanceYearperiodMasterFields
     * @return CompanyFinanceYearperiodMaster
     */
    public function fakeCompanyFinanceYearperiodMaster($companyFinanceYearperiodMasterFields = [])
    {
        return new CompanyFinanceYearperiodMaster($this->fakeCompanyFinanceYearperiodMasterData($companyFinanceYearperiodMasterFields));
    }

    /**
     * Get fake data of CompanyFinanceYearperiodMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeCompanyFinanceYearperiodMasterData($companyFinanceYearperiodMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'dateFrom' => $fake->date('Y-m-d H:i:s'),
            'dateTo' => $fake->date('Y-m-d H:i:s'),
            'timeStamp' => $fake->word
        ], $companyFinanceYearperiodMasterFields);
    }
}

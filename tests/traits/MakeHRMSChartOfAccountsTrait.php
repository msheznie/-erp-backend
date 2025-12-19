<?php

use Faker\Factory as Faker;
use App\Models\HRMSChartOfAccounts;
use App\Repositories\HRMSChartOfAccountsRepository;

trait MakeHRMSChartOfAccountsTrait
{
    /**
     * Create fake instance of HRMSChartOfAccounts and save it in database
     *
     * @param array $hRMSChartOfAccountsFields
     * @return HRMSChartOfAccounts
     */
    public function makeHRMSChartOfAccounts($hRMSChartOfAccountsFields = [])
    {
        /** @var HRMSChartOfAccountsRepository $hRMSChartOfAccountsRepo */
        $hRMSChartOfAccountsRepo = App::make(HRMSChartOfAccountsRepository::class);
        $theme = $this->fakeHRMSChartOfAccountsData($hRMSChartOfAccountsFields);
        return $hRMSChartOfAccountsRepo->create($theme);
    }

    /**
     * Get fake instance of HRMSChartOfAccounts
     *
     * @param array $hRMSChartOfAccountsFields
     * @return HRMSChartOfAccounts
     */
    public function fakeHRMSChartOfAccounts($hRMSChartOfAccountsFields = [])
    {
        return new HRMSChartOfAccounts($this->fakeHRMSChartOfAccountsData($hRMSChartOfAccountsFields));
    }

    /**
     * Get fake data of HRMSChartOfAccounts
     *
     * @param array $postFields
     * @return array
     */
    public function fakeHRMSChartOfAccountsData($hRMSChartOfAccountsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'AccountCode' => $fake->word,
            'AccountDescription' => $fake->word,
            'empGroup' => $fake->randomDigitNotNull,
            'createdPcID' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $hRMSChartOfAccountsFields);
    }
}

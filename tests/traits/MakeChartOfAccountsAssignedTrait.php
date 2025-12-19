<?php

use Faker\Factory as Faker;
use App\Models\ChartOfAccountsAssigned;
use App\Repositories\ChartOfAccountsAssignedRepository;

trait MakeChartOfAccountsAssignedTrait
{
    /**
     * Create fake instance of ChartOfAccountsAssigned and save it in database
     *
     * @param array $chartOfAccountsAssignedFields
     * @return ChartOfAccountsAssigned
     */
    public function makeChartOfAccountsAssigned($chartOfAccountsAssignedFields = [])
    {
        /** @var ChartOfAccountsAssignedRepository $chartOfAccountsAssignedRepo */
        $chartOfAccountsAssignedRepo = App::make(ChartOfAccountsAssignedRepository::class);
        $theme = $this->fakeChartOfAccountsAssignedData($chartOfAccountsAssignedFields);
        return $chartOfAccountsAssignedRepo->create($theme);
    }

    /**
     * Get fake instance of ChartOfAccountsAssigned
     *
     * @param array $chartOfAccountsAssignedFields
     * @return ChartOfAccountsAssigned
     */
    public function fakeChartOfAccountsAssigned($chartOfAccountsAssignedFields = [])
    {
        return new ChartOfAccountsAssigned($this->fakeChartOfAccountsAssignedData($chartOfAccountsAssignedFields));
    }

    /**
     * Get fake data of ChartOfAccountsAssigned
     *
     * @param array $postFields
     * @return array
     */
    public function fakeChartOfAccountsAssignedData($chartOfAccountsAssignedFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'chartOfAccountSystemID' => $fake->randomDigitNotNull,
            'AccountCode' => $fake->word,
            'AccountDescription' => $fake->text,
            'masterAccount' => $fake->word,
            'catogaryBLorPLID' => $fake->randomDigitNotNull,
            'catogaryBLorPL' => $fake->word,
            'controllAccountYN' => $fake->randomDigitNotNull,
            'controlAccountsSystemID' => $fake->randomDigitNotNull,
            'controlAccounts' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'isActive' => $fake->randomDigitNotNull,
            'isAssigned' => $fake->randomDigitNotNull,
            'isBank' => $fake->randomDigitNotNull,
            'AllocationID' => $fake->randomDigitNotNull,
            'relatedPartyYN' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $chartOfAccountsAssignedFields);
    }
}

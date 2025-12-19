<?php

use Faker\Factory as Faker;
use App\Models\ChartOfAccount;
use App\Repositories\ChartOfAccountRepository;

trait MakeChartOfAccountTrait
{
    /**
     * Create fake instance of ChartOfAccount and save it in database
     *
     * @param array $chartOfAccountFields
     * @return ChartOfAccount
     */
    public function makeChartOfAccount($chartOfAccountFields = [])
    {
        /** @var ChartOfAccountRepository $chartOfAccountRepo */
        $chartOfAccountRepo = App::make(ChartOfAccountRepository::class);
        $theme = $this->fakeChartOfAccountData($chartOfAccountFields);
        return $chartOfAccountRepo->create($theme);
    }

    /**
     * Get fake instance of ChartOfAccount
     *
     * @param array $chartOfAccountFields
     * @return ChartOfAccount
     */
    public function fakeChartOfAccount($chartOfAccountFields = [])
    {
        return new ChartOfAccount($this->fakeChartOfAccountData($chartOfAccountFields));
    }

    /**
     * Get fake data of ChartOfAccount
     *
     * @param array $postFields
     * @return array
     */
    public function fakeChartOfAccountData($chartOfAccountFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'AccountCode' => $fake->word,
            'AccountDescription' => $fake->text,
            'masterAccount' => $fake->word,
            'catogaryBLorPL' => $fake->word,
            'controllAccountYN' => $fake->randomDigitNotNull,
            'controlAccounts' => $fake->word,
            'isApproved' => $fake->randomDigitNotNull,
            'approvedBy' => $fake->word,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedComment' => $fake->text,
            'isActive' => $fake->randomDigitNotNull,
            'isBank' => $fake->randomDigitNotNull,
            'AllocationID' => $fake->randomDigitNotNull,
            'relatedPartyYN' => $fake->randomDigitNotNull,
            'interCompanyID' => $fake->word,
            'createdPcID' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $chartOfAccountFields);
    }
}

<?php

use Faker\Factory as Faker;
use App\Models\ChartOfAccountsRefferedBack;
use App\Repositories\ChartOfAccountsRefferedBackRepository;

trait MakeChartOfAccountsRefferedBackTrait
{
    /**
     * Create fake instance of ChartOfAccountsRefferedBack and save it in database
     *
     * @param array $chartOfAccountsRefferedBackFields
     * @return ChartOfAccountsRefferedBack
     */
    public function makeChartOfAccountsRefferedBack($chartOfAccountsRefferedBackFields = [])
    {
        /** @var ChartOfAccountsRefferedBackRepository $chartOfAccountsRefferedBackRepo */
        $chartOfAccountsRefferedBackRepo = App::make(ChartOfAccountsRefferedBackRepository::class);
        $theme = $this->fakeChartOfAccountsRefferedBackData($chartOfAccountsRefferedBackFields);
        return $chartOfAccountsRefferedBackRepo->create($theme);
    }

    /**
     * Get fake instance of ChartOfAccountsRefferedBack
     *
     * @param array $chartOfAccountsRefferedBackFields
     * @return ChartOfAccountsRefferedBack
     */
    public function fakeChartOfAccountsRefferedBack($chartOfAccountsRefferedBackFields = [])
    {
        return new ChartOfAccountsRefferedBack($this->fakeChartOfAccountsRefferedBackData($chartOfAccountsRefferedBackFields));
    }

    /**
     * Get fake data of ChartOfAccountsRefferedBack
     *
     * @param array $postFields
     * @return array
     */
    public function fakeChartOfAccountsRefferedBackData($chartOfAccountsRefferedBackFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'chartOfAccountSystemID' => $fake->randomDigitNotNull,
            'primaryCompanySystemID' => $fake->randomDigitNotNull,
            'primaryCompanyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'AccountCode' => $fake->word,
            'AccountDescription' => $fake->text,
            'masterAccount' => $fake->word,
            'catogaryBLorPLID' => $fake->randomDigitNotNull,
            'catogaryBLorPL' => $fake->word,
            'controllAccountYN' => $fake->randomDigitNotNull,
            'controlAccountsSystemID' => $fake->randomDigitNotNull,
            'controlAccounts' => $fake->word,
            'isApproved' => $fake->randomDigitNotNull,
            'approvedBySystemID' => $fake->randomDigitNotNull,
            'approvedBy' => $fake->word,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedComment' => $fake->text,
            'isActive' => $fake->randomDigitNotNull,
            'isBank' => $fake->randomDigitNotNull,
            'AllocationID' => $fake->randomDigitNotNull,
            'relatedPartyYN' => $fake->randomDigitNotNull,
            'interCompanySystemID' => $fake->randomDigitNotNull,
            'interCompanyID' => $fake->word,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedEmpID' => $fake->word,
            'confirmedEmpName' => $fake->word,
            'confirmedEmpDate' => $fake->date('Y-m-d H:i:s'),
            'isMasterAccount' => $fake->randomDigitNotNull,
            'RollLevForApp_curr' => $fake->randomDigitNotNull,
            'refferedBackYN' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->randomDigitNotNull,
            'createdPcID' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $chartOfAccountsRefferedBackFields);
    }
}

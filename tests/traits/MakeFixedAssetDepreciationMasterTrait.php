<?php

use Faker\Factory as Faker;
use App\Models\FixedAssetDepreciationMaster;
use App\Repositories\FixedAssetDepreciationMasterRepository;

trait MakeFixedAssetDepreciationMasterTrait
{
    /**
     * Create fake instance of FixedAssetDepreciationMaster and save it in database
     *
     * @param array $fixedAssetDepreciationMasterFields
     * @return FixedAssetDepreciationMaster
     */
    public function makeFixedAssetDepreciationMaster($fixedAssetDepreciationMasterFields = [])
    {
        /** @var FixedAssetDepreciationMasterRepository $fixedAssetDepreciationMasterRepo */
        $fixedAssetDepreciationMasterRepo = App::make(FixedAssetDepreciationMasterRepository::class);
        $theme = $this->fakeFixedAssetDepreciationMasterData($fixedAssetDepreciationMasterFields);
        return $fixedAssetDepreciationMasterRepo->create($theme);
    }

    /**
     * Get fake instance of FixedAssetDepreciationMaster
     *
     * @param array $fixedAssetDepreciationMasterFields
     * @return FixedAssetDepreciationMaster
     */
    public function fakeFixedAssetDepreciationMaster($fixedAssetDepreciationMasterFields = [])
    {
        return new FixedAssetDepreciationMaster($this->fakeFixedAssetDepreciationMasterData($fixedAssetDepreciationMasterFields));
    }

    /**
     * Get fake data of FixedAssetDepreciationMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFixedAssetDepreciationMasterData($fixedAssetDepreciationMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'serialNo' => $fake->randomDigitNotNull,
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateFrom' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateTo' => $fake->date('Y-m-d H:i:s'),
            'depCode' => $fake->word,
            'depDate' => $fake->date('Y-m-d H:i:s'),
            'depMonthYear' => $fake->word,
            'depLocalCur' => $fake->randomDigitNotNull,
            'depAmountLocal' => $fake->randomDigitNotNull,
            'depRptCur' => $fake->randomDigitNotNull,
            'depAmountRpt' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByEmpName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'createdUserID' => $fake->word,
            'createdPCID' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $fixedAssetDepreciationMasterFields);
    }
}

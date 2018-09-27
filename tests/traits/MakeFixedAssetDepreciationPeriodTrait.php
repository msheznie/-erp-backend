<?php

use Faker\Factory as Faker;
use App\Models\FixedAssetDepreciationPeriod;
use App\Repositories\FixedAssetDepreciationPeriodRepository;

trait MakeFixedAssetDepreciationPeriodTrait
{
    /**
     * Create fake instance of FixedAssetDepreciationPeriod and save it in database
     *
     * @param array $fixedAssetDepreciationPeriodFields
     * @return FixedAssetDepreciationPeriod
     */
    public function makeFixedAssetDepreciationPeriod($fixedAssetDepreciationPeriodFields = [])
    {
        /** @var FixedAssetDepreciationPeriodRepository $fixedAssetDepreciationPeriodRepo */
        $fixedAssetDepreciationPeriodRepo = App::make(FixedAssetDepreciationPeriodRepository::class);
        $theme = $this->fakeFixedAssetDepreciationPeriodData($fixedAssetDepreciationPeriodFields);
        return $fixedAssetDepreciationPeriodRepo->create($theme);
    }

    /**
     * Get fake instance of FixedAssetDepreciationPeriod
     *
     * @param array $fixedAssetDepreciationPeriodFields
     * @return FixedAssetDepreciationPeriod
     */
    public function fakeFixedAssetDepreciationPeriod($fixedAssetDepreciationPeriodFields = [])
    {
        return new FixedAssetDepreciationPeriod($this->fakeFixedAssetDepreciationPeriodData($fixedAssetDepreciationPeriodFields));
    }

    /**
     * Get fake data of FixedAssetDepreciationPeriod
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFixedAssetDepreciationPeriodData($fixedAssetDepreciationPeriodFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'depMasterAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'faFinanceCatID' => $fake->randomDigitNotNull,
            'faMainCategory' => $fake->randomDigitNotNull,
            'faSubCategory' => $fake->randomDigitNotNull,
            'faID' => $fake->randomDigitNotNull,
            'faCode' => $fake->word,
            'assetDescription' => $fake->text,
            'depMonth' => $fake->randomDigitNotNull,
            'depPercent' => $fake->randomDigitNotNull,
            'COSTUNIT' => $fake->randomDigitNotNull,
            'costUnitRpt' => $fake->randomDigitNotNull,
            'FYID' => $fake->randomDigitNotNull,
            'depForFYStartDate' => $fake->date('Y-m-d H:i:s'),
            'depForFYEndDate' => $fake->date('Y-m-d H:i:s'),
            'FYperiodID' => $fake->randomDigitNotNull,
            'depForFYperiodStartDate' => $fake->date('Y-m-d H:i:s'),
            'depForFYperiodEndDate' => $fake->date('Y-m-d H:i:s'),
            'depMonthYear' => $fake->word,
            'depAmountLocalCurr' => $fake->randomDigitNotNull,
            'depAmountLocal' => $fake->randomDigitNotNull,
            'depAmountRptCurr' => $fake->randomDigitNotNull,
            'depAmountRpt' => $fake->randomDigitNotNull,
            'depDoneYN' => $fake->randomDigitNotNull,
            'createdBy' => $fake->word,
            'createdPCid' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $fixedAssetDepreciationPeriodFields);
    }
}

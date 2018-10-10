<?php

use Faker\Factory as Faker;
use App\Models\AssetDepreciationPeriod;
use App\Repositories\AssetDepreciationPeriodRepository;

trait MakeAssetDepreciationPeriodTrait
{
    /**
     * Create fake instance of AssetDepreciationPeriod and save it in database
     *
     * @param array $assetDepreciationPeriodFields
     * @return AssetDepreciationPeriod
     */
    public function makeAssetDepreciationPeriod($assetDepreciationPeriodFields = [])
    {
        /** @var AssetDepreciationPeriodRepository $assetDepreciationPeriodRepo */
        $assetDepreciationPeriodRepo = App::make(AssetDepreciationPeriodRepository::class);
        $theme = $this->fakeAssetDepreciationPeriodData($assetDepreciationPeriodFields);
        return $assetDepreciationPeriodRepo->create($theme);
    }

    /**
     * Get fake instance of AssetDepreciationPeriod
     *
     * @param array $assetDepreciationPeriodFields
     * @return AssetDepreciationPeriod
     */
    public function fakeAssetDepreciationPeriod($assetDepreciationPeriodFields = [])
    {
        return new AssetDepreciationPeriod($this->fakeAssetDepreciationPeriodData($assetDepreciationPeriodFields));
    }

    /**
     * Get fake data of AssetDepreciationPeriod
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAssetDepreciationPeriodData($assetDepreciationPeriodFields = [])
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
        ], $assetDepreciationPeriodFields);
    }
}

<?php

use Faker\Factory as Faker;
use App\Models\AssetCapitalization;
use App\Repositories\AssetCapitalizationRepository;

trait MakeAssetCapitalizationTrait
{
    /**
     * Create fake instance of AssetCapitalization and save it in database
     *
     * @param array $assetCapitalizationFields
     * @return AssetCapitalization
     */
    public function makeAssetCapitalization($assetCapitalizationFields = [])
    {
        /** @var AssetCapitalizationRepository $assetCapitalizationRepo */
        $assetCapitalizationRepo = App::make(AssetCapitalizationRepository::class);
        $theme = $this->fakeAssetCapitalizationData($assetCapitalizationFields);
        return $assetCapitalizationRepo->create($theme);
    }

    /**
     * Get fake instance of AssetCapitalization
     *
     * @param array $assetCapitalizationFields
     * @return AssetCapitalization
     */
    public function fakeAssetCapitalization($assetCapitalizationFields = [])
    {
        return new AssetCapitalization($this->fakeAssetCapitalizationData($assetCapitalizationFields));
    }

    /**
     * Get fake data of AssetCapitalization
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAssetCapitalizationData($assetCapitalizationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'documentDate' => $fake->date('Y-m-d H:i:s'),
            'companyFinanceYearID' => $fake->randomDigitNotNull,
            'serialNo' => $fake->randomDigitNotNull,
            'FYBiggin' => $fake->date('Y-m-d H:i:s'),
            'FYEnd' => $fake->date('Y-m-d H:i:s'),
            'companyFinancePeriodID' => $fake->randomDigitNotNull,
            'FYPeriodDateFrom' => $fake->date('Y-m-d H:i:s'),
            'FYPeriodDateTo' => $fake->date('Y-m-d H:i:s'),
            'narration' => $fake->text,
            'allocationTypeID' => $fake->word,
            'faCatID' => $fake->randomDigitNotNull,
            'faID' => $fake->randomDigitNotNull,
            'assetNBVLocal' => $fake->randomDigitNotNull,
            'assetNBVRpt' => $fake->randomDigitNotNull,
            'confirmedYN' => $fake->randomDigitNotNull,
            'confirmedByEmpSystemID' => $fake->randomDigitNotNull,
            'confirmedByEmpID' => $fake->word,
            'confirmedByName' => $fake->word,
            'confirmedDate' => $fake->date('Y-m-d H:i:s'),
            'approved' => $fake->randomDigitNotNull,
            'approvedDate' => $fake->date('Y-m-d H:i:s'),
            'approvedByUserID' => $fake->word,
            'approvedByUserSystemID' => $fake->randomDigitNotNull,
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $assetCapitalizationFields);
    }
}

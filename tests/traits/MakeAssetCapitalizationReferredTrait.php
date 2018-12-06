<?php

use Faker\Factory as Faker;
use App\Models\AssetCapitalizationReferred;
use App\Repositories\AssetCapitalizationReferredRepository;

trait MakeAssetCapitalizationReferredTrait
{
    /**
     * Create fake instance of AssetCapitalizationReferred and save it in database
     *
     * @param array $assetCapitalizationReferredFields
     * @return AssetCapitalizationReferred
     */
    public function makeAssetCapitalizationReferred($assetCapitalizationReferredFields = [])
    {
        /** @var AssetCapitalizationReferredRepository $assetCapitalizationReferredRepo */
        $assetCapitalizationReferredRepo = App::make(AssetCapitalizationReferredRepository::class);
        $theme = $this->fakeAssetCapitalizationReferredData($assetCapitalizationReferredFields);
        return $assetCapitalizationReferredRepo->create($theme);
    }

    /**
     * Get fake instance of AssetCapitalizationReferred
     *
     * @param array $assetCapitalizationReferredFields
     * @return AssetCapitalizationReferred
     */
    public function fakeAssetCapitalizationReferred($assetCapitalizationReferredFields = [])
    {
        return new AssetCapitalizationReferred($this->fakeAssetCapitalizationReferredData($assetCapitalizationReferredFields));
    }

    /**
     * Get fake data of AssetCapitalizationReferred
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAssetCapitalizationReferredData($assetCapitalizationReferredFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'capitalizationID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'documentSystemID' => $fake->randomDigitNotNull,
            'documentID' => $fake->word,
            'capitalizationCode' => $fake->word,
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
            'contraAccountSystemID' => $fake->randomDigitNotNull,
            'contraAccountGLCode' => $fake->word,
            'assetNBVLocal' => $fake->randomDigitNotNull,
            'assetNBVRpt' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->word,
            'refferedBackYN' => $fake->word,
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
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'cancelYN' => $fake->randomDigitNotNull,
            'cancelComment' => $fake->text,
            'cancelDate' => $fake->date('Y-m-d H:i:s'),
            'cancelledByEmpSystemID' => $fake->randomDigitNotNull,
            'canceledByEmpID' => $fake->word,
            'canceledByEmpName' => $fake->word,
            'RollLevForApp_curr' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $assetCapitalizationReferredFields);
    }
}

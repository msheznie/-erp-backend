<?php

use Faker\Factory as Faker;
use App\Models\AssetCapitalizatioDetReferred;
use App\Repositories\AssetCapitalizatioDetReferredRepository;

trait MakeAssetCapitalizatioDetReferredTrait
{
    /**
     * Create fake instance of AssetCapitalizatioDetReferred and save it in database
     *
     * @param array $assetCapitalizatioDetReferredFields
     * @return AssetCapitalizatioDetReferred
     */
    public function makeAssetCapitalizatioDetReferred($assetCapitalizatioDetReferredFields = [])
    {
        /** @var AssetCapitalizatioDetReferredRepository $assetCapitalizatioDetReferredRepo */
        $assetCapitalizatioDetReferredRepo = App::make(AssetCapitalizatioDetReferredRepository::class);
        $theme = $this->fakeAssetCapitalizatioDetReferredData($assetCapitalizatioDetReferredFields);
        return $assetCapitalizatioDetReferredRepo->create($theme);
    }

    /**
     * Get fake instance of AssetCapitalizatioDetReferred
     *
     * @param array $assetCapitalizatioDetReferredFields
     * @return AssetCapitalizatioDetReferred
     */
    public function fakeAssetCapitalizatioDetReferred($assetCapitalizatioDetReferredFields = [])
    {
        return new AssetCapitalizatioDetReferred($this->fakeAssetCapitalizatioDetReferredData($assetCapitalizatioDetReferredFields));
    }

    /**
     * Get fake data of AssetCapitalizatioDetReferred
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAssetCapitalizatioDetReferredData($assetCapitalizatioDetReferredFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'capitalizationDetailID' => $fake->randomDigitNotNull,
            'capitalizationID' => $fake->randomDigitNotNull,
            'faID' => $fake->randomDigitNotNull,
            'faCode' => $fake->word,
            'assetDescription' => $fake->word,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'dateAQ' => $fake->word,
            'assetNBVLocal' => $fake->randomDigitNotNull,
            'assetNBVRpt' => $fake->randomDigitNotNull,
            'allocatedAmountLocal' => $fake->randomDigitNotNull,
            'allocatedAmountRpt' => $fake->randomDigitNotNull,
            'timesReferred' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $assetCapitalizatioDetReferredFields);
    }
}

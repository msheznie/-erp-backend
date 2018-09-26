<?php

use Faker\Factory as Faker;
use App\Models\AssetCapitalizationDetail;
use App\Repositories\AssetCapitalizationDetailRepository;

trait MakeAssetCapitalizationDetailTrait
{
    /**
     * Create fake instance of AssetCapitalizationDetail and save it in database
     *
     * @param array $assetCapitalizationDetailFields
     * @return AssetCapitalizationDetail
     */
    public function makeAssetCapitalizationDetail($assetCapitalizationDetailFields = [])
    {
        /** @var AssetCapitalizationDetailRepository $assetCapitalizationDetailRepo */
        $assetCapitalizationDetailRepo = App::make(AssetCapitalizationDetailRepository::class);
        $theme = $this->fakeAssetCapitalizationDetailData($assetCapitalizationDetailFields);
        return $assetCapitalizationDetailRepo->create($theme);
    }

    /**
     * Get fake instance of AssetCapitalizationDetail
     *
     * @param array $assetCapitalizationDetailFields
     * @return AssetCapitalizationDetail
     */
    public function fakeAssetCapitalizationDetail($assetCapitalizationDetailFields = [])
    {
        return new AssetCapitalizationDetail($this->fakeAssetCapitalizationDetailData($assetCapitalizationDetailFields));
    }

    /**
     * Get fake data of AssetCapitalizationDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAssetCapitalizationDetailData($assetCapitalizationDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
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
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdPcID' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'modifiedPc' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $assetCapitalizationDetailFields);
    }
}

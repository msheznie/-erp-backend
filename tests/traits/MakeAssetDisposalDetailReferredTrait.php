<?php

use Faker\Factory as Faker;
use App\Models\AssetDisposalDetailReferred;
use App\Repositories\AssetDisposalDetailReferredRepository;

trait MakeAssetDisposalDetailReferredTrait
{
    /**
     * Create fake instance of AssetDisposalDetailReferred and save it in database
     *
     * @param array $assetDisposalDetailReferredFields
     * @return AssetDisposalDetailReferred
     */
    public function makeAssetDisposalDetailReferred($assetDisposalDetailReferredFields = [])
    {
        /** @var AssetDisposalDetailReferredRepository $assetDisposalDetailReferredRepo */
        $assetDisposalDetailReferredRepo = App::make(AssetDisposalDetailReferredRepository::class);
        $theme = $this->fakeAssetDisposalDetailReferredData($assetDisposalDetailReferredFields);
        return $assetDisposalDetailReferredRepo->create($theme);
    }

    /**
     * Get fake instance of AssetDisposalDetailReferred
     *
     * @param array $assetDisposalDetailReferredFields
     * @return AssetDisposalDetailReferred
     */
    public function fakeAssetDisposalDetailReferred($assetDisposalDetailReferredFields = [])
    {
        return new AssetDisposalDetailReferred($this->fakeAssetDisposalDetailReferredData($assetDisposalDetailReferredFields));
    }

    /**
     * Get fake data of AssetDisposalDetailReferred
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAssetDisposalDetailReferredData($assetDisposalDetailReferredFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'assetDisposalDetailAutoID' => $fake->randomDigitNotNull,
            'assetdisposalMasterAutoID' => $fake->randomDigitNotNull,
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'serviceLineSystemID' => $fake->randomDigitNotNull,
            'serviceLineCode' => $fake->word,
            'itemCode' => $fake->randomDigitNotNull,
            'faID' => $fake->randomDigitNotNull,
            'faCode' => $fake->word,
            'faUnitSerialNo' => $fake->word,
            'assetDescription' => $fake->text,
            'COSTUNIT' => $fake->randomDigitNotNull,
            'costUnitRpt' => $fake->randomDigitNotNull,
            'netBookValueLocal' => $fake->randomDigitNotNull,
            'depAmountLocal' => $fake->randomDigitNotNull,
            'depAmountRpt' => $fake->randomDigitNotNull,
            'netBookValueRpt' => $fake->randomDigitNotNull,
            'COSTGLCODESystemID' => $fake->randomDigitNotNull,
            'COSTGLCODE' => $fake->word,
            'ACCDEPGLCODESystemID' => $fake->randomDigitNotNull,
            'ACCDEPGLCODE' => $fake->word,
            'DISPOGLCODESystemID' => $fake->randomDigitNotNull,
            'DISPOGLCODE' => $fake->word,
            'timesReferred' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $assetDisposalDetailReferredFields);
    }
}

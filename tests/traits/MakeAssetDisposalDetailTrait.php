<?php

use Faker\Factory as Faker;
use App\Models\AssetDisposalDetail;
use App\Repositories\AssetDisposalDetailRepository;

trait MakeAssetDisposalDetailTrait
{
    /**
     * Create fake instance of AssetDisposalDetail and save it in database
     *
     * @param array $assetDisposalDetailFields
     * @return AssetDisposalDetail
     */
    public function makeAssetDisposalDetail($assetDisposalDetailFields = [])
    {
        /** @var AssetDisposalDetailRepository $assetDisposalDetailRepo */
        $assetDisposalDetailRepo = App::make(AssetDisposalDetailRepository::class);
        $theme = $this->fakeAssetDisposalDetailData($assetDisposalDetailFields);
        return $assetDisposalDetailRepo->create($theme);
    }

    /**
     * Get fake instance of AssetDisposalDetail
     *
     * @param array $assetDisposalDetailFields
     * @return AssetDisposalDetail
     */
    public function fakeAssetDisposalDetail($assetDisposalDetailFields = [])
    {
        return new AssetDisposalDetail($this->fakeAssetDisposalDetailData($assetDisposalDetailFields));
    }

    /**
     * Get fake data of AssetDisposalDetail
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAssetDisposalDetailData($assetDisposalDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
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
            'COSTGLCODE' => $fake->word,
            'ACCDEPGLCODE' => $fake->word,
            'DISPOGLCODE' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $assetDisposalDetailFields);
    }
}

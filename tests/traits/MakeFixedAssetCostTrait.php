<?php

use Faker\Factory as Faker;
use App\Models\FixedAssetCost;
use App\Repositories\FixedAssetCostRepository;

trait MakeFixedAssetCostTrait
{
    /**
     * Create fake instance of FixedAssetCost and save it in database
     *
     * @param array $fixedAssetCostFields
     * @return FixedAssetCost
     */
    public function makeFixedAssetCost($fixedAssetCostFields = [])
    {
        /** @var FixedAssetCostRepository $fixedAssetCostRepo */
        $fixedAssetCostRepo = App::make(FixedAssetCostRepository::class);
        $theme = $this->fakeFixedAssetCostData($fixedAssetCostFields);
        return $fixedAssetCostRepo->create($theme);
    }

    /**
     * Get fake instance of FixedAssetCost
     *
     * @param array $fixedAssetCostFields
     * @return FixedAssetCost
     */
    public function fakeFixedAssetCost($fixedAssetCostFields = [])
    {
        return new FixedAssetCost($this->fakeFixedAssetCostData($fixedAssetCostFields));
    }

    /**
     * Get fake data of FixedAssetCost
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFixedAssetCostData($fixedAssetCostFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'originDocumentSystemCode' => $fake->randomDigitNotNull,
            'originDocumentID' => $fake->word,
            'itemCode' => $fake->randomDigitNotNull,
            'faID' => $fake->randomDigitNotNull,
            'assetID' => $fake->word,
            'assetDescription' => $fake->text,
            'costDate' => $fake->date('Y-m-d H:i:s'),
            'localCurrencyID' => $fake->randomDigitNotNull,
            'localAmount' => $fake->randomDigitNotNull,
            'rptCurrencyID' => $fake->randomDigitNotNull,
            'rptAmount' => $fake->randomDigitNotNull,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $fixedAssetCostFields);
    }
}

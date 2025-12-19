<?php

use Faker\Factory as Faker;
use App\Models\AssetFinanceCategory;
use App\Repositories\AssetFinanceCategoryRepository;

trait MakeAssetFinanceCategoryTrait
{
    /**
     * Create fake instance of AssetFinanceCategory and save it in database
     *
     * @param array $assetFinanceCategoryFields
     * @return AssetFinanceCategory
     */
    public function makeAssetFinanceCategory($assetFinanceCategoryFields = [])
    {
        /** @var AssetFinanceCategoryRepository $assetFinanceCategoryRepo */
        $assetFinanceCategoryRepo = App::make(AssetFinanceCategoryRepository::class);
        $theme = $this->fakeAssetFinanceCategoryData($assetFinanceCategoryFields);
        return $assetFinanceCategoryRepo->create($theme);
    }

    /**
     * Get fake instance of AssetFinanceCategory
     *
     * @param array $assetFinanceCategoryFields
     * @return AssetFinanceCategory
     */
    public function fakeAssetFinanceCategory($assetFinanceCategoryFields = [])
    {
        return new AssetFinanceCategory($this->fakeAssetFinanceCategoryData($assetFinanceCategoryFields));
    }

    /**
     * Get fake data of AssetFinanceCategory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAssetFinanceCategoryData($assetFinanceCategoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'financeCatDescription' => $fake->word,
            'COSTGLCODE' => $fake->word,
            'ACCDEPGLCODE' => $fake->word,
            'DEPGLCODE' => $fake->word,
            'DISPOGLCODE' => $fake->word,
            'isActive' => $fake->randomDigitNotNull,
            'sortOrder' => $fake->randomDigitNotNull,
            'createdPcID' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $assetFinanceCategoryFields);
    }
}

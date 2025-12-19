<?php

use Faker\Factory as Faker;
use App\Models\FixedAssetCategorySub;
use App\Repositories\FixedAssetCategorySubRepository;

trait MakeFixedAssetCategorySubTrait
{
    /**
     * Create fake instance of FixedAssetCategorySub and save it in database
     *
     * @param array $fixedAssetCategorySubFields
     * @return FixedAssetCategorySub
     */
    public function makeFixedAssetCategorySub($fixedAssetCategorySubFields = [])
    {
        /** @var FixedAssetCategorySubRepository $fixedAssetCategorySubRepo */
        $fixedAssetCategorySubRepo = App::make(FixedAssetCategorySubRepository::class);
        $theme = $this->fakeFixedAssetCategorySubData($fixedAssetCategorySubFields);
        return $fixedAssetCategorySubRepo->create($theme);
    }

    /**
     * Get fake instance of FixedAssetCategorySub
     *
     * @param array $fixedAssetCategorySubFields
     * @return FixedAssetCategorySub
     */
    public function fakeFixedAssetCategorySub($fixedAssetCategorySubFields = [])
    {
        return new FixedAssetCategorySub($this->fakeFixedAssetCategorySubData($fixedAssetCategorySubFields));
    }

    /**
     * Get fake data of FixedAssetCategorySub
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFixedAssetCategorySubData($fixedAssetCategorySubFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'catDescription' => $fake->word,
            'faCatID' => $fake->randomDigitNotNull,
            'mainCatDescription' => $fake->word,
            'suCatLevel' => $fake->randomDigitNotNull,
            'isActive' => $fake->randomDigitNotNull,
            'createdPcID' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPc' => $fake->word,
            'modifiedUser' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $fixedAssetCategorySubFields);
    }
}

<?php

use Faker\Factory as Faker;
use App\Models\FixedAssetCategory;
use App\Repositories\FixedAssetCategoryRepository;

trait MakeFixedAssetCategoryTrait
{
    /**
     * Create fake instance of FixedAssetCategory and save it in database
     *
     * @param array $fixedAssetCategoryFields
     * @return FixedAssetCategory
     */
    public function makeFixedAssetCategory($fixedAssetCategoryFields = [])
    {
        /** @var FixedAssetCategoryRepository $fixedAssetCategoryRepo */
        $fixedAssetCategoryRepo = App::make(FixedAssetCategoryRepository::class);
        $theme = $this->fakeFixedAssetCategoryData($fixedAssetCategoryFields);
        return $fixedAssetCategoryRepo->create($theme);
    }

    /**
     * Get fake instance of FixedAssetCategory
     *
     * @param array $fixedAssetCategoryFields
     * @return FixedAssetCategory
     */
    public function fakeFixedAssetCategory($fixedAssetCategoryFields = [])
    {
        return new FixedAssetCategory($this->fakeFixedAssetCategoryData($fixedAssetCategoryFields));
    }

    /**
     * Get fake data of FixedAssetCategory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeFixedAssetCategoryData($fixedAssetCategoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companySystemID' => $fake->randomDigitNotNull,
            'companyID' => $fake->word,
            'catDescription' => $fake->word,
            'isActive' => $fake->randomDigitNotNull,
            'createdPcID' => $fake->word,
            'createdUserGroup' => $fake->word,
            'createdUserSystemID' => $fake->randomDigitNotNull,
            'createdUserID' => $fake->word,
            'createdDateTime' => $fake->date('Y-m-d H:i:s'),
            'modifiedPc' => $fake->word,
            'modifiedUserSystemID' => $fake->randomDigitNotNull,
            'modifiedUser' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $fixedAssetCategoryFields);
    }
}

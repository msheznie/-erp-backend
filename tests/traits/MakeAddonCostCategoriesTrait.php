<?php

use Faker\Factory as Faker;
use App\Models\AddonCostCategories;
use App\Repositories\AddonCostCategoriesRepository;

trait MakeAddonCostCategoriesTrait
{
    /**
     * Create fake instance of AddonCostCategories and save it in database
     *
     * @param array $addonCostCategoriesFields
     * @return AddonCostCategories
     */
    public function makeAddonCostCategories($addonCostCategoriesFields = [])
    {
        /** @var AddonCostCategoriesRepository $addonCostCategoriesRepo */
        $addonCostCategoriesRepo = App::make(AddonCostCategoriesRepository::class);
        $theme = $this->fakeAddonCostCategoriesData($addonCostCategoriesFields);
        return $addonCostCategoriesRepo->create($theme);
    }

    /**
     * Get fake instance of AddonCostCategories
     *
     * @param array $addonCostCategoriesFields
     * @return AddonCostCategories
     */
    public function fakeAddonCostCategories($addonCostCategoriesFields = [])
    {
        return new AddonCostCategories($this->fakeAddonCostCategoriesData($addonCostCategoriesFields));
    }

    /**
     * Get fake data of AddonCostCategories
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAddonCostCategoriesData($addonCostCategoriesFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'costCatDes' => $fake->word,
            'glCode' => $fake->word,
            'timesStamp' => $fake->date('Y-m-d H:i:s')
        ], $addonCostCategoriesFields);
    }
}

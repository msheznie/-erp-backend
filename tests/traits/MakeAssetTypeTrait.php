<?php

use Faker\Factory as Faker;
use App\Models\AssetType;
use App\Repositories\AssetTypeRepository;

trait MakeAssetTypeTrait
{
    /**
     * Create fake instance of AssetType and save it in database
     *
     * @param array $assetTypeFields
     * @return AssetType
     */
    public function makeAssetType($assetTypeFields = [])
    {
        /** @var AssetTypeRepository $assetTypeRepo */
        $assetTypeRepo = App::make(AssetTypeRepository::class);
        $theme = $this->fakeAssetTypeData($assetTypeFields);
        return $assetTypeRepo->create($theme);
    }

    /**
     * Get fake instance of AssetType
     *
     * @param array $assetTypeFields
     * @return AssetType
     */
    public function fakeAssetType($assetTypeFields = [])
    {
        return new AssetType($this->fakeAssetTypeData($assetTypeFields));
    }

    /**
     * Get fake data of AssetType
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAssetTypeData($assetTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'typeDes' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $assetTypeFields);
    }
}

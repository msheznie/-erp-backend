<?php

use Faker\Factory as Faker;
use App\Models\AssetDisposalType;
use App\Repositories\AssetDisposalTypeRepository;

trait MakeAssetDisposalTypeTrait
{
    /**
     * Create fake instance of AssetDisposalType and save it in database
     *
     * @param array $assetDisposalTypeFields
     * @return AssetDisposalType
     */
    public function makeAssetDisposalType($assetDisposalTypeFields = [])
    {
        /** @var AssetDisposalTypeRepository $assetDisposalTypeRepo */
        $assetDisposalTypeRepo = App::make(AssetDisposalTypeRepository::class);
        $theme = $this->fakeAssetDisposalTypeData($assetDisposalTypeFields);
        return $assetDisposalTypeRepo->create($theme);
    }

    /**
     * Get fake instance of AssetDisposalType
     *
     * @param array $assetDisposalTypeFields
     * @return AssetDisposalType
     */
    public function fakeAssetDisposalType($assetDisposalTypeFields = [])
    {
        return new AssetDisposalType($this->fakeAssetDisposalTypeData($assetDisposalTypeFields));
    }

    /**
     * Get fake data of AssetDisposalType
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAssetDisposalTypeData($assetDisposalTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'typeDescription' => $fake->word
        ], $assetDisposalTypeFields);
    }
}

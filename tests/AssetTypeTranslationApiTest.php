<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AssetTypeTranslation;

class AssetTypeTranslationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_asset_type_translation()
    {
        $assetTypeTranslation = factory(AssetTypeTranslation::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/asset_type_translations', $assetTypeTranslation
        );

        $this->assertApiResponse($assetTypeTranslation);
    }

    /**
     * @test
     */
    public function test_read_asset_type_translation()
    {
        $assetTypeTranslation = factory(AssetTypeTranslation::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/asset_type_translations/'.$assetTypeTranslation->id
        );

        $this->assertApiResponse($assetTypeTranslation->toArray());
    }

    /**
     * @test
     */
    public function test_update_asset_type_translation()
    {
        $assetTypeTranslation = factory(AssetTypeTranslation::class)->create();
        $editedAssetTypeTranslation = factory(AssetTypeTranslation::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/asset_type_translations/'.$assetTypeTranslation->id,
            $editedAssetTypeTranslation
        );

        $this->assertApiResponse($editedAssetTypeTranslation);
    }

    /**
     * @test
     */
    public function test_delete_asset_type_translation()
    {
        $assetTypeTranslation = factory(AssetTypeTranslation::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/asset_type_translations/'.$assetTypeTranslation->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/asset_type_translations/'.$assetTypeTranslation->id
        );

        $this->response->assertStatus(404);
    }
}

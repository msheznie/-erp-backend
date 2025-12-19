<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AssetVerification;

class AssetVerificationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_asset_verification()
    {
        $assetVerification = factory(AssetVerification::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/asset_verifications', $assetVerification
        );

        $this->assertApiResponse($assetVerification);
    }

    /**
     * @test
     */
    public function test_read_asset_verification()
    {
        $assetVerification = factory(AssetVerification::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/asset_verifications/'.$assetVerification->id
        );

        $this->assertApiResponse($assetVerification->toArray());
    }

    /**
     * @test
     */
    public function test_update_asset_verification()
    {
        $assetVerification = factory(AssetVerification::class)->create();
        $editedAssetVerification = factory(AssetVerification::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/asset_verifications/'.$assetVerification->id,
            $editedAssetVerification
        );

        $this->assertApiResponse($editedAssetVerification);
    }

    /**
     * @test
     */
    public function test_delete_asset_verification()
    {
        $assetVerification = factory(AssetVerification::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/asset_verifications/'.$assetVerification->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/asset_verifications/'.$assetVerification->id
        );

        $this->response->assertStatus(404);
    }
}

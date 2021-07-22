<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AssetRequest;

class AssetRequestApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_asset_request()
    {
        $assetRequest = factory(AssetRequest::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/asset_requests', $assetRequest
        );

        $this->assertApiResponse($assetRequest);
    }

    /**
     * @test
     */
    public function test_read_asset_request()
    {
        $assetRequest = factory(AssetRequest::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/asset_requests/'.$assetRequest->id
        );

        $this->assertApiResponse($assetRequest->toArray());
    }

    /**
     * @test
     */
    public function test_update_asset_request()
    {
        $assetRequest = factory(AssetRequest::class)->create();
        $editedAssetRequest = factory(AssetRequest::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/asset_requests/'.$assetRequest->id,
            $editedAssetRequest
        );

        $this->assertApiResponse($editedAssetRequest);
    }

    /**
     * @test
     */
    public function test_delete_asset_request()
    {
        $assetRequest = factory(AssetRequest::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/asset_requests/'.$assetRequest->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/asset_requests/'.$assetRequest->id
        );

        $this->response->assertStatus(404);
    }
}

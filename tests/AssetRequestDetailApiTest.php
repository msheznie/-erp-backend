<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AssetRequestDetail;

class AssetRequestDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_asset_request_detail()
    {
        $assetRequestDetail = factory(AssetRequestDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/asset_request_details', $assetRequestDetail
        );

        $this->assertApiResponse($assetRequestDetail);
    }

    /**
     * @test
     */
    public function test_read_asset_request_detail()
    {
        $assetRequestDetail = factory(AssetRequestDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/asset_request_details/'.$assetRequestDetail->id
        );

        $this->assertApiResponse($assetRequestDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_asset_request_detail()
    {
        $assetRequestDetail = factory(AssetRequestDetail::class)->create();
        $editedAssetRequestDetail = factory(AssetRequestDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/asset_request_details/'.$assetRequestDetail->id,
            $editedAssetRequestDetail
        );

        $this->assertApiResponse($editedAssetRequestDetail);
    }

    /**
     * @test
     */
    public function test_delete_asset_request_detail()
    {
        $assetRequestDetail = factory(AssetRequestDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/asset_request_details/'.$assetRequestDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/asset_request_details/'.$assetRequestDetail->id
        );

        $this->response->assertStatus(404);
    }
}

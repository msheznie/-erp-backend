<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AssetVerificationDetail;

class AssetVerificationDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_asset_verification_detail()
    {
        $assetVerificationDetail = factory(AssetVerificationDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/asset_verification_details', $assetVerificationDetail
        );

        $this->assertApiResponse($assetVerificationDetail);
    }

    /**
     * @test
     */
    public function test_read_asset_verification_detail()
    {
        $assetVerificationDetail = factory(AssetVerificationDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/asset_verification_details/'.$assetVerificationDetail->id
        );

        $this->assertApiResponse($assetVerificationDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_asset_verification_detail()
    {
        $assetVerificationDetail = factory(AssetVerificationDetail::class)->create();
        $editedAssetVerificationDetail = factory(AssetVerificationDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/asset_verification_details/'.$assetVerificationDetail->id,
            $editedAssetVerificationDetail
        );

        $this->assertApiResponse($editedAssetVerificationDetail);
    }

    /**
     * @test
     */
    public function test_delete_asset_verification_detail()
    {
        $assetVerificationDetail = factory(AssetVerificationDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/asset_verification_details/'.$assetVerificationDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/asset_verification_details/'.$assetVerificationDetail->id
        );

        $this->response->assertStatus(404);
    }
}

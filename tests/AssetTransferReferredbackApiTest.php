<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AssetTransferReferredback;

class AssetTransferReferredbackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_asset_transfer_referredback()
    {
        $assetTransferReferredback = factory(AssetTransferReferredback::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/asset_transfer_referredbacks', $assetTransferReferredback
        );

        $this->assertApiResponse($assetTransferReferredback);
    }

    /**
     * @test
     */
    public function test_read_asset_transfer_referredback()
    {
        $assetTransferReferredback = factory(AssetTransferReferredback::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/asset_transfer_referredbacks/'.$assetTransferReferredback->id
        );

        $this->assertApiResponse($assetTransferReferredback->toArray());
    }

    /**
     * @test
     */
    public function test_update_asset_transfer_referredback()
    {
        $assetTransferReferredback = factory(AssetTransferReferredback::class)->create();
        $editedAssetTransferReferredback = factory(AssetTransferReferredback::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/asset_transfer_referredbacks/'.$assetTransferReferredback->id,
            $editedAssetTransferReferredback
        );

        $this->assertApiResponse($editedAssetTransferReferredback);
    }

    /**
     * @test
     */
    public function test_delete_asset_transfer_referredback()
    {
        $assetTransferReferredback = factory(AssetTransferReferredback::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/asset_transfer_referredbacks/'.$assetTransferReferredback->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/asset_transfer_referredbacks/'.$assetTransferReferredback->id
        );

        $this->response->assertStatus(404);
    }
}

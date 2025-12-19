<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\AssetWarranty;

class AssetWarrantyApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_asset_warranty()
    {
        $assetWarranty = factory(AssetWarranty::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/asset_warranties', $assetWarranty
        );

        $this->assertApiResponse($assetWarranty);
    }

    /**
     * @test
     */
    public function test_read_asset_warranty()
    {
        $assetWarranty = factory(AssetWarranty::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/asset_warranties/'.$assetWarranty->id
        );

        $this->assertApiResponse($assetWarranty->toArray());
    }

    /**
     * @test
     */
    public function test_update_asset_warranty()
    {
        $assetWarranty = factory(AssetWarranty::class)->create();
        $editedAssetWarranty = factory(AssetWarranty::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/asset_warranties/'.$assetWarranty->id,
            $editedAssetWarranty
        );

        $this->assertApiResponse($editedAssetWarranty);
    }

    /**
     * @test
     */
    public function test_delete_asset_warranty()
    {
        $assetWarranty = factory(AssetWarranty::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/asset_warranties/'.$assetWarranty->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/asset_warranties/'.$assetWarranty->id
        );

        $this->response->assertStatus(404);
    }
}

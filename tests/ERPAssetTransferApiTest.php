<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ERPAssetTransfer;

class ERPAssetTransferApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_e_r_p_asset_transfer()
    {
        $eRPAssetTransfer = factory(ERPAssetTransfer::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/e_r_p_asset_transfers', $eRPAssetTransfer
        );

        $this->assertApiResponse($eRPAssetTransfer);
    }

    /**
     * @test
     */
    public function test_read_e_r_p_asset_transfer()
    {
        $eRPAssetTransfer = factory(ERPAssetTransfer::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/e_r_p_asset_transfers/'.$eRPAssetTransfer->id
        );

        $this->assertApiResponse($eRPAssetTransfer->toArray());
    }

    /**
     * @test
     */
    public function test_update_e_r_p_asset_transfer()
    {
        $eRPAssetTransfer = factory(ERPAssetTransfer::class)->create();
        $editedERPAssetTransfer = factory(ERPAssetTransfer::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/e_r_p_asset_transfers/'.$eRPAssetTransfer->id,
            $editedERPAssetTransfer
        );

        $this->assertApiResponse($editedERPAssetTransfer);
    }

    /**
     * @test
     */
    public function test_delete_e_r_p_asset_transfer()
    {
        $eRPAssetTransfer = factory(ERPAssetTransfer::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/e_r_p_asset_transfers/'.$eRPAssetTransfer->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/e_r_p_asset_transfers/'.$eRPAssetTransfer->id
        );

        $this->response->assertStatus(404);
    }
}

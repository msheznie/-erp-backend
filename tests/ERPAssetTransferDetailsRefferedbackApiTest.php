<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ERPAssetTransferDetailsRefferedback;

class ERPAssetTransferDetailsRefferedbackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_e_r_p_asset_transfer_details_refferedback()
    {
        $eRPAssetTransferDetailsRefferedback = factory(ERPAssetTransferDetailsRefferedback::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/e_r_p_asset_transfer_details_refferedbacks', $eRPAssetTransferDetailsRefferedback
        );

        $this->assertApiResponse($eRPAssetTransferDetailsRefferedback);
    }

    /**
     * @test
     */
    public function test_read_e_r_p_asset_transfer_details_refferedback()
    {
        $eRPAssetTransferDetailsRefferedback = factory(ERPAssetTransferDetailsRefferedback::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/e_r_p_asset_transfer_details_refferedbacks/'.$eRPAssetTransferDetailsRefferedback->id
        );

        $this->assertApiResponse($eRPAssetTransferDetailsRefferedback->toArray());
    }

    /**
     * @test
     */
    public function test_update_e_r_p_asset_transfer_details_refferedback()
    {
        $eRPAssetTransferDetailsRefferedback = factory(ERPAssetTransferDetailsRefferedback::class)->create();
        $editedERPAssetTransferDetailsRefferedback = factory(ERPAssetTransferDetailsRefferedback::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/e_r_p_asset_transfer_details_refferedbacks/'.$eRPAssetTransferDetailsRefferedback->id,
            $editedERPAssetTransferDetailsRefferedback
        );

        $this->assertApiResponse($editedERPAssetTransferDetailsRefferedback);
    }

    /**
     * @test
     */
    public function test_delete_e_r_p_asset_transfer_details_refferedback()
    {
        $eRPAssetTransferDetailsRefferedback = factory(ERPAssetTransferDetailsRefferedback::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/e_r_p_asset_transfer_details_refferedbacks/'.$eRPAssetTransferDetailsRefferedback->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/e_r_p_asset_transfer_details_refferedbacks/'.$eRPAssetTransferDetailsRefferedback->id
        );

        $this->response->assertStatus(404);
    }
}

<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ERPAssetTransferDetail;

class ERPAssetTransferDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_e_r_p_asset_transfer_detail()
    {
        $eRPAssetTransferDetail = factory(ERPAssetTransferDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/e_r_p_asset_transfer_details', $eRPAssetTransferDetail
        );

        $this->assertApiResponse($eRPAssetTransferDetail);
    }

    /**
     * @test
     */
    public function test_read_e_r_p_asset_transfer_detail()
    {
        $eRPAssetTransferDetail = factory(ERPAssetTransferDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/e_r_p_asset_transfer_details/'.$eRPAssetTransferDetail->id
        );

        $this->assertApiResponse($eRPAssetTransferDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_e_r_p_asset_transfer_detail()
    {
        $eRPAssetTransferDetail = factory(ERPAssetTransferDetail::class)->create();
        $editedERPAssetTransferDetail = factory(ERPAssetTransferDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/e_r_p_asset_transfer_details/'.$eRPAssetTransferDetail->id,
            $editedERPAssetTransferDetail
        );

        $this->assertApiResponse($editedERPAssetTransferDetail);
    }

    /**
     * @test
     */
    public function test_delete_e_r_p_asset_transfer_detail()
    {
        $eRPAssetTransferDetail = factory(ERPAssetTransferDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/e_r_p_asset_transfer_details/'.$eRPAssetTransferDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/e_r_p_asset_transfer_details/'.$eRPAssetTransferDetail->id
        );

        $this->response->assertStatus(404);
    }
}

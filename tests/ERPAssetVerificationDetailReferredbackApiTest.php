<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ERPAssetVerificationDetailReferredback;

class ERPAssetVerificationDetailReferredbackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_e_r_p_asset_verification_detail_referredback()
    {
        $eRPAssetVerificationDetailReferredback = factory(ERPAssetVerificationDetailReferredback::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/e_r_p_asset_verification_detail_referredbacks', $eRPAssetVerificationDetailReferredback
        );

        $this->assertApiResponse($eRPAssetVerificationDetailReferredback);
    }

    /**
     * @test
     */
    public function test_read_e_r_p_asset_verification_detail_referredback()
    {
        $eRPAssetVerificationDetailReferredback = factory(ERPAssetVerificationDetailReferredback::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/e_r_p_asset_verification_detail_referredbacks/'.$eRPAssetVerificationDetailReferredback->id
        );

        $this->assertApiResponse($eRPAssetVerificationDetailReferredback->toArray());
    }

    /**
     * @test
     */
    public function test_update_e_r_p_asset_verification_detail_referredback()
    {
        $eRPAssetVerificationDetailReferredback = factory(ERPAssetVerificationDetailReferredback::class)->create();
        $editedERPAssetVerificationDetailReferredback = factory(ERPAssetVerificationDetailReferredback::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/e_r_p_asset_verification_detail_referredbacks/'.$eRPAssetVerificationDetailReferredback->id,
            $editedERPAssetVerificationDetailReferredback
        );

        $this->assertApiResponse($editedERPAssetVerificationDetailReferredback);
    }

    /**
     * @test
     */
    public function test_delete_e_r_p_asset_verification_detail_referredback()
    {
        $eRPAssetVerificationDetailReferredback = factory(ERPAssetVerificationDetailReferredback::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/e_r_p_asset_verification_detail_referredbacks/'.$eRPAssetVerificationDetailReferredback->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/e_r_p_asset_verification_detail_referredbacks/'.$eRPAssetVerificationDetailReferredback->id
        );

        $this->response->assertStatus(404);
    }
}

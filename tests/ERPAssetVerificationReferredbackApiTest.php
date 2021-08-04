<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ERPAssetVerificationReferredback;

class ERPAssetVerificationReferredbackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_e_r_p_asset_verification_referredback()
    {
        $eRPAssetVerificationReferredback = factory(ERPAssetVerificationReferredback::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/e_r_p_asset_verification_referredbacks', $eRPAssetVerificationReferredback
        );

        $this->assertApiResponse($eRPAssetVerificationReferredback);
    }

    /**
     * @test
     */
    public function test_read_e_r_p_asset_verification_referredback()
    {
        $eRPAssetVerificationReferredback = factory(ERPAssetVerificationReferredback::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/e_r_p_asset_verification_referredbacks/'.$eRPAssetVerificationReferredback->id
        );

        $this->assertApiResponse($eRPAssetVerificationReferredback->toArray());
    }

    /**
     * @test
     */
    public function test_update_e_r_p_asset_verification_referredback()
    {
        $eRPAssetVerificationReferredback = factory(ERPAssetVerificationReferredback::class)->create();
        $editedERPAssetVerificationReferredback = factory(ERPAssetVerificationReferredback::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/e_r_p_asset_verification_referredbacks/'.$eRPAssetVerificationReferredback->id,
            $editedERPAssetVerificationReferredback
        );

        $this->assertApiResponse($editedERPAssetVerificationReferredback);
    }

    /**
     * @test
     */
    public function test_delete_e_r_p_asset_verification_referredback()
    {
        $eRPAssetVerificationReferredback = factory(ERPAssetVerificationReferredback::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/e_r_p_asset_verification_referredbacks/'.$eRPAssetVerificationReferredback->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/e_r_p_asset_verification_referredbacks/'.$eRPAssetVerificationReferredback->id
        );

        $this->response->assertStatus(404);
    }
}

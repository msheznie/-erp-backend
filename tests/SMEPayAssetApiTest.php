<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMEPayAsset;

class SMEPayAssetApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_pay_asset()
    {
        $sMEPayAsset = factory(SMEPayAsset::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_pay_assets', $sMEPayAsset
        );

        $this->assertApiResponse($sMEPayAsset);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_pay_asset()
    {
        $sMEPayAsset = factory(SMEPayAsset::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_pay_assets/'.$sMEPayAsset->id
        );

        $this->assertApiResponse($sMEPayAsset->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_pay_asset()
    {
        $sMEPayAsset = factory(SMEPayAsset::class)->create();
        $editedSMEPayAsset = factory(SMEPayAsset::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_pay_assets/'.$sMEPayAsset->id,
            $editedSMEPayAsset
        );

        $this->assertApiResponse($editedSMEPayAsset);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_pay_asset()
    {
        $sMEPayAsset = factory(SMEPayAsset::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_pay_assets/'.$sMEPayAsset->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_pay_assets/'.$sMEPayAsset->id
        );

        $this->response->assertStatus(404);
    }
}

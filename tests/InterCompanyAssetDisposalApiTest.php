<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\InterCompanyAssetDisposal;

class InterCompanyAssetDisposalApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_inter_company_asset_disposal()
    {
        $interCompanyAssetDisposal = factory(InterCompanyAssetDisposal::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/inter_company_asset_disposals', $interCompanyAssetDisposal
        );

        $this->assertApiResponse($interCompanyAssetDisposal);
    }

    /**
     * @test
     */
    public function test_read_inter_company_asset_disposal()
    {
        $interCompanyAssetDisposal = factory(InterCompanyAssetDisposal::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/inter_company_asset_disposals/'.$interCompanyAssetDisposal->id
        );

        $this->assertApiResponse($interCompanyAssetDisposal->toArray());
    }

    /**
     * @test
     */
    public function test_update_inter_company_asset_disposal()
    {
        $interCompanyAssetDisposal = factory(InterCompanyAssetDisposal::class)->create();
        $editedInterCompanyAssetDisposal = factory(InterCompanyAssetDisposal::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/inter_company_asset_disposals/'.$interCompanyAssetDisposal->id,
            $editedInterCompanyAssetDisposal
        );

        $this->assertApiResponse($editedInterCompanyAssetDisposal);
    }

    /**
     * @test
     */
    public function test_delete_inter_company_asset_disposal()
    {
        $interCompanyAssetDisposal = factory(InterCompanyAssetDisposal::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/inter_company_asset_disposals/'.$interCompanyAssetDisposal->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/inter_company_asset_disposals/'.$interCompanyAssetDisposal->id
        );

        $this->response->assertStatus(404);
    }
}

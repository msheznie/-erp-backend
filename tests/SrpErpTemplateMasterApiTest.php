<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SrpErpTemplateMaster;

class SrpErpTemplateMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_srp_erp_template_master()
    {
        $srpErpTemplateMaster = factory(SrpErpTemplateMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/srp_erp_template_masters', $srpErpTemplateMaster
        );

        $this->assertApiResponse($srpErpTemplateMaster);
    }

    /**
     * @test
     */
    public function test_read_srp_erp_template_master()
    {
        $srpErpTemplateMaster = factory(SrpErpTemplateMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/srp_erp_template_masters/'.$srpErpTemplateMaster->id
        );

        $this->assertApiResponse($srpErpTemplateMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_srp_erp_template_master()
    {
        $srpErpTemplateMaster = factory(SrpErpTemplateMaster::class)->create();
        $editedSrpErpTemplateMaster = factory(SrpErpTemplateMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/srp_erp_template_masters/'.$srpErpTemplateMaster->id,
            $editedSrpErpTemplateMaster
        );

        $this->assertApiResponse($editedSrpErpTemplateMaster);
    }

    /**
     * @test
     */
    public function test_delete_srp_erp_template_master()
    {
        $srpErpTemplateMaster = factory(SrpErpTemplateMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/srp_erp_template_masters/'.$srpErpTemplateMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/srp_erp_template_masters/'.$srpErpTemplateMaster->id
        );

        $this->response->assertStatus(404);
    }
}

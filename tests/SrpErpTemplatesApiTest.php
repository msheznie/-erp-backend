<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SrpErpTemplates;

class SrpErpTemplatesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_srp_erp_templates()
    {
        $srpErpTemplates = factory(SrpErpTemplates::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/srp_erp_templates', $srpErpTemplates
        );

        $this->assertApiResponse($srpErpTemplates);
    }

    /**
     * @test
     */
    public function test_read_srp_erp_templates()
    {
        $srpErpTemplates = factory(SrpErpTemplates::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/srp_erp_templates/'.$srpErpTemplates->id
        );

        $this->assertApiResponse($srpErpTemplates->toArray());
    }

    /**
     * @test
     */
    public function test_update_srp_erp_templates()
    {
        $srpErpTemplates = factory(SrpErpTemplates::class)->create();
        $editedSrpErpTemplates = factory(SrpErpTemplates::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/srp_erp_templates/'.$srpErpTemplates->id,
            $editedSrpErpTemplates
        );

        $this->assertApiResponse($editedSrpErpTemplates);
    }

    /**
     * @test
     */
    public function test_delete_srp_erp_templates()
    {
        $srpErpTemplates = factory(SrpErpTemplates::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/srp_erp_templates/'.$srpErpTemplates->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/srp_erp_templates/'.$srpErpTemplates->id
        );

        $this->response->assertStatus(404);
    }
}

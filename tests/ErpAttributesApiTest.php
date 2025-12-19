<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ErpAttributes;

class ErpAttributesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_erp_attributes()
    {
        $erpAttributes = factory(ErpAttributes::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/erp_attributes', $erpAttributes
        );

        $this->assertApiResponse($erpAttributes);
    }

    /**
     * @test
     */
    public function test_read_erp_attributes()
    {
        $erpAttributes = factory(ErpAttributes::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/erp_attributes/'.$erpAttributes->id
        );

        $this->assertApiResponse($erpAttributes->toArray());
    }

    /**
     * @test
     */
    public function test_update_erp_attributes()
    {
        $erpAttributes = factory(ErpAttributes::class)->create();
        $editedErpAttributes = factory(ErpAttributes::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/erp_attributes/'.$erpAttributes->id,
            $editedErpAttributes
        );

        $this->assertApiResponse($editedErpAttributes);
    }

    /**
     * @test
     */
    public function test_delete_erp_attributes()
    {
        $erpAttributes = factory(ErpAttributes::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/erp_attributes/'.$erpAttributes->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/erp_attributes/'.$erpAttributes->id
        );

        $this->response->assertStatus(404);
    }
}

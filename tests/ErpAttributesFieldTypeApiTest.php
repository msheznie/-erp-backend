<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ErpAttributesFieldType;

class ErpAttributesFieldTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_erp_attributes_field_type()
    {
        $erpAttributesFieldType = factory(ErpAttributesFieldType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/erp_attributes_field_types', $erpAttributesFieldType
        );

        $this->assertApiResponse($erpAttributesFieldType);
    }

    /**
     * @test
     */
    public function test_read_erp_attributes_field_type()
    {
        $erpAttributesFieldType = factory(ErpAttributesFieldType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/erp_attributes_field_types/'.$erpAttributesFieldType->id
        );

        $this->assertApiResponse($erpAttributesFieldType->toArray());
    }

    /**
     * @test
     */
    public function test_update_erp_attributes_field_type()
    {
        $erpAttributesFieldType = factory(ErpAttributesFieldType::class)->create();
        $editedErpAttributesFieldType = factory(ErpAttributesFieldType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/erp_attributes_field_types/'.$erpAttributesFieldType->id,
            $editedErpAttributesFieldType
        );

        $this->assertApiResponse($editedErpAttributesFieldType);
    }

    /**
     * @test
     */
    public function test_delete_erp_attributes_field_type()
    {
        $erpAttributesFieldType = factory(ErpAttributesFieldType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/erp_attributes_field_types/'.$erpAttributesFieldType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/erp_attributes_field_types/'.$erpAttributesFieldType->id
        );

        $this->response->assertStatus(404);
    }
}

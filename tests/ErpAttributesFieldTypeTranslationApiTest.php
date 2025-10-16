<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ErpAttributesFieldTypeTranslation;

class ErpAttributesFieldTypeTranslationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_erp_attributes_field_type_translation()
    {
        $erpAttributesFieldTypeTranslation = factory(ErpAttributesFieldTypeTranslation::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/erp_attributes_field_type_translations', $erpAttributesFieldTypeTranslation
        );

        $this->assertApiResponse($erpAttributesFieldTypeTranslation);
    }

    /**
     * @test
     */
    public function test_read_erp_attributes_field_type_translation()
    {
        $erpAttributesFieldTypeTranslation = factory(ErpAttributesFieldTypeTranslation::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/erp_attributes_field_type_translations/'.$erpAttributesFieldTypeTranslation->id
        );

        $this->assertApiResponse($erpAttributesFieldTypeTranslation->toArray());
    }

    /**
     * @test
     */
    public function test_update_erp_attributes_field_type_translation()
    {
        $erpAttributesFieldTypeTranslation = factory(ErpAttributesFieldTypeTranslation::class)->create();
        $editedErpAttributesFieldTypeTranslation = factory(ErpAttributesFieldTypeTranslation::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/erp_attributes_field_type_translations/'.$erpAttributesFieldTypeTranslation->id,
            $editedErpAttributesFieldTypeTranslation
        );

        $this->assertApiResponse($editedErpAttributesFieldTypeTranslation);
    }

    /**
     * @test
     */
    public function test_delete_erp_attributes_field_type_translation()
    {
        $erpAttributesFieldTypeTranslation = factory(ErpAttributesFieldTypeTranslation::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/erp_attributes_field_type_translations/'.$erpAttributesFieldTypeTranslation->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/erp_attributes_field_type_translations/'.$erpAttributesFieldTypeTranslation->id
        );

        $this->response->assertStatus(404);
    }
}

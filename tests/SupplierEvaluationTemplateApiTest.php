<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SupplierEvaluationTemplate;

class SupplierEvaluationTemplateApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_supplier_evaluation_template()
    {
        $supplierEvaluationTemplate = factory(SupplierEvaluationTemplate::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/supplier_evaluation_templates', $supplierEvaluationTemplate
        );

        $this->assertApiResponse($supplierEvaluationTemplate);
    }

    /**
     * @test
     */
    public function test_read_supplier_evaluation_template()
    {
        $supplierEvaluationTemplate = factory(SupplierEvaluationTemplate::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/supplier_evaluation_templates/'.$supplierEvaluationTemplate->id
        );

        $this->assertApiResponse($supplierEvaluationTemplate->toArray());
    }

    /**
     * @test
     */
    public function test_update_supplier_evaluation_template()
    {
        $supplierEvaluationTemplate = factory(SupplierEvaluationTemplate::class)->create();
        $editedSupplierEvaluationTemplate = factory(SupplierEvaluationTemplate::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/supplier_evaluation_templates/'.$supplierEvaluationTemplate->id,
            $editedSupplierEvaluationTemplate
        );

        $this->assertApiResponse($editedSupplierEvaluationTemplate);
    }

    /**
     * @test
     */
    public function test_delete_supplier_evaluation_template()
    {
        $supplierEvaluationTemplate = factory(SupplierEvaluationTemplate::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/supplier_evaluation_templates/'.$supplierEvaluationTemplate->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/supplier_evaluation_templates/'.$supplierEvaluationTemplate->id
        );

        $this->response->assertStatus(404);
    }
}

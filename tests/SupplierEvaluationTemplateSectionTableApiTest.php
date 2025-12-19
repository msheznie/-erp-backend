<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SupplierEvaluationTemplateSectionTable;

class SupplierEvaluationTemplateSectionTableApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_supplier_evaluation_template_section_table()
    {
        $supplierEvaluationTemplateSectionTable = factory(SupplierEvaluationTemplateSectionTable::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/supplier_evaluation_template_section_tables', $supplierEvaluationTemplateSectionTable
        );

        $this->assertApiResponse($supplierEvaluationTemplateSectionTable);
    }

    /**
     * @test
     */
    public function test_read_supplier_evaluation_template_section_table()
    {
        $supplierEvaluationTemplateSectionTable = factory(SupplierEvaluationTemplateSectionTable::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/supplier_evaluation_template_section_tables/'.$supplierEvaluationTemplateSectionTable->id
        );

        $this->assertApiResponse($supplierEvaluationTemplateSectionTable->toArray());
    }

    /**
     * @test
     */
    public function test_update_supplier_evaluation_template_section_table()
    {
        $supplierEvaluationTemplateSectionTable = factory(SupplierEvaluationTemplateSectionTable::class)->create();
        $editedSupplierEvaluationTemplateSectionTable = factory(SupplierEvaluationTemplateSectionTable::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/supplier_evaluation_template_section_tables/'.$supplierEvaluationTemplateSectionTable->id,
            $editedSupplierEvaluationTemplateSectionTable
        );

        $this->assertApiResponse($editedSupplierEvaluationTemplateSectionTable);
    }

    /**
     * @test
     */
    public function test_delete_supplier_evaluation_template_section_table()
    {
        $supplierEvaluationTemplateSectionTable = factory(SupplierEvaluationTemplateSectionTable::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/supplier_evaluation_template_section_tables/'.$supplierEvaluationTemplateSectionTable->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/supplier_evaluation_template_section_tables/'.$supplierEvaluationTemplateSectionTable->id
        );

        $this->response->assertStatus(404);
    }
}

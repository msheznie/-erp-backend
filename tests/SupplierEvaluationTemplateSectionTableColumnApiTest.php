<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SupplierEvaluationTemplateSectionTableColumn;

class SupplierEvaluationTemplateSectionTableColumnApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_supplier_evaluation_template_section_table_column()
    {
        $supplierEvaluationTemplateSectionTableColumn = factory(SupplierEvaluationTemplateSectionTableColumn::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/supplier_evaluation_template_section_table_columns', $supplierEvaluationTemplateSectionTableColumn
        );

        $this->assertApiResponse($supplierEvaluationTemplateSectionTableColumn);
    }

    /**
     * @test
     */
    public function test_read_supplier_evaluation_template_section_table_column()
    {
        $supplierEvaluationTemplateSectionTableColumn = factory(SupplierEvaluationTemplateSectionTableColumn::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/supplier_evaluation_template_section_table_columns/'.$supplierEvaluationTemplateSectionTableColumn->id
        );

        $this->assertApiResponse($supplierEvaluationTemplateSectionTableColumn->toArray());
    }

    /**
     * @test
     */
    public function test_update_supplier_evaluation_template_section_table_column()
    {
        $supplierEvaluationTemplateSectionTableColumn = factory(SupplierEvaluationTemplateSectionTableColumn::class)->create();
        $editedSupplierEvaluationTemplateSectionTableColumn = factory(SupplierEvaluationTemplateSectionTableColumn::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/supplier_evaluation_template_section_table_columns/'.$supplierEvaluationTemplateSectionTableColumn->id,
            $editedSupplierEvaluationTemplateSectionTableColumn
        );

        $this->assertApiResponse($editedSupplierEvaluationTemplateSectionTableColumn);
    }

    /**
     * @test
     */
    public function test_delete_supplier_evaluation_template_section_table_column()
    {
        $supplierEvaluationTemplateSectionTableColumn = factory(SupplierEvaluationTemplateSectionTableColumn::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/supplier_evaluation_template_section_table_columns/'.$supplierEvaluationTemplateSectionTableColumn->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/supplier_evaluation_template_section_table_columns/'.$supplierEvaluationTemplateSectionTableColumn->id
        );

        $this->response->assertStatus(404);
    }
}

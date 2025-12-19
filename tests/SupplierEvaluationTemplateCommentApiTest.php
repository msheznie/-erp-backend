<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SupplierEvaluationTemplateComment;

class SupplierEvaluationTemplateCommentApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_supplier_evaluation_template_comment()
    {
        $supplierEvaluationTemplateComment = factory(SupplierEvaluationTemplateComment::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/supplier_evaluation_template_comments', $supplierEvaluationTemplateComment
        );

        $this->assertApiResponse($supplierEvaluationTemplateComment);
    }

    /**
     * @test
     */
    public function test_read_supplier_evaluation_template_comment()
    {
        $supplierEvaluationTemplateComment = factory(SupplierEvaluationTemplateComment::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/supplier_evaluation_template_comments/'.$supplierEvaluationTemplateComment->id
        );

        $this->assertApiResponse($supplierEvaluationTemplateComment->toArray());
    }

    /**
     * @test
     */
    public function test_update_supplier_evaluation_template_comment()
    {
        $supplierEvaluationTemplateComment = factory(SupplierEvaluationTemplateComment::class)->create();
        $editedSupplierEvaluationTemplateComment = factory(SupplierEvaluationTemplateComment::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/supplier_evaluation_template_comments/'.$supplierEvaluationTemplateComment->id,
            $editedSupplierEvaluationTemplateComment
        );

        $this->assertApiResponse($editedSupplierEvaluationTemplateComment);
    }

    /**
     * @test
     */
    public function test_delete_supplier_evaluation_template_comment()
    {
        $supplierEvaluationTemplateComment = factory(SupplierEvaluationTemplateComment::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/supplier_evaluation_template_comments/'.$supplierEvaluationTemplateComment->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/supplier_evaluation_template_comments/'.$supplierEvaluationTemplateComment->id
        );

        $this->response->assertStatus(404);
    }
}

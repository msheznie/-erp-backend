<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocumentSubProduct;

class DocumentSubProductApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_document_sub_product()
    {
        $documentSubProduct = factory(DocumentSubProduct::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/document_sub_products', $documentSubProduct
        );

        $this->assertApiResponse($documentSubProduct);
    }

    /**
     * @test
     */
    public function test_read_document_sub_product()
    {
        $documentSubProduct = factory(DocumentSubProduct::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/document_sub_products/'.$documentSubProduct->id
        );

        $this->assertApiResponse($documentSubProduct->toArray());
    }

    /**
     * @test
     */
    public function test_update_document_sub_product()
    {
        $documentSubProduct = factory(DocumentSubProduct::class)->create();
        $editedDocumentSubProduct = factory(DocumentSubProduct::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/document_sub_products/'.$documentSubProduct->id,
            $editedDocumentSubProduct
        );

        $this->assertApiResponse($editedDocumentSubProduct);
    }

    /**
     * @test
     */
    public function test_delete_document_sub_product()
    {
        $documentSubProduct = factory(DocumentSubProduct::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/document_sub_products/'.$documentSubProduct->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/document_sub_products/'.$documentSubProduct->id
        );

        $this->response->assertStatus(404);
    }
}

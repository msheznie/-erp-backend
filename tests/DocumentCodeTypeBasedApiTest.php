<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocumentCodeTypeBased;

class DocumentCodeTypeBasedApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_document_code_type_based()
    {
        $documentCodeTypeBased = factory(DocumentCodeTypeBased::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/document_code_type_baseds', $documentCodeTypeBased
        );

        $this->assertApiResponse($documentCodeTypeBased);
    }

    /**
     * @test
     */
    public function test_read_document_code_type_based()
    {
        $documentCodeTypeBased = factory(DocumentCodeTypeBased::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/document_code_type_baseds/'.$documentCodeTypeBased->id
        );

        $this->assertApiResponse($documentCodeTypeBased->toArray());
    }

    /**
     * @test
     */
    public function test_update_document_code_type_based()
    {
        $documentCodeTypeBased = factory(DocumentCodeTypeBased::class)->create();
        $editedDocumentCodeTypeBased = factory(DocumentCodeTypeBased::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/document_code_type_baseds/'.$documentCodeTypeBased->id,
            $editedDocumentCodeTypeBased
        );

        $this->assertApiResponse($editedDocumentCodeTypeBased);
    }

    /**
     * @test
     */
    public function test_delete_document_code_type_based()
    {
        $documentCodeTypeBased = factory(DocumentCodeTypeBased::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/document_code_type_baseds/'.$documentCodeTypeBased->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/document_code_type_baseds/'.$documentCodeTypeBased->id
        );

        $this->response->assertStatus(404);
    }
}

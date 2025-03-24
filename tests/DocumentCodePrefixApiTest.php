<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocumentCodePrefix;

class DocumentCodePrefixApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_document_code_prefix()
    {
        $documentCodePrefix = factory(DocumentCodePrefix::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/document_code_prefixes', $documentCodePrefix
        );

        $this->assertApiResponse($documentCodePrefix);
    }

    /**
     * @test
     */
    public function test_read_document_code_prefix()
    {
        $documentCodePrefix = factory(DocumentCodePrefix::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/document_code_prefixes/'.$documentCodePrefix->id
        );

        $this->assertApiResponse($documentCodePrefix->toArray());
    }

    /**
     * @test
     */
    public function test_update_document_code_prefix()
    {
        $documentCodePrefix = factory(DocumentCodePrefix::class)->create();
        $editedDocumentCodePrefix = factory(DocumentCodePrefix::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/document_code_prefixes/'.$documentCodePrefix->id,
            $editedDocumentCodePrefix
        );

        $this->assertApiResponse($editedDocumentCodePrefix);
    }

    /**
     * @test
     */
    public function test_delete_document_code_prefix()
    {
        $documentCodePrefix = factory(DocumentCodePrefix::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/document_code_prefixes/'.$documentCodePrefix->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/document_code_prefixes/'.$documentCodePrefix->id
        );

        $this->response->assertStatus(404);
    }
}

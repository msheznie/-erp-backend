<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocumentCodeFormat;

class DocumentCodeFormatApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_document_code_format()
    {
        $documentCodeFormat = factory(DocumentCodeFormat::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/document_code_formats', $documentCodeFormat
        );

        $this->assertApiResponse($documentCodeFormat);
    }

    /**
     * @test
     */
    public function test_read_document_code_format()
    {
        $documentCodeFormat = factory(DocumentCodeFormat::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/document_code_formats/'.$documentCodeFormat->id
        );

        $this->assertApiResponse($documentCodeFormat->toArray());
    }

    /**
     * @test
     */
    public function test_update_document_code_format()
    {
        $documentCodeFormat = factory(DocumentCodeFormat::class)->create();
        $editedDocumentCodeFormat = factory(DocumentCodeFormat::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/document_code_formats/'.$documentCodeFormat->id,
            $editedDocumentCodeFormat
        );

        $this->assertApiResponse($editedDocumentCodeFormat);
    }

    /**
     * @test
     */
    public function test_delete_document_code_format()
    {
        $documentCodeFormat = factory(DocumentCodeFormat::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/document_code_formats/'.$documentCodeFormat->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/document_code_formats/'.$documentCodeFormat->id
        );

        $this->response->assertStatus(404);
    }
}

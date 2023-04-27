<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocumentModifyRequest;

class DocumentModifyRequestApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_document_modify_request()
    {
        $documentModifyRequest = factory(DocumentModifyRequest::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/document_modify_requests', $documentModifyRequest
        );

        $this->assertApiResponse($documentModifyRequest);
    }

    /**
     * @test
     */
    public function test_read_document_modify_request()
    {
        $documentModifyRequest = factory(DocumentModifyRequest::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/document_modify_requests/'.$documentModifyRequest->id
        );

        $this->assertApiResponse($documentModifyRequest->toArray());
    }

    /**
     * @test
     */
    public function test_update_document_modify_request()
    {
        $documentModifyRequest = factory(DocumentModifyRequest::class)->create();
        $editedDocumentModifyRequest = factory(DocumentModifyRequest::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/document_modify_requests/'.$documentModifyRequest->id,
            $editedDocumentModifyRequest
        );

        $this->assertApiResponse($editedDocumentModifyRequest);
    }

    /**
     * @test
     */
    public function test_delete_document_modify_request()
    {
        $documentModifyRequest = factory(DocumentModifyRequest::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/document_modify_requests/'.$documentModifyRequest->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/document_modify_requests/'.$documentModifyRequest->id
        );

        $this->response->assertStatus(404);
    }
}

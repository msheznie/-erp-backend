<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DocumentAttachmentsEditLog;

class DocumentAttachmentsEditLogApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_document_attachments_edit_log()
    {
        $documentAttachmentsEditLog = factory(DocumentAttachmentsEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/document_attachments_edit_logs', $documentAttachmentsEditLog
        );

        $this->assertApiResponse($documentAttachmentsEditLog);
    }

    /**
     * @test
     */
    public function test_read_document_attachments_edit_log()
    {
        $documentAttachmentsEditLog = factory(DocumentAttachmentsEditLog::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/document_attachments_edit_logs/'.$documentAttachmentsEditLog->id
        );

        $this->assertApiResponse($documentAttachmentsEditLog->toArray());
    }

    /**
     * @test
     */
    public function test_update_document_attachments_edit_log()
    {
        $documentAttachmentsEditLog = factory(DocumentAttachmentsEditLog::class)->create();
        $editedDocumentAttachmentsEditLog = factory(DocumentAttachmentsEditLog::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/document_attachments_edit_logs/'.$documentAttachmentsEditLog->id,
            $editedDocumentAttachmentsEditLog
        );

        $this->assertApiResponse($editedDocumentAttachmentsEditLog);
    }

    /**
     * @test
     */
    public function test_delete_document_attachments_edit_log()
    {
        $documentAttachmentsEditLog = factory(DocumentAttachmentsEditLog::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/document_attachments_edit_logs/'.$documentAttachmentsEditLog->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/document_attachments_edit_logs/'.$documentAttachmentsEditLog->id
        );

        $this->response->assertStatus(404);
    }
}

<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeHrmsDocumentAttachmentsTrait;
use Tests\ApiTestTrait;

class HrmsDocumentAttachmentsApiTest extends TestCase
{
    use MakeHrmsDocumentAttachmentsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hrms_document_attachments()
    {
        $hrmsDocumentAttachments = $this->fakeHrmsDocumentAttachmentsData();
        $this->response = $this->json('POST', '/api/hrmsDocumentAttachments', $hrmsDocumentAttachments);

        $this->assertApiResponse($hrmsDocumentAttachments);
    }

    /**
     * @test
     */
    public function test_read_hrms_document_attachments()
    {
        $hrmsDocumentAttachments = $this->makeHrmsDocumentAttachments();
        $this->response = $this->json('GET', '/api/hrmsDocumentAttachments/'.$hrmsDocumentAttachments->id);

        $this->assertApiResponse($hrmsDocumentAttachments->toArray());
    }

    /**
     * @test
     */
    public function test_update_hrms_document_attachments()
    {
        $hrmsDocumentAttachments = $this->makeHrmsDocumentAttachments();
        $editedHrmsDocumentAttachments = $this->fakeHrmsDocumentAttachmentsData();

        $this->response = $this->json('PUT', '/api/hrmsDocumentAttachments/'.$hrmsDocumentAttachments->id, $editedHrmsDocumentAttachments);

        $this->assertApiResponse($editedHrmsDocumentAttachments);
    }

    /**
     * @test
     */
    public function test_delete_hrms_document_attachments()
    {
        $hrmsDocumentAttachments = $this->makeHrmsDocumentAttachments();
        $this->response = $this->json('DELETE', '/api/hrmsDocumentAttachments/'.$hrmsDocumentAttachments->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/hrmsDocumentAttachments/'.$hrmsDocumentAttachments->id);

        $this->response->assertStatus(404);
    }
}

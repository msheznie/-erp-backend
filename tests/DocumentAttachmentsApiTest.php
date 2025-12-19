<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentAttachmentsApiTest extends TestCase
{
    use MakeDocumentAttachmentsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDocumentAttachments()
    {
        $documentAttachments = $this->fakeDocumentAttachmentsData();
        $this->json('POST', '/api/v1/documentAttachments', $documentAttachments);

        $this->assertApiResponse($documentAttachments);
    }

    /**
     * @test
     */
    public function testReadDocumentAttachments()
    {
        $documentAttachments = $this->makeDocumentAttachments();
        $this->json('GET', '/api/v1/documentAttachments/'.$documentAttachments->id);

        $this->assertApiResponse($documentAttachments->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDocumentAttachments()
    {
        $documentAttachments = $this->makeDocumentAttachments();
        $editedDocumentAttachments = $this->fakeDocumentAttachmentsData();

        $this->json('PUT', '/api/v1/documentAttachments/'.$documentAttachments->id, $editedDocumentAttachments);

        $this->assertApiResponse($editedDocumentAttachments);
    }

    /**
     * @test
     */
    public function testDeleteDocumentAttachments()
    {
        $documentAttachments = $this->makeDocumentAttachments();
        $this->json('DELETE', '/api/v1/documentAttachments/'.$documentAttachments->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/documentAttachments/'.$documentAttachments->id);

        $this->assertResponseStatus(404);
    }
}

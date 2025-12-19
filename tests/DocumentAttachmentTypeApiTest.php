<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentAttachmentTypeApiTest extends TestCase
{
    use MakeDocumentAttachmentTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDocumentAttachmentType()
    {
        $documentAttachmentType = $this->fakeDocumentAttachmentTypeData();
        $this->json('POST', '/api/v1/documentAttachmentTypes', $documentAttachmentType);

        $this->assertApiResponse($documentAttachmentType);
    }

    /**
     * @test
     */
    public function testReadDocumentAttachmentType()
    {
        $documentAttachmentType = $this->makeDocumentAttachmentType();
        $this->json('GET', '/api/v1/documentAttachmentTypes/'.$documentAttachmentType->id);

        $this->assertApiResponse($documentAttachmentType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDocumentAttachmentType()
    {
        $documentAttachmentType = $this->makeDocumentAttachmentType();
        $editedDocumentAttachmentType = $this->fakeDocumentAttachmentTypeData();

        $this->json('PUT', '/api/v1/documentAttachmentTypes/'.$documentAttachmentType->id, $editedDocumentAttachmentType);

        $this->assertApiResponse($editedDocumentAttachmentType);
    }

    /**
     * @test
     */
    public function testDeleteDocumentAttachmentType()
    {
        $documentAttachmentType = $this->makeDocumentAttachmentType();
        $this->json('DELETE', '/api/v1/documentAttachmentTypes/'.$documentAttachmentType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/documentAttachmentTypes/'.$documentAttachmentType->id);

        $this->assertResponseStatus(404);
    }
}

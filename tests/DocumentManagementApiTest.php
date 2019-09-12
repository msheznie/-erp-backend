<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeDocumentManagementTrait;
use Tests\ApiTestTrait;

class DocumentManagementApiTest extends TestCase
{
    use MakeDocumentManagementTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_document_management()
    {
        $documentManagement = $this->fakeDocumentManagementData();
        $this->response = $this->json('POST', '/api/documentManagements', $documentManagement);

        $this->assertApiResponse($documentManagement);
    }

    /**
     * @test
     */
    public function test_read_document_management()
    {
        $documentManagement = $this->makeDocumentManagement();
        $this->response = $this->json('GET', '/api/documentManagements/'.$documentManagement->id);

        $this->assertApiResponse($documentManagement->toArray());
    }

    /**
     * @test
     */
    public function test_update_document_management()
    {
        $documentManagement = $this->makeDocumentManagement();
        $editedDocumentManagement = $this->fakeDocumentManagementData();

        $this->response = $this->json('PUT', '/api/documentManagements/'.$documentManagement->id, $editedDocumentManagement);

        $this->assertApiResponse($editedDocumentManagement);
    }

    /**
     * @test
     */
    public function test_delete_document_management()
    {
        $documentManagement = $this->makeDocumentManagement();
        $this->response = $this->json('DELETE', '/api/documentManagements/'.$documentManagement->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/documentManagements/'.$documentManagement->id);

        $this->response->assertStatus(404);
    }
}

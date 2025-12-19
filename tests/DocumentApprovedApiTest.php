<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentApprovedApiTest extends TestCase
{
    use MakeDocumentApprovedTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDocumentApproved()
    {
        $documentApproved = $this->fakeDocumentApprovedData();
        $this->json('POST', '/api/v1/documentApproveds', $documentApproved);

        $this->assertApiResponse($documentApproved);
    }

    /**
     * @test
     */
    public function testReadDocumentApproved()
    {
        $documentApproved = $this->makeDocumentApproved();
        $this->json('GET', '/api/v1/documentApproveds/'.$documentApproved->id);

        $this->assertApiResponse($documentApproved->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDocumentApproved()
    {
        $documentApproved = $this->makeDocumentApproved();
        $editedDocumentApproved = $this->fakeDocumentApprovedData();

        $this->json('PUT', '/api/v1/documentApproveds/'.$documentApproved->id, $editedDocumentApproved);

        $this->assertApiResponse($editedDocumentApproved);
    }

    /**
     * @test
     */
    public function testDeleteDocumentApproved()
    {
        $documentApproved = $this->makeDocumentApproved();
        $this->json('DELETE', '/api/v1/documentApproveds/'.$documentApproved->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/documentApproveds/'.$documentApproved->id);

        $this->assertResponseStatus(404);
    }
}

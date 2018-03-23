<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentMasterApiTest extends TestCase
{
    use MakeDocumentMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDocumentMaster()
    {
        $documentMaster = $this->fakeDocumentMasterData();
        $this->json('POST', '/api/v1/documentMasters', $documentMaster);

        $this->assertApiResponse($documentMaster);
    }

    /**
     * @test
     */
    public function testReadDocumentMaster()
    {
        $documentMaster = $this->makeDocumentMaster();
        $this->json('GET', '/api/v1/documentMasters/'.$documentMaster->id);

        $this->assertApiResponse($documentMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDocumentMaster()
    {
        $documentMaster = $this->makeDocumentMaster();
        $editedDocumentMaster = $this->fakeDocumentMasterData();

        $this->json('PUT', '/api/v1/documentMasters/'.$documentMaster->id, $editedDocumentMaster);

        $this->assertApiResponse($editedDocumentMaster);
    }

    /**
     * @test
     */
    public function testDeleteDocumentMaster()
    {
        $documentMaster = $this->makeDocumentMaster();
        $this->json('DELETE', '/api/v1/documentMasters/'.$documentMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/documentMasters/'.$documentMaster->id);

        $this->assertResponseStatus(404);
    }
}

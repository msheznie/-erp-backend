<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentEmailNotificationMasterApiTest extends TestCase
{
    use MakeDocumentEmailNotificationMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDocumentEmailNotificationMaster()
    {
        $documentEmailNotificationMaster = $this->fakeDocumentEmailNotificationMasterData();
        $this->json('POST', '/api/v1/documentEmailNotificationMasters', $documentEmailNotificationMaster);

        $this->assertApiResponse($documentEmailNotificationMaster);
    }

    /**
     * @test
     */
    public function testReadDocumentEmailNotificationMaster()
    {
        $documentEmailNotificationMaster = $this->makeDocumentEmailNotificationMaster();
        $this->json('GET', '/api/v1/documentEmailNotificationMasters/'.$documentEmailNotificationMaster->id);

        $this->assertApiResponse($documentEmailNotificationMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDocumentEmailNotificationMaster()
    {
        $documentEmailNotificationMaster = $this->makeDocumentEmailNotificationMaster();
        $editedDocumentEmailNotificationMaster = $this->fakeDocumentEmailNotificationMasterData();

        $this->json('PUT', '/api/v1/documentEmailNotificationMasters/'.$documentEmailNotificationMaster->id, $editedDocumentEmailNotificationMaster);

        $this->assertApiResponse($editedDocumentEmailNotificationMaster);
    }

    /**
     * @test
     */
    public function testDeleteDocumentEmailNotificationMaster()
    {
        $documentEmailNotificationMaster = $this->makeDocumentEmailNotificationMaster();
        $this->json('DELETE', '/api/v1/documentEmailNotificationMasters/'.$documentEmailNotificationMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/documentEmailNotificationMasters/'.$documentEmailNotificationMaster->id);

        $this->assertResponseStatus(404);
    }
}

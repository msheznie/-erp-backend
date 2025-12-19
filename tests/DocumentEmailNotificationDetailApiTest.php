<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentEmailNotificationDetailApiTest extends TestCase
{
    use MakeDocumentEmailNotificationDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDocumentEmailNotificationDetail()
    {
        $documentEmailNotificationDetail = $this->fakeDocumentEmailNotificationDetailData();
        $this->json('POST', '/api/v1/documentEmailNotificationDetails', $documentEmailNotificationDetail);

        $this->assertApiResponse($documentEmailNotificationDetail);
    }

    /**
     * @test
     */
    public function testReadDocumentEmailNotificationDetail()
    {
        $documentEmailNotificationDetail = $this->makeDocumentEmailNotificationDetail();
        $this->json('GET', '/api/v1/documentEmailNotificationDetails/'.$documentEmailNotificationDetail->id);

        $this->assertApiResponse($documentEmailNotificationDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDocumentEmailNotificationDetail()
    {
        $documentEmailNotificationDetail = $this->makeDocumentEmailNotificationDetail();
        $editedDocumentEmailNotificationDetail = $this->fakeDocumentEmailNotificationDetailData();

        $this->json('PUT', '/api/v1/documentEmailNotificationDetails/'.$documentEmailNotificationDetail->id, $editedDocumentEmailNotificationDetail);

        $this->assertApiResponse($editedDocumentEmailNotificationDetail);
    }

    /**
     * @test
     */
    public function testDeleteDocumentEmailNotificationDetail()
    {
        $documentEmailNotificationDetail = $this->makeDocumentEmailNotificationDetail();
        $this->json('DELETE', '/api/v1/documentEmailNotificationDetails/'.$documentEmailNotificationDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/documentEmailNotificationDetails/'.$documentEmailNotificationDetail->id);

        $this->assertResponseStatus(404);
    }
}

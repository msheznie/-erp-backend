<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentReferedHistoryApiTest extends TestCase
{
    use MakeDocumentReferedHistoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDocumentReferedHistory()
    {
        $documentReferedHistory = $this->fakeDocumentReferedHistoryData();
        $this->json('POST', '/api/v1/documentReferedHistories', $documentReferedHistory);

        $this->assertApiResponse($documentReferedHistory);
    }

    /**
     * @test
     */
    public function testReadDocumentReferedHistory()
    {
        $documentReferedHistory = $this->makeDocumentReferedHistory();
        $this->json('GET', '/api/v1/documentReferedHistories/'.$documentReferedHistory->id);

        $this->assertApiResponse($documentReferedHistory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDocumentReferedHistory()
    {
        $documentReferedHistory = $this->makeDocumentReferedHistory();
        $editedDocumentReferedHistory = $this->fakeDocumentReferedHistoryData();

        $this->json('PUT', '/api/v1/documentReferedHistories/'.$documentReferedHistory->id, $editedDocumentReferedHistory);

        $this->assertApiResponse($editedDocumentReferedHistory);
    }

    /**
     * @test
     */
    public function testDeleteDocumentReferedHistory()
    {
        $documentReferedHistory = $this->makeDocumentReferedHistory();
        $this->json('DELETE', '/api/v1/documentReferedHistories/'.$documentReferedHistory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/documentReferedHistories/'.$documentReferedHistory->id);

        $this->assertResponseStatus(404);
    }
}

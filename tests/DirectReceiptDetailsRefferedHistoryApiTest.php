<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectReceiptDetailsRefferedHistoryApiTest extends TestCase
{
    use MakeDirectReceiptDetailsRefferedHistoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDirectReceiptDetailsRefferedHistory()
    {
        $directReceiptDetailsRefferedHistory = $this->fakeDirectReceiptDetailsRefferedHistoryData();
        $this->json('POST', '/api/v1/directReceiptDetailsRefferedHistories', $directReceiptDetailsRefferedHistory);

        $this->assertApiResponse($directReceiptDetailsRefferedHistory);
    }

    /**
     * @test
     */
    public function testReadDirectReceiptDetailsRefferedHistory()
    {
        $directReceiptDetailsRefferedHistory = $this->makeDirectReceiptDetailsRefferedHistory();
        $this->json('GET', '/api/v1/directReceiptDetailsRefferedHistories/'.$directReceiptDetailsRefferedHistory->id);

        $this->assertApiResponse($directReceiptDetailsRefferedHistory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDirectReceiptDetailsRefferedHistory()
    {
        $directReceiptDetailsRefferedHistory = $this->makeDirectReceiptDetailsRefferedHistory();
        $editedDirectReceiptDetailsRefferedHistory = $this->fakeDirectReceiptDetailsRefferedHistoryData();

        $this->json('PUT', '/api/v1/directReceiptDetailsRefferedHistories/'.$directReceiptDetailsRefferedHistory->id, $editedDirectReceiptDetailsRefferedHistory);

        $this->assertApiResponse($editedDirectReceiptDetailsRefferedHistory);
    }

    /**
     * @test
     */
    public function testDeleteDirectReceiptDetailsRefferedHistory()
    {
        $directReceiptDetailsRefferedHistory = $this->makeDirectReceiptDetailsRefferedHistory();
        $this->json('DELETE', '/api/v1/directReceiptDetailsRefferedHistories/'.$directReceiptDetailsRefferedHistory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/directReceiptDetailsRefferedHistories/'.$directReceiptDetailsRefferedHistory->id);

        $this->assertResponseStatus(404);
    }
}

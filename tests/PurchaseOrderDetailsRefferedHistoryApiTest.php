<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderDetailsRefferedHistoryApiTest extends TestCase
{
    use MakePurchaseOrderDetailsRefferedHistoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePurchaseOrderDetailsRefferedHistory()
    {
        $purchaseOrderDetailsRefferedHistory = $this->fakePurchaseOrderDetailsRefferedHistoryData();
        $this->json('POST', '/api/v1/purchaseOrderDetailsRefferedHistories', $purchaseOrderDetailsRefferedHistory);

        $this->assertApiResponse($purchaseOrderDetailsRefferedHistory);
    }

    /**
     * @test
     */
    public function testReadPurchaseOrderDetailsRefferedHistory()
    {
        $purchaseOrderDetailsRefferedHistory = $this->makePurchaseOrderDetailsRefferedHistory();
        $this->json('GET', '/api/v1/purchaseOrderDetailsRefferedHistories/'.$purchaseOrderDetailsRefferedHistory->id);

        $this->assertApiResponse($purchaseOrderDetailsRefferedHistory->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePurchaseOrderDetailsRefferedHistory()
    {
        $purchaseOrderDetailsRefferedHistory = $this->makePurchaseOrderDetailsRefferedHistory();
        $editedPurchaseOrderDetailsRefferedHistory = $this->fakePurchaseOrderDetailsRefferedHistoryData();

        $this->json('PUT', '/api/v1/purchaseOrderDetailsRefferedHistories/'.$purchaseOrderDetailsRefferedHistory->id, $editedPurchaseOrderDetailsRefferedHistory);

        $this->assertApiResponse($editedPurchaseOrderDetailsRefferedHistory);
    }

    /**
     * @test
     */
    public function testDeletePurchaseOrderDetailsRefferedHistory()
    {
        $purchaseOrderDetailsRefferedHistory = $this->makePurchaseOrderDetailsRefferedHistory();
        $this->json('DELETE', '/api/v1/purchaseOrderDetailsRefferedHistories/'.$purchaseOrderDetailsRefferedHistory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/purchaseOrderDetailsRefferedHistories/'.$purchaseOrderDetailsRefferedHistory->id);

        $this->assertResponseStatus(404);
    }
}

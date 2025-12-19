<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderMasterRefferedHistoryApiTest extends TestCase
{
    use MakePurchaseOrderMasterRefferedHistoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePurchaseOrderMasterRefferedHistory()
    {
        $purchaseOrderMasterRefferedHistory = $this->fakePurchaseOrderMasterRefferedHistoryData();
        $this->json('POST', '/api/v1/purchaseOrderMasterRefferedHistories', $purchaseOrderMasterRefferedHistory);

        $this->assertApiResponse($purchaseOrderMasterRefferedHistory);
    }

    /**
     * @test
     */
    public function testReadPurchaseOrderMasterRefferedHistory()
    {
        $purchaseOrderMasterRefferedHistory = $this->makePurchaseOrderMasterRefferedHistory();
        $this->json('GET', '/api/v1/purchaseOrderMasterRefferedHistories/'.$purchaseOrderMasterRefferedHistory->id);

        $this->assertApiResponse($purchaseOrderMasterRefferedHistory->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePurchaseOrderMasterRefferedHistory()
    {
        $purchaseOrderMasterRefferedHistory = $this->makePurchaseOrderMasterRefferedHistory();
        $editedPurchaseOrderMasterRefferedHistory = $this->fakePurchaseOrderMasterRefferedHistoryData();

        $this->json('PUT', '/api/v1/purchaseOrderMasterRefferedHistories/'.$purchaseOrderMasterRefferedHistory->id, $editedPurchaseOrderMasterRefferedHistory);

        $this->assertApiResponse($editedPurchaseOrderMasterRefferedHistory);
    }

    /**
     * @test
     */
    public function testDeletePurchaseOrderMasterRefferedHistory()
    {
        $purchaseOrderMasterRefferedHistory = $this->makePurchaseOrderMasterRefferedHistory();
        $this->json('DELETE', '/api/v1/purchaseOrderMasterRefferedHistories/'.$purchaseOrderMasterRefferedHistory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/purchaseOrderMasterRefferedHistories/'.$purchaseOrderMasterRefferedHistory->id);

        $this->assertResponseStatus(404);
    }
}

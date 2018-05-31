<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderStatusApiTest extends TestCase
{
    use MakePurchaseOrderStatusTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePurchaseOrderStatus()
    {
        $purchaseOrderStatus = $this->fakePurchaseOrderStatusData();
        $this->json('POST', '/api/v1/purchaseOrderStatuses', $purchaseOrderStatus);

        $this->assertApiResponse($purchaseOrderStatus);
    }

    /**
     * @test
     */
    public function testReadPurchaseOrderStatus()
    {
        $purchaseOrderStatus = $this->makePurchaseOrderStatus();
        $this->json('GET', '/api/v1/purchaseOrderStatuses/'.$purchaseOrderStatus->id);

        $this->assertApiResponse($purchaseOrderStatus->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePurchaseOrderStatus()
    {
        $purchaseOrderStatus = $this->makePurchaseOrderStatus();
        $editedPurchaseOrderStatus = $this->fakePurchaseOrderStatusData();

        $this->json('PUT', '/api/v1/purchaseOrderStatuses/'.$purchaseOrderStatus->id, $editedPurchaseOrderStatus);

        $this->assertApiResponse($editedPurchaseOrderStatus);
    }

    /**
     * @test
     */
    public function testDeletePurchaseOrderStatus()
    {
        $purchaseOrderStatus = $this->makePurchaseOrderStatus();
        $this->json('DELETE', '/api/v1/purchaseOrderStatuses/'.$purchaseOrderStatus->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/purchaseOrderStatuses/'.$purchaseOrderStatus->id);

        $this->assertResponseStatus(404);
    }
}

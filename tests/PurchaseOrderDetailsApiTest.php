<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderDetailsApiTest extends TestCase
{
    use MakePurchaseOrderDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePurchaseOrderDetails()
    {
        $purchaseOrderDetails = $this->fakePurchaseOrderDetailsData();
        $this->json('POST', '/api/v1/purchaseOrderDetails', $purchaseOrderDetails);

        $this->assertApiResponse($purchaseOrderDetails);
    }

    /**
     * @test
     */
    public function testReadPurchaseOrderDetails()
    {
        $purchaseOrderDetails = $this->makePurchaseOrderDetails();
        $this->json('GET', '/api/v1/purchaseOrderDetails/'.$purchaseOrderDetails->id);

        $this->assertApiResponse($purchaseOrderDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePurchaseOrderDetails()
    {
        $purchaseOrderDetails = $this->makePurchaseOrderDetails();
        $editedPurchaseOrderDetails = $this->fakePurchaseOrderDetailsData();

        $this->json('PUT', '/api/v1/purchaseOrderDetails/'.$purchaseOrderDetails->id, $editedPurchaseOrderDetails);

        $this->assertApiResponse($editedPurchaseOrderDetails);
    }

    /**
     * @test
     */
    public function testDeletePurchaseOrderDetails()
    {
        $purchaseOrderDetails = $this->makePurchaseOrderDetails();
        $this->json('DELETE', '/api/v1/purchaseOrderDetails/'.$purchaseOrderDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/purchaseOrderDetails/'.$purchaseOrderDetails->id);

        $this->assertResponseStatus(404);
    }
}

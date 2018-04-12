<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderProcessDetailsApiTest extends TestCase
{
    use MakePurchaseOrderProcessDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePurchaseOrderProcessDetails()
    {
        $purchaseOrderProcessDetails = $this->fakePurchaseOrderProcessDetailsData();
        $this->json('POST', '/api/v1/purchaseOrderProcessDetails', $purchaseOrderProcessDetails);

        $this->assertApiResponse($purchaseOrderProcessDetails);
    }

    /**
     * @test
     */
    public function testReadPurchaseOrderProcessDetails()
    {
        $purchaseOrderProcessDetails = $this->makePurchaseOrderProcessDetails();
        $this->json('GET', '/api/v1/purchaseOrderProcessDetails/'.$purchaseOrderProcessDetails->id);

        $this->assertApiResponse($purchaseOrderProcessDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePurchaseOrderProcessDetails()
    {
        $purchaseOrderProcessDetails = $this->makePurchaseOrderProcessDetails();
        $editedPurchaseOrderProcessDetails = $this->fakePurchaseOrderProcessDetailsData();

        $this->json('PUT', '/api/v1/purchaseOrderProcessDetails/'.$purchaseOrderProcessDetails->id, $editedPurchaseOrderProcessDetails);

        $this->assertApiResponse($editedPurchaseOrderProcessDetails);
    }

    /**
     * @test
     */
    public function testDeletePurchaseOrderProcessDetails()
    {
        $purchaseOrderProcessDetails = $this->makePurchaseOrderProcessDetails();
        $this->json('DELETE', '/api/v1/purchaseOrderProcessDetails/'.$purchaseOrderProcessDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/purchaseOrderProcessDetails/'.$purchaseOrderProcessDetails->id);

        $this->assertResponseStatus(404);
    }
}

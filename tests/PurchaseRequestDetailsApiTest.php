<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseRequestDetailsApiTest extends TestCase
{
    use MakePurchaseRequestDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePurchaseRequestDetails()
    {
        $purchaseRequestDetails = $this->fakePurchaseRequestDetailsData();
        $this->json('POST', '/api/v1/purchaseRequestDetails', $purchaseRequestDetails);

        $this->assertApiResponse($purchaseRequestDetails);
    }

    /**
     * @test
     */
    public function testReadPurchaseRequestDetails()
    {
        $purchaseRequestDetails = $this->makePurchaseRequestDetails();
        $this->json('GET', '/api/v1/purchaseRequestDetails/'.$purchaseRequestDetails->id);

        $this->assertApiResponse($purchaseRequestDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePurchaseRequestDetails()
    {
        $purchaseRequestDetails = $this->makePurchaseRequestDetails();
        $editedPurchaseRequestDetails = $this->fakePurchaseRequestDetailsData();

        $this->json('PUT', '/api/v1/purchaseRequestDetails/'.$purchaseRequestDetails->id, $editedPurchaseRequestDetails);

        $this->assertApiResponse($editedPurchaseRequestDetails);
    }

    /**
     * @test
     */
    public function testDeletePurchaseRequestDetails()
    {
        $purchaseRequestDetails = $this->makePurchaseRequestDetails();
        $this->json('DELETE', '/api/v1/purchaseRequestDetails/'.$purchaseRequestDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/purchaseRequestDetails/'.$purchaseRequestDetails->id);

        $this->assertResponseStatus(404);
    }
}

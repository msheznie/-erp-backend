<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseReturnDetailsApiTest extends TestCase
{
    use MakePurchaseReturnDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePurchaseReturnDetails()
    {
        $purchaseReturnDetails = $this->fakePurchaseReturnDetailsData();
        $this->json('POST', '/api/v1/purchaseReturnDetails', $purchaseReturnDetails);

        $this->assertApiResponse($purchaseReturnDetails);
    }

    /**
     * @test
     */
    public function testReadPurchaseReturnDetails()
    {
        $purchaseReturnDetails = $this->makePurchaseReturnDetails();
        $this->json('GET', '/api/v1/purchaseReturnDetails/'.$purchaseReturnDetails->id);

        $this->assertApiResponse($purchaseReturnDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePurchaseReturnDetails()
    {
        $purchaseReturnDetails = $this->makePurchaseReturnDetails();
        $editedPurchaseReturnDetails = $this->fakePurchaseReturnDetailsData();

        $this->json('PUT', '/api/v1/purchaseReturnDetails/'.$purchaseReturnDetails->id, $editedPurchaseReturnDetails);

        $this->assertApiResponse($editedPurchaseReturnDetails);
    }

    /**
     * @test
     */
    public function testDeletePurchaseReturnDetails()
    {
        $purchaseReturnDetails = $this->makePurchaseReturnDetails();
        $this->json('DELETE', '/api/v1/purchaseReturnDetails/'.$purchaseReturnDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/purchaseReturnDetails/'.$purchaseReturnDetails->id);

        $this->assertResponseStatus(404);
    }
}

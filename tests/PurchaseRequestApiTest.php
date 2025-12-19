<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseRequestApiTest extends TestCase
{
    use MakePurchaseRequestTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePurchaseRequest()
    {
        $purchaseRequest = $this->fakePurchaseRequestData();
        $this->json('POST', '/api/v1/purchaseRequests', $purchaseRequest);

        $this->assertApiResponse($purchaseRequest);
    }

    /**
     * @test
     */
    public function testReadPurchaseRequest()
    {
        $purchaseRequest = $this->makePurchaseRequest();
        $this->json('GET', '/api/v1/purchaseRequests/'.$purchaseRequest->id);

        $this->assertApiResponse($purchaseRequest->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePurchaseRequest()
    {
        $purchaseRequest = $this->makePurchaseRequest();
        $editedPurchaseRequest = $this->fakePurchaseRequestData();

        $this->json('PUT', '/api/v1/purchaseRequests/'.$purchaseRequest->id, $editedPurchaseRequest);

        $this->assertApiResponse($editedPurchaseRequest);
    }

    /**
     * @test
     */
    public function testDeletePurchaseRequest()
    {
        $purchaseRequest = $this->makePurchaseRequest();
        $this->json('DELETE', '/api/v1/purchaseRequests/'.$purchaseRequest->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/purchaseRequests/'.$purchaseRequest->id);

        $this->assertResponseStatus(404);
    }
}

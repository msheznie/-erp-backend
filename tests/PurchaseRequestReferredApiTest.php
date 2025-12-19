<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseRequestReferredApiTest extends TestCase
{
    use MakePurchaseRequestReferredTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePurchaseRequestReferred()
    {
        $purchaseRequestReferred = $this->fakePurchaseRequestReferredData();
        $this->json('POST', '/api/v1/purchaseRequestReferreds', $purchaseRequestReferred);

        $this->assertApiResponse($purchaseRequestReferred);
    }

    /**
     * @test
     */
    public function testReadPurchaseRequestReferred()
    {
        $purchaseRequestReferred = $this->makePurchaseRequestReferred();
        $this->json('GET', '/api/v1/purchaseRequestReferreds/'.$purchaseRequestReferred->id);

        $this->assertApiResponse($purchaseRequestReferred->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePurchaseRequestReferred()
    {
        $purchaseRequestReferred = $this->makePurchaseRequestReferred();
        $editedPurchaseRequestReferred = $this->fakePurchaseRequestReferredData();

        $this->json('PUT', '/api/v1/purchaseRequestReferreds/'.$purchaseRequestReferred->id, $editedPurchaseRequestReferred);

        $this->assertApiResponse($editedPurchaseRequestReferred);
    }

    /**
     * @test
     */
    public function testDeletePurchaseRequestReferred()
    {
        $purchaseRequestReferred = $this->makePurchaseRequestReferred();
        $this->json('DELETE', '/api/v1/purchaseRequestReferreds/'.$purchaseRequestReferred->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/purchaseRequestReferreds/'.$purchaseRequestReferred->id);

        $this->assertResponseStatus(404);
    }
}

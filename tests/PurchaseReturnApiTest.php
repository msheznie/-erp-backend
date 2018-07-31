<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseReturnApiTest extends TestCase
{
    use MakePurchaseReturnTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePurchaseReturn()
    {
        $purchaseReturn = $this->fakePurchaseReturnData();
        $this->json('POST', '/api/v1/purchaseReturns', $purchaseReturn);

        $this->assertApiResponse($purchaseReturn);
    }

    /**
     * @test
     */
    public function testReadPurchaseReturn()
    {
        $purchaseReturn = $this->makePurchaseReturn();
        $this->json('GET', '/api/v1/purchaseReturns/'.$purchaseReturn->id);

        $this->assertApiResponse($purchaseReturn->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePurchaseReturn()
    {
        $purchaseReturn = $this->makePurchaseReturn();
        $editedPurchaseReturn = $this->fakePurchaseReturnData();

        $this->json('PUT', '/api/v1/purchaseReturns/'.$purchaseReturn->id, $editedPurchaseReturn);

        $this->assertApiResponse($editedPurchaseReturn);
    }

    /**
     * @test
     */
    public function testDeletePurchaseReturn()
    {
        $purchaseReturn = $this->makePurchaseReturn();
        $this->json('DELETE', '/api/v1/purchaseReturns/'.$purchaseReturn->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/purchaseReturns/'.$purchaseReturn->id);

        $this->assertResponseStatus(404);
    }
}

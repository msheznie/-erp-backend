<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseOrderAdvPaymentRefferedbackApiTest extends TestCase
{
    use MakePurchaseOrderAdvPaymentRefferedbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePurchaseOrderAdvPaymentRefferedback()
    {
        $purchaseOrderAdvPaymentRefferedback = $this->fakePurchaseOrderAdvPaymentRefferedbackData();
        $this->json('POST', '/api/v1/purchaseOrderAdvPaymentRefferedbacks', $purchaseOrderAdvPaymentRefferedback);

        $this->assertApiResponse($purchaseOrderAdvPaymentRefferedback);
    }

    /**
     * @test
     */
    public function testReadPurchaseOrderAdvPaymentRefferedback()
    {
        $purchaseOrderAdvPaymentRefferedback = $this->makePurchaseOrderAdvPaymentRefferedback();
        $this->json('GET', '/api/v1/purchaseOrderAdvPaymentRefferedbacks/'.$purchaseOrderAdvPaymentRefferedback->id);

        $this->assertApiResponse($purchaseOrderAdvPaymentRefferedback->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePurchaseOrderAdvPaymentRefferedback()
    {
        $purchaseOrderAdvPaymentRefferedback = $this->makePurchaseOrderAdvPaymentRefferedback();
        $editedPurchaseOrderAdvPaymentRefferedback = $this->fakePurchaseOrderAdvPaymentRefferedbackData();

        $this->json('PUT', '/api/v1/purchaseOrderAdvPaymentRefferedbacks/'.$purchaseOrderAdvPaymentRefferedback->id, $editedPurchaseOrderAdvPaymentRefferedback);

        $this->assertApiResponse($editedPurchaseOrderAdvPaymentRefferedback);
    }

    /**
     * @test
     */
    public function testDeletePurchaseOrderAdvPaymentRefferedback()
    {
        $purchaseOrderAdvPaymentRefferedback = $this->makePurchaseOrderAdvPaymentRefferedback();
        $this->json('DELETE', '/api/v1/purchaseOrderAdvPaymentRefferedbacks/'.$purchaseOrderAdvPaymentRefferedback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/purchaseOrderAdvPaymentRefferedbacks/'.$purchaseOrderAdvPaymentRefferedback->id);

        $this->assertResponseStatus(404);
    }
}

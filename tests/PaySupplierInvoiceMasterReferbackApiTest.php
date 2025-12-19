<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaySupplierInvoiceMasterReferbackApiTest extends TestCase
{
    use MakePaySupplierInvoiceMasterReferbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePaySupplierInvoiceMasterReferback()
    {
        $paySupplierInvoiceMasterReferback = $this->fakePaySupplierInvoiceMasterReferbackData();
        $this->json('POST', '/api/v1/paySupplierInvoiceMasterReferbacks', $paySupplierInvoiceMasterReferback);

        $this->assertApiResponse($paySupplierInvoiceMasterReferback);
    }

    /**
     * @test
     */
    public function testReadPaySupplierInvoiceMasterReferback()
    {
        $paySupplierInvoiceMasterReferback = $this->makePaySupplierInvoiceMasterReferback();
        $this->json('GET', '/api/v1/paySupplierInvoiceMasterReferbacks/'.$paySupplierInvoiceMasterReferback->id);

        $this->assertApiResponse($paySupplierInvoiceMasterReferback->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePaySupplierInvoiceMasterReferback()
    {
        $paySupplierInvoiceMasterReferback = $this->makePaySupplierInvoiceMasterReferback();
        $editedPaySupplierInvoiceMasterReferback = $this->fakePaySupplierInvoiceMasterReferbackData();

        $this->json('PUT', '/api/v1/paySupplierInvoiceMasterReferbacks/'.$paySupplierInvoiceMasterReferback->id, $editedPaySupplierInvoiceMasterReferback);

        $this->assertApiResponse($editedPaySupplierInvoiceMasterReferback);
    }

    /**
     * @test
     */
    public function testDeletePaySupplierInvoiceMasterReferback()
    {
        $paySupplierInvoiceMasterReferback = $this->makePaySupplierInvoiceMasterReferback();
        $this->json('DELETE', '/api/v1/paySupplierInvoiceMasterReferbacks/'.$paySupplierInvoiceMasterReferback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/paySupplierInvoiceMasterReferbacks/'.$paySupplierInvoiceMasterReferback->id);

        $this->assertResponseStatus(404);
    }
}

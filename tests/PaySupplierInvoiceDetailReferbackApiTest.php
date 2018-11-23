<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaySupplierInvoiceDetailReferbackApiTest extends TestCase
{
    use MakePaySupplierInvoiceDetailReferbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePaySupplierInvoiceDetailReferback()
    {
        $paySupplierInvoiceDetailReferback = $this->fakePaySupplierInvoiceDetailReferbackData();
        $this->json('POST', '/api/v1/paySupplierInvoiceDetailReferbacks', $paySupplierInvoiceDetailReferback);

        $this->assertApiResponse($paySupplierInvoiceDetailReferback);
    }

    /**
     * @test
     */
    public function testReadPaySupplierInvoiceDetailReferback()
    {
        $paySupplierInvoiceDetailReferback = $this->makePaySupplierInvoiceDetailReferback();
        $this->json('GET', '/api/v1/paySupplierInvoiceDetailReferbacks/'.$paySupplierInvoiceDetailReferback->id);

        $this->assertApiResponse($paySupplierInvoiceDetailReferback->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePaySupplierInvoiceDetailReferback()
    {
        $paySupplierInvoiceDetailReferback = $this->makePaySupplierInvoiceDetailReferback();
        $editedPaySupplierInvoiceDetailReferback = $this->fakePaySupplierInvoiceDetailReferbackData();

        $this->json('PUT', '/api/v1/paySupplierInvoiceDetailReferbacks/'.$paySupplierInvoiceDetailReferback->id, $editedPaySupplierInvoiceDetailReferback);

        $this->assertApiResponse($editedPaySupplierInvoiceDetailReferback);
    }

    /**
     * @test
     */
    public function testDeletePaySupplierInvoiceDetailReferback()
    {
        $paySupplierInvoiceDetailReferback = $this->makePaySupplierInvoiceDetailReferback();
        $this->json('DELETE', '/api/v1/paySupplierInvoiceDetailReferbacks/'.$paySupplierInvoiceDetailReferback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/paySupplierInvoiceDetailReferbacks/'.$paySupplierInvoiceDetailReferback->id);

        $this->assertResponseStatus(404);
    }
}

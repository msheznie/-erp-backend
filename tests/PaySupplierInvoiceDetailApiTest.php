<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaySupplierInvoiceDetailApiTest extends TestCase
{
    use MakePaySupplierInvoiceDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePaySupplierInvoiceDetail()
    {
        $paySupplierInvoiceDetail = $this->fakePaySupplierInvoiceDetailData();
        $this->json('POST', '/api/v1/paySupplierInvoiceDetails', $paySupplierInvoiceDetail);

        $this->assertApiResponse($paySupplierInvoiceDetail);
    }

    /**
     * @test
     */
    public function testReadPaySupplierInvoiceDetail()
    {
        $paySupplierInvoiceDetail = $this->makePaySupplierInvoiceDetail();
        $this->json('GET', '/api/v1/paySupplierInvoiceDetails/'.$paySupplierInvoiceDetail->id);

        $this->assertApiResponse($paySupplierInvoiceDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePaySupplierInvoiceDetail()
    {
        $paySupplierInvoiceDetail = $this->makePaySupplierInvoiceDetail();
        $editedPaySupplierInvoiceDetail = $this->fakePaySupplierInvoiceDetailData();

        $this->json('PUT', '/api/v1/paySupplierInvoiceDetails/'.$paySupplierInvoiceDetail->id, $editedPaySupplierInvoiceDetail);

        $this->assertApiResponse($editedPaySupplierInvoiceDetail);
    }

    /**
     * @test
     */
    public function testDeletePaySupplierInvoiceDetail()
    {
        $paySupplierInvoiceDetail = $this->makePaySupplierInvoiceDetail();
        $this->json('DELETE', '/api/v1/paySupplierInvoiceDetails/'.$paySupplierInvoiceDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/paySupplierInvoiceDetails/'.$paySupplierInvoiceDetail->id);

        $this->assertResponseStatus(404);
    }
}

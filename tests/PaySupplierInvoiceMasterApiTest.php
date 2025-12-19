<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PaySupplierInvoiceMasterApiTest extends TestCase
{
    use MakePaySupplierInvoiceMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePaySupplierInvoiceMaster()
    {
        $paySupplierInvoiceMaster = $this->fakePaySupplierInvoiceMasterData();
        $this->json('POST', '/api/v1/paySupplierInvoiceMasters', $paySupplierInvoiceMaster);

        $this->assertApiResponse($paySupplierInvoiceMaster);
    }

    /**
     * @test
     */
    public function testReadPaySupplierInvoiceMaster()
    {
        $paySupplierInvoiceMaster = $this->makePaySupplierInvoiceMaster();
        $this->json('GET', '/api/v1/paySupplierInvoiceMasters/'.$paySupplierInvoiceMaster->id);

        $this->assertApiResponse($paySupplierInvoiceMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePaySupplierInvoiceMaster()
    {
        $paySupplierInvoiceMaster = $this->makePaySupplierInvoiceMaster();
        $editedPaySupplierInvoiceMaster = $this->fakePaySupplierInvoiceMasterData();

        $this->json('PUT', '/api/v1/paySupplierInvoiceMasters/'.$paySupplierInvoiceMaster->id, $editedPaySupplierInvoiceMaster);

        $this->assertApiResponse($editedPaySupplierInvoiceMaster);
    }

    /**
     * @test
     */
    public function testDeletePaySupplierInvoiceMaster()
    {
        $paySupplierInvoiceMaster = $this->makePaySupplierInvoiceMaster();
        $this->json('DELETE', '/api/v1/paySupplierInvoiceMasters/'.$paySupplierInvoiceMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/paySupplierInvoiceMasters/'.$paySupplierInvoiceMaster->id);

        $this->assertResponseStatus(404);
    }
}

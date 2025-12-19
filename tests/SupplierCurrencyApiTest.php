<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierCurrencyApiTest extends TestCase
{
    use MakeSupplierCurrencyTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSupplierCurrency()
    {
        $supplierCurrency = $this->fakeSupplierCurrencyData();
        $this->json('POST', '/api/v1/supplierCurrencies', $supplierCurrency);

        $this->assertApiResponse($supplierCurrency);
    }

    /**
     * @test
     */
    public function testReadSupplierCurrency()
    {
        $supplierCurrency = $this->makeSupplierCurrency();
        $this->json('GET', '/api/v1/supplierCurrencies/'.$supplierCurrency->id);

        $this->assertApiResponse($supplierCurrency->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSupplierCurrency()
    {
        $supplierCurrency = $this->makeSupplierCurrency();
        $editedSupplierCurrency = $this->fakeSupplierCurrencyData();

        $this->json('PUT', '/api/v1/supplierCurrencies/'.$supplierCurrency->id, $editedSupplierCurrency);

        $this->assertApiResponse($editedSupplierCurrency);
    }

    /**
     * @test
     */
    public function testDeleteSupplierCurrency()
    {
        $supplierCurrency = $this->makeSupplierCurrency();
        $this->json('DELETE', '/api/v1/supplierCurrencies/'.$supplierCurrency->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/supplierCurrencies/'.$supplierCurrency->id);

        $this->assertResponseStatus(404);
    }
}

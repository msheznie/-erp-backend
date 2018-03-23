<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerCurrencyApiTest extends TestCase
{
    use MakeCustomerCurrencyTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerCurrency()
    {
        $customerCurrency = $this->fakeCustomerCurrencyData();
        $this->json('POST', '/api/v1/customerCurrencies', $customerCurrency);

        $this->assertApiResponse($customerCurrency);
    }

    /**
     * @test
     */
    public function testReadCustomerCurrency()
    {
        $customerCurrency = $this->makeCustomerCurrency();
        $this->json('GET', '/api/v1/customerCurrencies/'.$customerCurrency->id);

        $this->assertApiResponse($customerCurrency->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerCurrency()
    {
        $customerCurrency = $this->makeCustomerCurrency();
        $editedCustomerCurrency = $this->fakeCustomerCurrencyData();

        $this->json('PUT', '/api/v1/customerCurrencies/'.$customerCurrency->id, $editedCustomerCurrency);

        $this->assertApiResponse($editedCustomerCurrency);
    }

    /**
     * @test
     */
    public function testDeleteCustomerCurrency()
    {
        $customerCurrency = $this->makeCustomerCurrency();
        $this->json('DELETE', '/api/v1/customerCurrencies/'.$customerCurrency->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerCurrencies/'.$customerCurrency->id);

        $this->assertResponseStatus(404);
    }
}

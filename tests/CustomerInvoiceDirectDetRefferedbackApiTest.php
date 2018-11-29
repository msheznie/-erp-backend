<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerInvoiceDirectDetRefferedbackApiTest extends TestCase
{
    use MakeCustomerInvoiceDirectDetRefferedbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerInvoiceDirectDetRefferedback()
    {
        $customerInvoiceDirectDetRefferedback = $this->fakeCustomerInvoiceDirectDetRefferedbackData();
        $this->json('POST', '/api/v1/customerInvoiceDirectDetRefferedbacks', $customerInvoiceDirectDetRefferedback);

        $this->assertApiResponse($customerInvoiceDirectDetRefferedback);
    }

    /**
     * @test
     */
    public function testReadCustomerInvoiceDirectDetRefferedback()
    {
        $customerInvoiceDirectDetRefferedback = $this->makeCustomerInvoiceDirectDetRefferedback();
        $this->json('GET', '/api/v1/customerInvoiceDirectDetRefferedbacks/'.$customerInvoiceDirectDetRefferedback->id);

        $this->assertApiResponse($customerInvoiceDirectDetRefferedback->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerInvoiceDirectDetRefferedback()
    {
        $customerInvoiceDirectDetRefferedback = $this->makeCustomerInvoiceDirectDetRefferedback();
        $editedCustomerInvoiceDirectDetRefferedback = $this->fakeCustomerInvoiceDirectDetRefferedbackData();

        $this->json('PUT', '/api/v1/customerInvoiceDirectDetRefferedbacks/'.$customerInvoiceDirectDetRefferedback->id, $editedCustomerInvoiceDirectDetRefferedback);

        $this->assertApiResponse($editedCustomerInvoiceDirectDetRefferedback);
    }

    /**
     * @test
     */
    public function testDeleteCustomerInvoiceDirectDetRefferedback()
    {
        $customerInvoiceDirectDetRefferedback = $this->makeCustomerInvoiceDirectDetRefferedback();
        $this->json('DELETE', '/api/v1/customerInvoiceDirectDetRefferedbacks/'.$customerInvoiceDirectDetRefferedback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerInvoiceDirectDetRefferedbacks/'.$customerInvoiceDirectDetRefferedback->id);

        $this->assertResponseStatus(404);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerInvoiceDirectRefferedbackApiTest extends TestCase
{
    use MakeCustomerInvoiceDirectRefferedbackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerInvoiceDirectRefferedback()
    {
        $customerInvoiceDirectRefferedback = $this->fakeCustomerInvoiceDirectRefferedbackData();
        $this->json('POST', '/api/v1/customerInvoiceDirectRefferedbacks', $customerInvoiceDirectRefferedback);

        $this->assertApiResponse($customerInvoiceDirectRefferedback);
    }

    /**
     * @test
     */
    public function testReadCustomerInvoiceDirectRefferedback()
    {
        $customerInvoiceDirectRefferedback = $this->makeCustomerInvoiceDirectRefferedback();
        $this->json('GET', '/api/v1/customerInvoiceDirectRefferedbacks/'.$customerInvoiceDirectRefferedback->id);

        $this->assertApiResponse($customerInvoiceDirectRefferedback->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerInvoiceDirectRefferedback()
    {
        $customerInvoiceDirectRefferedback = $this->makeCustomerInvoiceDirectRefferedback();
        $editedCustomerInvoiceDirectRefferedback = $this->fakeCustomerInvoiceDirectRefferedbackData();

        $this->json('PUT', '/api/v1/customerInvoiceDirectRefferedbacks/'.$customerInvoiceDirectRefferedback->id, $editedCustomerInvoiceDirectRefferedback);

        $this->assertApiResponse($editedCustomerInvoiceDirectRefferedback);
    }

    /**
     * @test
     */
    public function testDeleteCustomerInvoiceDirectRefferedback()
    {
        $customerInvoiceDirectRefferedback = $this->makeCustomerInvoiceDirectRefferedback();
        $this->json('DELETE', '/api/v1/customerInvoiceDirectRefferedbacks/'.$customerInvoiceDirectRefferedback->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerInvoiceDirectRefferedbacks/'.$customerInvoiceDirectRefferedback->id);

        $this->assertResponseStatus(404);
    }
}

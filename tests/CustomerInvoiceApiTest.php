<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerInvoiceApiTest extends TestCase
{
    use MakeCustomerInvoiceTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerInvoice()
    {
        $customerInvoice = $this->fakeCustomerInvoiceData();
        $this->json('POST', '/api/v1/customerInvoices', $customerInvoice);

        $this->assertApiResponse($customerInvoice);
    }

    /**
     * @test
     */
    public function testReadCustomerInvoice()
    {
        $customerInvoice = $this->makeCustomerInvoice();
        $this->json('GET', '/api/v1/customerInvoices/'.$customerInvoice->id);

        $this->assertApiResponse($customerInvoice->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerInvoice()
    {
        $customerInvoice = $this->makeCustomerInvoice();
        $editedCustomerInvoice = $this->fakeCustomerInvoiceData();

        $this->json('PUT', '/api/v1/customerInvoices/'.$customerInvoice->id, $editedCustomerInvoice);

        $this->assertApiResponse($editedCustomerInvoice);
    }

    /**
     * @test
     */
    public function testDeleteCustomerInvoice()
    {
        $customerInvoice = $this->makeCustomerInvoice();
        $this->json('DELETE', '/api/v1/customerInvoices/'.$customerInvoice->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerInvoices/'.$customerInvoice->id);

        $this->assertResponseStatus(404);
    }
}

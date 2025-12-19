<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerInvoiceDirectApiTest extends TestCase
{
    use MakeCustomerInvoiceDirectTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerInvoiceDirect()
    {
        $customerInvoiceDirect = $this->fakeCustomerInvoiceDirectData();
        $this->json('POST', '/api/v1/customerInvoiceDirects', $customerInvoiceDirect);

        $this->assertApiResponse($customerInvoiceDirect);
    }

    /**
     * @test
     */
    public function testReadCustomerInvoiceDirect()
    {
        $customerInvoiceDirect = $this->makeCustomerInvoiceDirect();
        $this->json('GET', '/api/v1/customerInvoiceDirects/'.$customerInvoiceDirect->id);

        $this->assertApiResponse($customerInvoiceDirect->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerInvoiceDirect()
    {
        $customerInvoiceDirect = $this->makeCustomerInvoiceDirect();
        $editedCustomerInvoiceDirect = $this->fakeCustomerInvoiceDirectData();

        $this->json('PUT', '/api/v1/customerInvoiceDirects/'.$customerInvoiceDirect->id, $editedCustomerInvoiceDirect);

        $this->assertApiResponse($editedCustomerInvoiceDirect);
    }

    /**
     * @test
     */
    public function testDeleteCustomerInvoiceDirect()
    {
        $customerInvoiceDirect = $this->makeCustomerInvoiceDirect();
        $this->json('DELETE', '/api/v1/customerInvoiceDirects/'.$customerInvoiceDirect->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerInvoiceDirects/'.$customerInvoiceDirect->id);

        $this->assertResponseStatus(404);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerInvoiceDirectDetailApiTest extends TestCase
{
    use MakeCustomerInvoiceDirectDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerInvoiceDirectDetail()
    {
        $customerInvoiceDirectDetail = $this->fakeCustomerInvoiceDirectDetailData();
        $this->json('POST', '/api/v1/customerInvoiceDirectDetails', $customerInvoiceDirectDetail);

        $this->assertApiResponse($customerInvoiceDirectDetail);
    }

    /**
     * @test
     */
    public function testReadCustomerInvoiceDirectDetail()
    {
        $customerInvoiceDirectDetail = $this->makeCustomerInvoiceDirectDetail();
        $this->json('GET', '/api/v1/customerInvoiceDirectDetails/'.$customerInvoiceDirectDetail->id);

        $this->assertApiResponse($customerInvoiceDirectDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerInvoiceDirectDetail()
    {
        $customerInvoiceDirectDetail = $this->makeCustomerInvoiceDirectDetail();
        $editedCustomerInvoiceDirectDetail = $this->fakeCustomerInvoiceDirectDetailData();

        $this->json('PUT', '/api/v1/customerInvoiceDirectDetails/'.$customerInvoiceDirectDetail->id, $editedCustomerInvoiceDirectDetail);

        $this->assertApiResponse($editedCustomerInvoiceDirectDetail);
    }

    /**
     * @test
     */
    public function testDeleteCustomerInvoiceDirectDetail()
    {
        $customerInvoiceDirectDetail = $this->makeCustomerInvoiceDirectDetail();
        $this->json('DELETE', '/api/v1/customerInvoiceDirectDetails/'.$customerInvoiceDirectDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerInvoiceDirectDetails/'.$customerInvoiceDirectDetail->id);

        $this->assertResponseStatus(404);
    }
}

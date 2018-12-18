<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerInvoiceCollectionDetailApiTest extends TestCase
{
    use MakeCustomerInvoiceCollectionDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCustomerInvoiceCollectionDetail()
    {
        $customerInvoiceCollectionDetail = $this->fakeCustomerInvoiceCollectionDetailData();
        $this->json('POST', '/api/v1/customerInvoiceCollectionDetails', $customerInvoiceCollectionDetail);

        $this->assertApiResponse($customerInvoiceCollectionDetail);
    }

    /**
     * @test
     */
    public function testReadCustomerInvoiceCollectionDetail()
    {
        $customerInvoiceCollectionDetail = $this->makeCustomerInvoiceCollectionDetail();
        $this->json('GET', '/api/v1/customerInvoiceCollectionDetails/'.$customerInvoiceCollectionDetail->id);

        $this->assertApiResponse($customerInvoiceCollectionDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCustomerInvoiceCollectionDetail()
    {
        $customerInvoiceCollectionDetail = $this->makeCustomerInvoiceCollectionDetail();
        $editedCustomerInvoiceCollectionDetail = $this->fakeCustomerInvoiceCollectionDetailData();

        $this->json('PUT', '/api/v1/customerInvoiceCollectionDetails/'.$customerInvoiceCollectionDetail->id, $editedCustomerInvoiceCollectionDetail);

        $this->assertApiResponse($editedCustomerInvoiceCollectionDetail);
    }

    /**
     * @test
     */
    public function testDeleteCustomerInvoiceCollectionDetail()
    {
        $customerInvoiceCollectionDetail = $this->makeCustomerInvoiceCollectionDetail();
        $this->json('DELETE', '/api/v1/customerInvoiceCollectionDetails/'.$customerInvoiceCollectionDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/customerInvoiceCollectionDetails/'.$customerInvoiceCollectionDetail->id);

        $this->assertResponseStatus(404);
    }
}

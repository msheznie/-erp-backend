<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeCustomerInvoiceItemDetailsTrait;
use Tests\ApiTestTrait;

class CustomerInvoiceItemDetailsApiTest extends TestCase
{
    use MakeCustomerInvoiceItemDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_customer_invoice_item_details()
    {
        $customerInvoiceItemDetails = $this->fakeCustomerInvoiceItemDetailsData();
        $this->response = $this->json('POST', '/api/customerInvoiceItemDetails', $customerInvoiceItemDetails);

        $this->assertApiResponse($customerInvoiceItemDetails);
    }

    /**
     * @test
     */
    public function test_read_customer_invoice_item_details()
    {
        $customerInvoiceItemDetails = $this->makeCustomerInvoiceItemDetails();
        $this->response = $this->json('GET', '/api/customerInvoiceItemDetails/'.$customerInvoiceItemDetails->id);

        $this->assertApiResponse($customerInvoiceItemDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_customer_invoice_item_details()
    {
        $customerInvoiceItemDetails = $this->makeCustomerInvoiceItemDetails();
        $editedCustomerInvoiceItemDetails = $this->fakeCustomerInvoiceItemDetailsData();

        $this->response = $this->json('PUT', '/api/customerInvoiceItemDetails/'.$customerInvoiceItemDetails->id, $editedCustomerInvoiceItemDetails);

        $this->assertApiResponse($editedCustomerInvoiceItemDetails);
    }

    /**
     * @test
     */
    public function test_delete_customer_invoice_item_details()
    {
        $customerInvoiceItemDetails = $this->makeCustomerInvoiceItemDetails();
        $this->response = $this->json('DELETE', '/api/customerInvoiceItemDetails/'.$customerInvoiceItemDetails->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/customerInvoiceItemDetails/'.$customerInvoiceItemDetails->id);

        $this->response->assertStatus(404);
    }
}

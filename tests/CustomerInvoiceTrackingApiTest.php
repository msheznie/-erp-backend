<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeCustomerInvoiceTrackingTrait;
use Tests\ApiTestTrait;

class CustomerInvoiceTrackingApiTest extends TestCase
{
    use MakeCustomerInvoiceTrackingTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_customer_invoice_tracking()
    {
        $customerInvoiceTracking = $this->fakeCustomerInvoiceTrackingData();
        $this->response = $this->json('POST', '/api/customerInvoiceTrackings', $customerInvoiceTracking);

        $this->assertApiResponse($customerInvoiceTracking);
    }

    /**
     * @test
     */
    public function test_read_customer_invoice_tracking()
    {
        $customerInvoiceTracking = $this->makeCustomerInvoiceTracking();
        $this->response = $this->json('GET', '/api/customerInvoiceTrackings/'.$customerInvoiceTracking->id);

        $this->assertApiResponse($customerInvoiceTracking->toArray());
    }

    /**
     * @test
     */
    public function test_update_customer_invoice_tracking()
    {
        $customerInvoiceTracking = $this->makeCustomerInvoiceTracking();
        $editedCustomerInvoiceTracking = $this->fakeCustomerInvoiceTrackingData();

        $this->response = $this->json('PUT', '/api/customerInvoiceTrackings/'.$customerInvoiceTracking->id, $editedCustomerInvoiceTracking);

        $this->assertApiResponse($editedCustomerInvoiceTracking);
    }

    /**
     * @test
     */
    public function test_delete_customer_invoice_tracking()
    {
        $customerInvoiceTracking = $this->makeCustomerInvoiceTracking();
        $this->response = $this->json('DELETE', '/api/customerInvoiceTrackings/'.$customerInvoiceTracking->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/customerInvoiceTrackings/'.$customerInvoiceTracking->id);

        $this->response->assertStatus(404);
    }
}

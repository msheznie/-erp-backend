<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeCustomerInvoiceTrackingDetailTrait;
use Tests\ApiTestTrait;

class CustomerInvoiceTrackingDetailApiTest extends TestCase
{
    use MakeCustomerInvoiceTrackingDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_customer_invoice_tracking_detail()
    {
        $customerInvoiceTrackingDetail = $this->fakeCustomerInvoiceTrackingDetailData();
        $this->response = $this->json('POST', '/api/customerInvoiceTrackingDetails', $customerInvoiceTrackingDetail);

        $this->assertApiResponse($customerInvoiceTrackingDetail);
    }

    /**
     * @test
     */
    public function test_read_customer_invoice_tracking_detail()
    {
        $customerInvoiceTrackingDetail = $this->makeCustomerInvoiceTrackingDetail();
        $this->response = $this->json('GET', '/api/customerInvoiceTrackingDetails/'.$customerInvoiceTrackingDetail->id);

        $this->assertApiResponse($customerInvoiceTrackingDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_customer_invoice_tracking_detail()
    {
        $customerInvoiceTrackingDetail = $this->makeCustomerInvoiceTrackingDetail();
        $editedCustomerInvoiceTrackingDetail = $this->fakeCustomerInvoiceTrackingDetailData();

        $this->response = $this->json('PUT', '/api/customerInvoiceTrackingDetails/'.$customerInvoiceTrackingDetail->id, $editedCustomerInvoiceTrackingDetail);

        $this->assertApiResponse($editedCustomerInvoiceTrackingDetail);
    }

    /**
     * @test
     */
    public function test_delete_customer_invoice_tracking_detail()
    {
        $customerInvoiceTrackingDetail = $this->makeCustomerInvoiceTrackingDetail();
        $this->response = $this->json('DELETE', '/api/customerInvoiceTrackingDetails/'.$customerInvoiceTrackingDetail->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/customerInvoiceTrackingDetails/'.$customerInvoiceTrackingDetail->id);

        $this->response->assertStatus(404);
    }
}

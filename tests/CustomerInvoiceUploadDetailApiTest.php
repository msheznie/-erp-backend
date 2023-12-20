<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomerInvoiceUploadDetail;

class CustomerInvoiceUploadDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_customer_invoice_upload_detail()
    {
        $customerInvoiceUploadDetail = factory(CustomerInvoiceUploadDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/customer_invoice_upload_details', $customerInvoiceUploadDetail
        );

        $this->assertApiResponse($customerInvoiceUploadDetail);
    }

    /**
     * @test
     */
    public function test_read_customer_invoice_upload_detail()
    {
        $customerInvoiceUploadDetail = factory(CustomerInvoiceUploadDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/customer_invoice_upload_details/'.$customerInvoiceUploadDetail->id
        );

        $this->assertApiResponse($customerInvoiceUploadDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_customer_invoice_upload_detail()
    {
        $customerInvoiceUploadDetail = factory(CustomerInvoiceUploadDetail::class)->create();
        $editedCustomerInvoiceUploadDetail = factory(CustomerInvoiceUploadDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/customer_invoice_upload_details/'.$customerInvoiceUploadDetail->id,
            $editedCustomerInvoiceUploadDetail
        );

        $this->assertApiResponse($editedCustomerInvoiceUploadDetail);
    }

    /**
     * @test
     */
    public function test_delete_customer_invoice_upload_detail()
    {
        $customerInvoiceUploadDetail = factory(CustomerInvoiceUploadDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/customer_invoice_upload_details/'.$customerInvoiceUploadDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/customer_invoice_upload_details/'.$customerInvoiceUploadDetail->id
        );

        $this->response->assertStatus(404);
    }
}

<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\LogUploadCustomerInvoice;

class LogUploadCustomerInvoiceApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_log_upload_customer_invoice()
    {
        $logUploadCustomerInvoice = factory(LogUploadCustomerInvoice::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/log_upload_customer_invoices', $logUploadCustomerInvoice
        );

        $this->assertApiResponse($logUploadCustomerInvoice);
    }

    /**
     * @test
     */
    public function test_read_log_upload_customer_invoice()
    {
        $logUploadCustomerInvoice = factory(LogUploadCustomerInvoice::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/log_upload_customer_invoices/'.$logUploadCustomerInvoice->id
        );

        $this->assertApiResponse($logUploadCustomerInvoice->toArray());
    }

    /**
     * @test
     */
    public function test_update_log_upload_customer_invoice()
    {
        $logUploadCustomerInvoice = factory(LogUploadCustomerInvoice::class)->create();
        $editedLogUploadCustomerInvoice = factory(LogUploadCustomerInvoice::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/log_upload_customer_invoices/'.$logUploadCustomerInvoice->id,
            $editedLogUploadCustomerInvoice
        );

        $this->assertApiResponse($editedLogUploadCustomerInvoice);
    }

    /**
     * @test
     */
    public function test_delete_log_upload_customer_invoice()
    {
        $logUploadCustomerInvoice = factory(LogUploadCustomerInvoice::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/log_upload_customer_invoices/'.$logUploadCustomerInvoice->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/log_upload_customer_invoices/'.$logUploadCustomerInvoice->id
        );

        $this->response->assertStatus(404);
    }
}

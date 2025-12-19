<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\UploadCustomerInvoice;

class UploadCustomerInvoiceApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_upload_customer_invoice()
    {
        $uploadCustomerInvoice = factory(UploadCustomerInvoice::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/upload_customer_invoices', $uploadCustomerInvoice
        );

        $this->assertApiResponse($uploadCustomerInvoice);
    }

    /**
     * @test
     */
    public function test_read_upload_customer_invoice()
    {
        $uploadCustomerInvoice = factory(UploadCustomerInvoice::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/upload_customer_invoices/'.$uploadCustomerInvoice->id
        );

        $this->assertApiResponse($uploadCustomerInvoice->toArray());
    }

    /**
     * @test
     */
    public function test_update_upload_customer_invoice()
    {
        $uploadCustomerInvoice = factory(UploadCustomerInvoice::class)->create();
        $editedUploadCustomerInvoice = factory(UploadCustomerInvoice::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/upload_customer_invoices/'.$uploadCustomerInvoice->id,
            $editedUploadCustomerInvoice
        );

        $this->assertApiResponse($editedUploadCustomerInvoice);
    }

    /**
     * @test
     */
    public function test_delete_upload_customer_invoice()
    {
        $uploadCustomerInvoice = factory(UploadCustomerInvoice::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/upload_customer_invoices/'.$uploadCustomerInvoice->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/upload_customer_invoices/'.$uploadCustomerInvoice->id
        );

        $this->response->assertStatus(404);
    }
}

<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomerInvoiceItemDetailsRefferedback;

class CustomerInvoiceItemDetailsRefferedbackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_customer_invoice_item_details_refferedback()
    {
        $customerInvoiceItemDetailsRefferedback = factory(CustomerInvoiceItemDetailsRefferedback::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/customer_invoice_item_details_refferedbacks', $customerInvoiceItemDetailsRefferedback
        );

        $this->assertApiResponse($customerInvoiceItemDetailsRefferedback);
    }

    /**
     * @test
     */
    public function test_read_customer_invoice_item_details_refferedback()
    {
        $customerInvoiceItemDetailsRefferedback = factory(CustomerInvoiceItemDetailsRefferedback::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/customer_invoice_item_details_refferedbacks/'.$customerInvoiceItemDetailsRefferedback->id
        );

        $this->assertApiResponse($customerInvoiceItemDetailsRefferedback->toArray());
    }

    /**
     * @test
     */
    public function test_update_customer_invoice_item_details_refferedback()
    {
        $customerInvoiceItemDetailsRefferedback = factory(CustomerInvoiceItemDetailsRefferedback::class)->create();
        $editedCustomerInvoiceItemDetailsRefferedback = factory(CustomerInvoiceItemDetailsRefferedback::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/customer_invoice_item_details_refferedbacks/'.$customerInvoiceItemDetailsRefferedback->id,
            $editedCustomerInvoiceItemDetailsRefferedback
        );

        $this->assertApiResponse($editedCustomerInvoiceItemDetailsRefferedback);
    }

    /**
     * @test
     */
    public function test_delete_customer_invoice_item_details_refferedback()
    {
        $customerInvoiceItemDetailsRefferedback = factory(CustomerInvoiceItemDetailsRefferedback::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/customer_invoice_item_details_refferedbacks/'.$customerInvoiceItemDetailsRefferedback->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/customer_invoice_item_details_refferedbacks/'.$customerInvoiceItemDetailsRefferedback->id
        );

        $this->response->assertStatus(404);
    }
}

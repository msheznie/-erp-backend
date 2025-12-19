<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomerInvoiceLogistic;

class CustomerInvoiceLogisticApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_customer_invoice_logistic()
    {
        $customerInvoiceLogistic = factory(CustomerInvoiceLogistic::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/customer_invoice_logistics', $customerInvoiceLogistic
        );

        $this->assertApiResponse($customerInvoiceLogistic);
    }

    /**
     * @test
     */
    public function test_read_customer_invoice_logistic()
    {
        $customerInvoiceLogistic = factory(CustomerInvoiceLogistic::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/customer_invoice_logistics/'.$customerInvoiceLogistic->id
        );

        $this->assertApiResponse($customerInvoiceLogistic->toArray());
    }

    /**
     * @test
     */
    public function test_update_customer_invoice_logistic()
    {
        $customerInvoiceLogistic = factory(CustomerInvoiceLogistic::class)->create();
        $editedCustomerInvoiceLogistic = factory(CustomerInvoiceLogistic::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/customer_invoice_logistics/'.$customerInvoiceLogistic->id,
            $editedCustomerInvoiceLogistic
        );

        $this->assertApiResponse($editedCustomerInvoiceLogistic);
    }

    /**
     * @test
     */
    public function test_delete_customer_invoice_logistic()
    {
        $customerInvoiceLogistic = factory(CustomerInvoiceLogistic::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/customer_invoice_logistics/'.$customerInvoiceLogistic->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/customer_invoice_logistics/'.$customerInvoiceLogistic->id
        );

        $this->response->assertStatus(404);
    }
}

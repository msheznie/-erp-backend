<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomerInvoiceStatusType;

class CustomerInvoiceStatusTypeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_customer_invoice_status_type()
    {
        $customerInvoiceStatusType = factory(CustomerInvoiceStatusType::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/customer_invoice_status_types', $customerInvoiceStatusType
        );

        $this->assertApiResponse($customerInvoiceStatusType);
    }

    /**
     * @test
     */
    public function test_read_customer_invoice_status_type()
    {
        $customerInvoiceStatusType = factory(CustomerInvoiceStatusType::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/customer_invoice_status_types/'.$customerInvoiceStatusType->id
        );

        $this->assertApiResponse($customerInvoiceStatusType->toArray());
    }

    /**
     * @test
     */
    public function test_update_customer_invoice_status_type()
    {
        $customerInvoiceStatusType = factory(CustomerInvoiceStatusType::class)->create();
        $editedCustomerInvoiceStatusType = factory(CustomerInvoiceStatusType::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/customer_invoice_status_types/'.$customerInvoiceStatusType->id,
            $editedCustomerInvoiceStatusType
        );

        $this->assertApiResponse($editedCustomerInvoiceStatusType);
    }

    /**
     * @test
     */
    public function test_delete_customer_invoice_status_type()
    {
        $customerInvoiceStatusType = factory(CustomerInvoiceStatusType::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/customer_invoice_status_types/'.$customerInvoiceStatusType->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/customer_invoice_status_types/'.$customerInvoiceStatusType->id
        );

        $this->response->assertStatus(404);
    }
}

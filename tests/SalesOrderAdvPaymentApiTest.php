<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SalesOrderAdvPayment;

class SalesOrderAdvPaymentApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_sales_order_adv_payment()
    {
        $salesOrderAdvPayment = factory(SalesOrderAdvPayment::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/sales_order_adv_payments', $salesOrderAdvPayment
        );

        $this->assertApiResponse($salesOrderAdvPayment);
    }

    /**
     * @test
     */
    public function test_read_sales_order_adv_payment()
    {
        $salesOrderAdvPayment = factory(SalesOrderAdvPayment::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/sales_order_adv_payments/'.$salesOrderAdvPayment->id
        );

        $this->assertApiResponse($salesOrderAdvPayment->toArray());
    }

    /**
     * @test
     */
    public function test_update_sales_order_adv_payment()
    {
        $salesOrderAdvPayment = factory(SalesOrderAdvPayment::class)->create();
        $editedSalesOrderAdvPayment = factory(SalesOrderAdvPayment::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/sales_order_adv_payments/'.$salesOrderAdvPayment->id,
            $editedSalesOrderAdvPayment
        );

        $this->assertApiResponse($editedSalesOrderAdvPayment);
    }

    /**
     * @test
     */
    public function test_delete_sales_order_adv_payment()
    {
        $salesOrderAdvPayment = factory(SalesOrderAdvPayment::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/sales_order_adv_payments/'.$salesOrderAdvPayment->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/sales_order_adv_payments/'.$salesOrderAdvPayment->id
        );

        $this->response->assertStatus(404);
    }
}

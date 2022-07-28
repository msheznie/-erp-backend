<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSourceMenuSalesPayment;

class POSSourceMenuSalesPaymentApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_source_menu_sales_payment()
    {
        $pOSSourceMenuSalesPayment = factory(POSSourceMenuSalesPayment::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_source_menu_sales_payments', $pOSSourceMenuSalesPayment
        );

        $this->assertApiResponse($pOSSourceMenuSalesPayment);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_source_menu_sales_payment()
    {
        $pOSSourceMenuSalesPayment = factory(POSSourceMenuSalesPayment::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_menu_sales_payments/'.$pOSSourceMenuSalesPayment->id
        );

        $this->assertApiResponse($pOSSourceMenuSalesPayment->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_source_menu_sales_payment()
    {
        $pOSSourceMenuSalesPayment = factory(POSSourceMenuSalesPayment::class)->create();
        $editedPOSSourceMenuSalesPayment = factory(POSSourceMenuSalesPayment::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_source_menu_sales_payments/'.$pOSSourceMenuSalesPayment->id,
            $editedPOSSourceMenuSalesPayment
        );

        $this->assertApiResponse($editedPOSSourceMenuSalesPayment);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_source_menu_sales_payment()
    {
        $pOSSourceMenuSalesPayment = factory(POSSourceMenuSalesPayment::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_source_menu_sales_payments/'.$pOSSourceMenuSalesPayment->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_menu_sales_payments/'.$pOSSourceMenuSalesPayment->id
        );

        $this->response->assertStatus(404);
    }
}

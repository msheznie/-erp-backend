<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSStagMenuSalesPayment;

class POSStagMenuSalesPaymentApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_stag_menu_sales_payment()
    {
        $pOSStagMenuSalesPayment = factory(POSStagMenuSalesPayment::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_stag_menu_sales_payments', $pOSStagMenuSalesPayment
        );

        $this->assertApiResponse($pOSStagMenuSalesPayment);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_stag_menu_sales_payment()
    {
        $pOSStagMenuSalesPayment = factory(POSStagMenuSalesPayment::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_menu_sales_payments/'.$pOSStagMenuSalesPayment->id
        );

        $this->assertApiResponse($pOSStagMenuSalesPayment->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_stag_menu_sales_payment()
    {
        $pOSStagMenuSalesPayment = factory(POSStagMenuSalesPayment::class)->create();
        $editedPOSStagMenuSalesPayment = factory(POSStagMenuSalesPayment::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_stag_menu_sales_payments/'.$pOSStagMenuSalesPayment->id,
            $editedPOSStagMenuSalesPayment
        );

        $this->assertApiResponse($editedPOSStagMenuSalesPayment);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_stag_menu_sales_payment()
    {
        $pOSStagMenuSalesPayment = factory(POSStagMenuSalesPayment::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_stag_menu_sales_payments/'.$pOSStagMenuSalesPayment->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_menu_sales_payments/'.$pOSStagMenuSalesPayment->id
        );

        $this->response->assertStatus(404);
    }
}

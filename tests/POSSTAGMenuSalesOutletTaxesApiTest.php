<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSTAGMenuSalesOutletTaxes;

class POSSTAGMenuSalesOutletTaxesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_t_a_g_menu_sales_outlet_taxes()
    {
        $pOSSTAGMenuSalesOutletTaxes = factory(POSSTAGMenuSalesOutletTaxes::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_t_a_g_menu_sales_outlet_taxes', $pOSSTAGMenuSalesOutletTaxes
        );

        $this->assertApiResponse($pOSSTAGMenuSalesOutletTaxes);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_t_a_g_menu_sales_outlet_taxes()
    {
        $pOSSTAGMenuSalesOutletTaxes = factory(POSSTAGMenuSalesOutletTaxes::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_menu_sales_outlet_taxes/'.$pOSSTAGMenuSalesOutletTaxes->id
        );

        $this->assertApiResponse($pOSSTAGMenuSalesOutletTaxes->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_t_a_g_menu_sales_outlet_taxes()
    {
        $pOSSTAGMenuSalesOutletTaxes = factory(POSSTAGMenuSalesOutletTaxes::class)->create();
        $editedPOSSTAGMenuSalesOutletTaxes = factory(POSSTAGMenuSalesOutletTaxes::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_t_a_g_menu_sales_outlet_taxes/'.$pOSSTAGMenuSalesOutletTaxes->id,
            $editedPOSSTAGMenuSalesOutletTaxes
        );

        $this->assertApiResponse($editedPOSSTAGMenuSalesOutletTaxes);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_t_a_g_menu_sales_outlet_taxes()
    {
        $pOSSTAGMenuSalesOutletTaxes = factory(POSSTAGMenuSalesOutletTaxes::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_t_a_g_menu_sales_outlet_taxes/'.$pOSSTAGMenuSalesOutletTaxes->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_t_a_g_menu_sales_outlet_taxes/'.$pOSSTAGMenuSalesOutletTaxes->id
        );

        $this->response->assertStatus(404);
    }
}

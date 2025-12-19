<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSOURCEMenuSalesOutletTaxes;

class POSSOURCEMenuSalesOutletTaxesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes()
    {
        $pOSSOURCEMenuSalesOutletTaxes = factory(POSSOURCEMenuSalesOutletTaxes::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes', $pOSSOURCEMenuSalesOutletTaxes
        );

        $this->assertApiResponse($pOSSOURCEMenuSalesOutletTaxes);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes()
    {
        $pOSSOURCEMenuSalesOutletTaxes = factory(POSSOURCEMenuSalesOutletTaxes::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes/'.$pOSSOURCEMenuSalesOutletTaxes->id
        );

        $this->assertApiResponse($pOSSOURCEMenuSalesOutletTaxes->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes()
    {
        $pOSSOURCEMenuSalesOutletTaxes = factory(POSSOURCEMenuSalesOutletTaxes::class)->create();
        $editedPOSSOURCEMenuSalesOutletTaxes = factory(POSSOURCEMenuSalesOutletTaxes::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes/'.$pOSSOURCEMenuSalesOutletTaxes->id,
            $editedPOSSOURCEMenuSalesOutletTaxes
        );

        $this->assertApiResponse($editedPOSSOURCEMenuSalesOutletTaxes);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes()
    {
        $pOSSOURCEMenuSalesOutletTaxes = factory(POSSOURCEMenuSalesOutletTaxes::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes/'.$pOSSOURCEMenuSalesOutletTaxes->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_o_u_r_c_e_menu_sales_outlet_taxes/'.$pOSSOURCEMenuSalesOutletTaxes->id
        );

        $this->response->assertStatus(404);
    }
}

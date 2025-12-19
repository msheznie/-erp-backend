<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSOURCEMenuSalesTaxes;

class POSSOURCEMenuSalesTaxesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_s_o_u_r_c_e_menu_sales_taxes()
    {
        $pOSSOURCEMenuSalesTaxes = factory(POSSOURCEMenuSalesTaxes::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_s_o_u_r_c_e_menu_sales_taxes', $pOSSOURCEMenuSalesTaxes
        );

        $this->assertApiResponse($pOSSOURCEMenuSalesTaxes);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_s_o_u_r_c_e_menu_sales_taxes()
    {
        $pOSSOURCEMenuSalesTaxes = factory(POSSOURCEMenuSalesTaxes::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_o_u_r_c_e_menu_sales_taxes/'.$pOSSOURCEMenuSalesTaxes->id
        );

        $this->assertApiResponse($pOSSOURCEMenuSalesTaxes->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_s_o_u_r_c_e_menu_sales_taxes()
    {
        $pOSSOURCEMenuSalesTaxes = factory(POSSOURCEMenuSalesTaxes::class)->create();
        $editedPOSSOURCEMenuSalesTaxes = factory(POSSOURCEMenuSalesTaxes::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_s_o_u_r_c_e_menu_sales_taxes/'.$pOSSOURCEMenuSalesTaxes->id,
            $editedPOSSOURCEMenuSalesTaxes
        );

        $this->assertApiResponse($editedPOSSOURCEMenuSalesTaxes);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_s_o_u_r_c_e_menu_sales_taxes()
    {
        $pOSSOURCEMenuSalesTaxes = factory(POSSOURCEMenuSalesTaxes::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_s_o_u_r_c_e_menu_sales_taxes/'.$pOSSOURCEMenuSalesTaxes->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_s_o_u_r_c_e_menu_sales_taxes/'.$pOSSOURCEMenuSalesTaxes->id
        );

        $this->response->assertStatus(404);
    }
}

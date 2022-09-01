<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSourceMenuSalesServiceCharge;

class POSSourceMenuSalesServiceChargeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_source_menu_sales_service_charge()
    {
        $pOSSourceMenuSalesServiceCharge = factory(POSSourceMenuSalesServiceCharge::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_source_menu_sales_service_charges', $pOSSourceMenuSalesServiceCharge
        );

        $this->assertApiResponse($pOSSourceMenuSalesServiceCharge);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_source_menu_sales_service_charge()
    {
        $pOSSourceMenuSalesServiceCharge = factory(POSSourceMenuSalesServiceCharge::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_menu_sales_service_charges/'.$pOSSourceMenuSalesServiceCharge->id
        );

        $this->assertApiResponse($pOSSourceMenuSalesServiceCharge->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_source_menu_sales_service_charge()
    {
        $pOSSourceMenuSalesServiceCharge = factory(POSSourceMenuSalesServiceCharge::class)->create();
        $editedPOSSourceMenuSalesServiceCharge = factory(POSSourceMenuSalesServiceCharge::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_source_menu_sales_service_charges/'.$pOSSourceMenuSalesServiceCharge->id,
            $editedPOSSourceMenuSalesServiceCharge
        );

        $this->assertApiResponse($editedPOSSourceMenuSalesServiceCharge);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_source_menu_sales_service_charge()
    {
        $pOSSourceMenuSalesServiceCharge = factory(POSSourceMenuSalesServiceCharge::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_source_menu_sales_service_charges/'.$pOSSourceMenuSalesServiceCharge->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_menu_sales_service_charges/'.$pOSSourceMenuSalesServiceCharge->id
        );

        $this->response->assertStatus(404);
    }
}

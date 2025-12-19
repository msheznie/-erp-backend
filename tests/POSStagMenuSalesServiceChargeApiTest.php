<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSStagMenuSalesServiceCharge;

class POSStagMenuSalesServiceChargeApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_stag_menu_sales_service_charge()
    {
        $pOSStagMenuSalesServiceCharge = factory(POSStagMenuSalesServiceCharge::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_stag_menu_sales_service_charges', $pOSStagMenuSalesServiceCharge
        );

        $this->assertApiResponse($pOSStagMenuSalesServiceCharge);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_stag_menu_sales_service_charge()
    {
        $pOSStagMenuSalesServiceCharge = factory(POSStagMenuSalesServiceCharge::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_menu_sales_service_charges/'.$pOSStagMenuSalesServiceCharge->id
        );

        $this->assertApiResponse($pOSStagMenuSalesServiceCharge->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_stag_menu_sales_service_charge()
    {
        $pOSStagMenuSalesServiceCharge = factory(POSStagMenuSalesServiceCharge::class)->create();
        $editedPOSStagMenuSalesServiceCharge = factory(POSStagMenuSalesServiceCharge::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_stag_menu_sales_service_charges/'.$pOSStagMenuSalesServiceCharge->id,
            $editedPOSStagMenuSalesServiceCharge
        );

        $this->assertApiResponse($editedPOSStagMenuSalesServiceCharge);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_stag_menu_sales_service_charge()
    {
        $pOSStagMenuSalesServiceCharge = factory(POSStagMenuSalesServiceCharge::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_stag_menu_sales_service_charges/'.$pOSStagMenuSalesServiceCharge->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_menu_sales_service_charges/'.$pOSStagMenuSalesServiceCharge->id
        );

        $this->response->assertStatus(404);
    }
}

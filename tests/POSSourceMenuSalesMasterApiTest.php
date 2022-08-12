<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSourceMenuSalesMaster;

class POSSourceMenuSalesMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_source_menu_sales_master()
    {
        $pOSSourceMenuSalesMaster = factory(POSSourceMenuSalesMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_source_menu_sales_masters', $pOSSourceMenuSalesMaster
        );

        $this->assertApiResponse($pOSSourceMenuSalesMaster);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_source_menu_sales_master()
    {
        $pOSSourceMenuSalesMaster = factory(POSSourceMenuSalesMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_menu_sales_masters/'.$pOSSourceMenuSalesMaster->id
        );

        $this->assertApiResponse($pOSSourceMenuSalesMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_source_menu_sales_master()
    {
        $pOSSourceMenuSalesMaster = factory(POSSourceMenuSalesMaster::class)->create();
        $editedPOSSourceMenuSalesMaster = factory(POSSourceMenuSalesMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_source_menu_sales_masters/'.$pOSSourceMenuSalesMaster->id,
            $editedPOSSourceMenuSalesMaster
        );

        $this->assertApiResponse($editedPOSSourceMenuSalesMaster);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_source_menu_sales_master()
    {
        $pOSSourceMenuSalesMaster = factory(POSSourceMenuSalesMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_source_menu_sales_masters/'.$pOSSourceMenuSalesMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_menu_sales_masters/'.$pOSSourceMenuSalesMaster->id
        );

        $this->response->assertStatus(404);
    }
}

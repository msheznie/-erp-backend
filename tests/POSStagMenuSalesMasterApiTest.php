<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSStagMenuSalesMaster;

class POSStagMenuSalesMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_stag_menu_sales_master()
    {
        $pOSStagMenuSalesMaster = factory(POSStagMenuSalesMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_stag_menu_sales_masters', $pOSStagMenuSalesMaster
        );

        $this->assertApiResponse($pOSStagMenuSalesMaster);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_stag_menu_sales_master()
    {
        $pOSStagMenuSalesMaster = factory(POSStagMenuSalesMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_menu_sales_masters/'.$pOSStagMenuSalesMaster->id
        );

        $this->assertApiResponse($pOSStagMenuSalesMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_stag_menu_sales_master()
    {
        $pOSStagMenuSalesMaster = factory(POSStagMenuSalesMaster::class)->create();
        $editedPOSStagMenuSalesMaster = factory(POSStagMenuSalesMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_stag_menu_sales_masters/'.$pOSStagMenuSalesMaster->id,
            $editedPOSStagMenuSalesMaster
        );

        $this->assertApiResponse($editedPOSStagMenuSalesMaster);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_stag_menu_sales_master()
    {
        $pOSStagMenuSalesMaster = factory(POSStagMenuSalesMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_stag_menu_sales_masters/'.$pOSStagMenuSalesMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_menu_sales_masters/'.$pOSStagMenuSalesMaster->id
        );

        $this->response->assertStatus(404);
    }
}

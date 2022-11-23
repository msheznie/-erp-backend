<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSStagMenuSalesItem;

class POSStagMenuSalesItemApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_stag_menu_sales_item()
    {
        $pOSStagMenuSalesItem = factory(POSStagMenuSalesItem::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_stag_menu_sales_items', $pOSStagMenuSalesItem
        );

        $this->assertApiResponse($pOSStagMenuSalesItem);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_stag_menu_sales_item()
    {
        $pOSStagMenuSalesItem = factory(POSStagMenuSalesItem::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_menu_sales_items/'.$pOSStagMenuSalesItem->id
        );

        $this->assertApiResponse($pOSStagMenuSalesItem->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_stag_menu_sales_item()
    {
        $pOSStagMenuSalesItem = factory(POSStagMenuSalesItem::class)->create();
        $editedPOSStagMenuSalesItem = factory(POSStagMenuSalesItem::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_stag_menu_sales_items/'.$pOSStagMenuSalesItem->id,
            $editedPOSStagMenuSalesItem
        );

        $this->assertApiResponse($editedPOSStagMenuSalesItem);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_stag_menu_sales_item()
    {
        $pOSStagMenuSalesItem = factory(POSStagMenuSalesItem::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_stag_menu_sales_items/'.$pOSStagMenuSalesItem->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_stag_menu_sales_items/'.$pOSStagMenuSalesItem->id
        );

        $this->response->assertStatus(404);
    }
}

<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\POSSourceMenuSalesItem;

class POSSourceMenuSalesItemApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_p_o_s_source_menu_sales_item()
    {
        $pOSSourceMenuSalesItem = factory(POSSourceMenuSalesItem::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/p_o_s_source_menu_sales_items', $pOSSourceMenuSalesItem
        );

        $this->assertApiResponse($pOSSourceMenuSalesItem);
    }

    /**
     * @test
     */
    public function test_read_p_o_s_source_menu_sales_item()
    {
        $pOSSourceMenuSalesItem = factory(POSSourceMenuSalesItem::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_menu_sales_items/'.$pOSSourceMenuSalesItem->id
        );

        $this->assertApiResponse($pOSSourceMenuSalesItem->toArray());
    }

    /**
     * @test
     */
    public function test_update_p_o_s_source_menu_sales_item()
    {
        $pOSSourceMenuSalesItem = factory(POSSourceMenuSalesItem::class)->create();
        $editedPOSSourceMenuSalesItem = factory(POSSourceMenuSalesItem::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/p_o_s_source_menu_sales_items/'.$pOSSourceMenuSalesItem->id,
            $editedPOSSourceMenuSalesItem
        );

        $this->assertApiResponse($editedPOSSourceMenuSalesItem);
    }

    /**
     * @test
     */
    public function test_delete_p_o_s_source_menu_sales_item()
    {
        $pOSSourceMenuSalesItem = factory(POSSourceMenuSalesItem::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/p_o_s_source_menu_sales_items/'.$pOSSourceMenuSalesItem->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/p_o_s_source_menu_sales_items/'.$pOSSourceMenuSalesItem->id
        );

        $this->response->assertStatus(404);
    }
}

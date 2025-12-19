<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\StockCountDetailsRefferedBack;

class StockCountDetailsRefferedBackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_stock_count_details_reffered_back()
    {
        $stockCountDetailsRefferedBack = factory(StockCountDetailsRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/stock_count_details_reffered_backs', $stockCountDetailsRefferedBack
        );

        $this->assertApiResponse($stockCountDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function test_read_stock_count_details_reffered_back()
    {
        $stockCountDetailsRefferedBack = factory(StockCountDetailsRefferedBack::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/stock_count_details_reffered_backs/'.$stockCountDetailsRefferedBack->id
        );

        $this->assertApiResponse($stockCountDetailsRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function test_update_stock_count_details_reffered_back()
    {
        $stockCountDetailsRefferedBack = factory(StockCountDetailsRefferedBack::class)->create();
        $editedStockCountDetailsRefferedBack = factory(StockCountDetailsRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/stock_count_details_reffered_backs/'.$stockCountDetailsRefferedBack->id,
            $editedStockCountDetailsRefferedBack
        );

        $this->assertApiResponse($editedStockCountDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function test_delete_stock_count_details_reffered_back()
    {
        $stockCountDetailsRefferedBack = factory(StockCountDetailsRefferedBack::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/stock_count_details_reffered_backs/'.$stockCountDetailsRefferedBack->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/stock_count_details_reffered_backs/'.$stockCountDetailsRefferedBack->id
        );

        $this->response->assertStatus(404);
    }
}

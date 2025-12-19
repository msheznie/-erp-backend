<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\StockCountRefferedBack;

class StockCountRefferedBackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_stock_count_reffered_back()
    {
        $stockCountRefferedBack = factory(StockCountRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/stock_count_reffered_backs', $stockCountRefferedBack
        );

        $this->assertApiResponse($stockCountRefferedBack);
    }

    /**
     * @test
     */
    public function test_read_stock_count_reffered_back()
    {
        $stockCountRefferedBack = factory(StockCountRefferedBack::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/stock_count_reffered_backs/'.$stockCountRefferedBack->id
        );

        $this->assertApiResponse($stockCountRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function test_update_stock_count_reffered_back()
    {
        $stockCountRefferedBack = factory(StockCountRefferedBack::class)->create();
        $editedStockCountRefferedBack = factory(StockCountRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/stock_count_reffered_backs/'.$stockCountRefferedBack->id,
            $editedStockCountRefferedBack
        );

        $this->assertApiResponse($editedStockCountRefferedBack);
    }

    /**
     * @test
     */
    public function test_delete_stock_count_reffered_back()
    {
        $stockCountRefferedBack = factory(StockCountRefferedBack::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/stock_count_reffered_backs/'.$stockCountRefferedBack->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/stock_count_reffered_backs/'.$stockCountRefferedBack->id
        );

        $this->response->assertStatus(404);
    }
}

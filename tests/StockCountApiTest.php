<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\StockCount;

class StockCountApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_stock_count()
    {
        $stockCount = factory(StockCount::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/stock_counts', $stockCount
        );

        $this->assertApiResponse($stockCount);
    }

    /**
     * @test
     */
    public function test_read_stock_count()
    {
        $stockCount = factory(StockCount::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/stock_counts/'.$stockCount->id
        );

        $this->assertApiResponse($stockCount->toArray());
    }

    /**
     * @test
     */
    public function test_update_stock_count()
    {
        $stockCount = factory(StockCount::class)->create();
        $editedStockCount = factory(StockCount::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/stock_counts/'.$stockCount->id,
            $editedStockCount
        );

        $this->assertApiResponse($editedStockCount);
    }

    /**
     * @test
     */
    public function test_delete_stock_count()
    {
        $stockCount = factory(StockCount::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/stock_counts/'.$stockCount->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/stock_counts/'.$stockCount->id
        );

        $this->response->assertStatus(404);
    }
}

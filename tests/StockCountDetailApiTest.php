<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\StockCountDetail;

class StockCountDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_stock_count_detail()
    {
        $stockCountDetail = factory(StockCountDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/stock_count_details', $stockCountDetail
        );

        $this->assertApiResponse($stockCountDetail);
    }

    /**
     * @test
     */
    public function test_read_stock_count_detail()
    {
        $stockCountDetail = factory(StockCountDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/stock_count_details/'.$stockCountDetail->id
        );

        $this->assertApiResponse($stockCountDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_stock_count_detail()
    {
        $stockCountDetail = factory(StockCountDetail::class)->create();
        $editedStockCountDetail = factory(StockCountDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/stock_count_details/'.$stockCountDetail->id,
            $editedStockCountDetail
        );

        $this->assertApiResponse($editedStockCountDetail);
    }

    /**
     * @test
     */
    public function test_delete_stock_count_detail()
    {
        $stockCountDetail = factory(StockCountDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/stock_count_details/'.$stockCountDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/stock_count_details/'.$stockCountDetail->id
        );

        $this->response->assertStatus(404);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockAdjustmentApiTest extends TestCase
{
    use MakeStockAdjustmentTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStockAdjustment()
    {
        $stockAdjustment = $this->fakeStockAdjustmentData();
        $this->json('POST', '/api/v1/stockAdjustments', $stockAdjustment);

        $this->assertApiResponse($stockAdjustment);
    }

    /**
     * @test
     */
    public function testReadStockAdjustment()
    {
        $stockAdjustment = $this->makeStockAdjustment();
        $this->json('GET', '/api/v1/stockAdjustments/'.$stockAdjustment->id);

        $this->assertApiResponse($stockAdjustment->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStockAdjustment()
    {
        $stockAdjustment = $this->makeStockAdjustment();
        $editedStockAdjustment = $this->fakeStockAdjustmentData();

        $this->json('PUT', '/api/v1/stockAdjustments/'.$stockAdjustment->id, $editedStockAdjustment);

        $this->assertApiResponse($editedStockAdjustment);
    }

    /**
     * @test
     */
    public function testDeleteStockAdjustment()
    {
        $stockAdjustment = $this->makeStockAdjustment();
        $this->json('DELETE', '/api/v1/stockAdjustments/'.$stockAdjustment->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/stockAdjustments/'.$stockAdjustment->id);

        $this->assertResponseStatus(404);
    }
}

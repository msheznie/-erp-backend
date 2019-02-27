<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockAdjustmentRefferedBackApiTest extends TestCase
{
    use MakeStockAdjustmentRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStockAdjustmentRefferedBack()
    {
        $stockAdjustmentRefferedBack = $this->fakeStockAdjustmentRefferedBackData();
        $this->json('POST', '/api/v1/stockAdjustmentRefferedBacks', $stockAdjustmentRefferedBack);

        $this->assertApiResponse($stockAdjustmentRefferedBack);
    }

    /**
     * @test
     */
    public function testReadStockAdjustmentRefferedBack()
    {
        $stockAdjustmentRefferedBack = $this->makeStockAdjustmentRefferedBack();
        $this->json('GET', '/api/v1/stockAdjustmentRefferedBacks/'.$stockAdjustmentRefferedBack->id);

        $this->assertApiResponse($stockAdjustmentRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStockAdjustmentRefferedBack()
    {
        $stockAdjustmentRefferedBack = $this->makeStockAdjustmentRefferedBack();
        $editedStockAdjustmentRefferedBack = $this->fakeStockAdjustmentRefferedBackData();

        $this->json('PUT', '/api/v1/stockAdjustmentRefferedBacks/'.$stockAdjustmentRefferedBack->id, $editedStockAdjustmentRefferedBack);

        $this->assertApiResponse($editedStockAdjustmentRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteStockAdjustmentRefferedBack()
    {
        $stockAdjustmentRefferedBack = $this->makeStockAdjustmentRefferedBack();
        $this->json('DELETE', '/api/v1/stockAdjustmentRefferedBacks/'.$stockAdjustmentRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/stockAdjustmentRefferedBacks/'.$stockAdjustmentRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}

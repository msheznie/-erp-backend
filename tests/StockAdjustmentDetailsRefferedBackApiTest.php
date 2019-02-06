<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockAdjustmentDetailsRefferedBackApiTest extends TestCase
{
    use MakeStockAdjustmentDetailsRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStockAdjustmentDetailsRefferedBack()
    {
        $stockAdjustmentDetailsRefferedBack = $this->fakeStockAdjustmentDetailsRefferedBackData();
        $this->json('POST', '/api/v1/stockAdjustmentDetailsRefferedBacks', $stockAdjustmentDetailsRefferedBack);

        $this->assertApiResponse($stockAdjustmentDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function testReadStockAdjustmentDetailsRefferedBack()
    {
        $stockAdjustmentDetailsRefferedBack = $this->makeStockAdjustmentDetailsRefferedBack();
        $this->json('GET', '/api/v1/stockAdjustmentDetailsRefferedBacks/'.$stockAdjustmentDetailsRefferedBack->id);

        $this->assertApiResponse($stockAdjustmentDetailsRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStockAdjustmentDetailsRefferedBack()
    {
        $stockAdjustmentDetailsRefferedBack = $this->makeStockAdjustmentDetailsRefferedBack();
        $editedStockAdjustmentDetailsRefferedBack = $this->fakeStockAdjustmentDetailsRefferedBackData();

        $this->json('PUT', '/api/v1/stockAdjustmentDetailsRefferedBacks/'.$stockAdjustmentDetailsRefferedBack->id, $editedStockAdjustmentDetailsRefferedBack);

        $this->assertApiResponse($editedStockAdjustmentDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteStockAdjustmentDetailsRefferedBack()
    {
        $stockAdjustmentDetailsRefferedBack = $this->makeStockAdjustmentDetailsRefferedBack();
        $this->json('DELETE', '/api/v1/stockAdjustmentDetailsRefferedBacks/'.$stockAdjustmentDetailsRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/stockAdjustmentDetailsRefferedBacks/'.$stockAdjustmentDetailsRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}

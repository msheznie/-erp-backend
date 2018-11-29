<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockTransferDetailsRefferedBackApiTest extends TestCase
{
    use MakeStockTransferDetailsRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStockTransferDetailsRefferedBack()
    {
        $stockTransferDetailsRefferedBack = $this->fakeStockTransferDetailsRefferedBackData();
        $this->json('POST', '/api/v1/stockTransferDetailsRefferedBacks', $stockTransferDetailsRefferedBack);

        $this->assertApiResponse($stockTransferDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function testReadStockTransferDetailsRefferedBack()
    {
        $stockTransferDetailsRefferedBack = $this->makeStockTransferDetailsRefferedBack();
        $this->json('GET', '/api/v1/stockTransferDetailsRefferedBacks/'.$stockTransferDetailsRefferedBack->id);

        $this->assertApiResponse($stockTransferDetailsRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStockTransferDetailsRefferedBack()
    {
        $stockTransferDetailsRefferedBack = $this->makeStockTransferDetailsRefferedBack();
        $editedStockTransferDetailsRefferedBack = $this->fakeStockTransferDetailsRefferedBackData();

        $this->json('PUT', '/api/v1/stockTransferDetailsRefferedBacks/'.$stockTransferDetailsRefferedBack->id, $editedStockTransferDetailsRefferedBack);

        $this->assertApiResponse($editedStockTransferDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteStockTransferDetailsRefferedBack()
    {
        $stockTransferDetailsRefferedBack = $this->makeStockTransferDetailsRefferedBack();
        $this->json('DELETE', '/api/v1/stockTransferDetailsRefferedBacks/'.$stockTransferDetailsRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/stockTransferDetailsRefferedBacks/'.$stockTransferDetailsRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}

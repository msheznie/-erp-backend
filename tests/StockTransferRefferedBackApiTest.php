<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockTransferRefferedBackApiTest extends TestCase
{
    use MakeStockTransferRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStockTransferRefferedBack()
    {
        $stockTransferRefferedBack = $this->fakeStockTransferRefferedBackData();
        $this->json('POST', '/api/v1/stockTransferRefferedBacks', $stockTransferRefferedBack);

        $this->assertApiResponse($stockTransferRefferedBack);
    }

    /**
     * @test
     */
    public function testReadStockTransferRefferedBack()
    {
        $stockTransferRefferedBack = $this->makeStockTransferRefferedBack();
        $this->json('GET', '/api/v1/stockTransferRefferedBacks/'.$stockTransferRefferedBack->id);

        $this->assertApiResponse($stockTransferRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStockTransferRefferedBack()
    {
        $stockTransferRefferedBack = $this->makeStockTransferRefferedBack();
        $editedStockTransferRefferedBack = $this->fakeStockTransferRefferedBackData();

        $this->json('PUT', '/api/v1/stockTransferRefferedBacks/'.$stockTransferRefferedBack->id, $editedStockTransferRefferedBack);

        $this->assertApiResponse($editedStockTransferRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteStockTransferRefferedBack()
    {
        $stockTransferRefferedBack = $this->makeStockTransferRefferedBack();
        $this->json('DELETE', '/api/v1/stockTransferRefferedBacks/'.$stockTransferRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/stockTransferRefferedBacks/'.$stockTransferRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}

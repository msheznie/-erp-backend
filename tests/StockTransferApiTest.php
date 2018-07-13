<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockTransferApiTest extends TestCase
{
    use MakeStockTransferTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStockTransfer()
    {
        $stockTransfer = $this->fakeStockTransferData();
        $this->json('POST', '/api/v1/stockTransfers', $stockTransfer);

        $this->assertApiResponse($stockTransfer);
    }

    /**
     * @test
     */
    public function testReadStockTransfer()
    {
        $stockTransfer = $this->makeStockTransfer();
        $this->json('GET', '/api/v1/stockTransfers/'.$stockTransfer->id);

        $this->assertApiResponse($stockTransfer->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStockTransfer()
    {
        $stockTransfer = $this->makeStockTransfer();
        $editedStockTransfer = $this->fakeStockTransferData();

        $this->json('PUT', '/api/v1/stockTransfers/'.$stockTransfer->id, $editedStockTransfer);

        $this->assertApiResponse($editedStockTransfer);
    }

    /**
     * @test
     */
    public function testDeleteStockTransfer()
    {
        $stockTransfer = $this->makeStockTransfer();
        $this->json('DELETE', '/api/v1/stockTransfers/'.$stockTransfer->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/stockTransfers/'.$stockTransfer->id);

        $this->assertResponseStatus(404);
    }
}

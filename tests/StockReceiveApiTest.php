<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockReceiveApiTest extends TestCase
{
    use MakeStockReceiveTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStockReceive()
    {
        $stockReceive = $this->fakeStockReceiveData();
        $this->json('POST', '/api/v1/stockReceives', $stockReceive);

        $this->assertApiResponse($stockReceive);
    }

    /**
     * @test
     */
    public function testReadStockReceive()
    {
        $stockReceive = $this->makeStockReceive();
        $this->json('GET', '/api/v1/stockReceives/'.$stockReceive->id);

        $this->assertApiResponse($stockReceive->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStockReceive()
    {
        $stockReceive = $this->makeStockReceive();
        $editedStockReceive = $this->fakeStockReceiveData();

        $this->json('PUT', '/api/v1/stockReceives/'.$stockReceive->id, $editedStockReceive);

        $this->assertApiResponse($editedStockReceive);
    }

    /**
     * @test
     */
    public function testDeleteStockReceive()
    {
        $stockReceive = $this->makeStockReceive();
        $this->json('DELETE', '/api/v1/stockReceives/'.$stockReceive->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/stockReceives/'.$stockReceive->id);

        $this->assertResponseStatus(404);
    }
}

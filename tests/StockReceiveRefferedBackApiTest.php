<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockReceiveRefferedBackApiTest extends TestCase
{
    use MakeStockReceiveRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStockReceiveRefferedBack()
    {
        $stockReceiveRefferedBack = $this->fakeStockReceiveRefferedBackData();
        $this->json('POST', '/api/v1/stockReceiveRefferedBacks', $stockReceiveRefferedBack);

        $this->assertApiResponse($stockReceiveRefferedBack);
    }

    /**
     * @test
     */
    public function testReadStockReceiveRefferedBack()
    {
        $stockReceiveRefferedBack = $this->makeStockReceiveRefferedBack();
        $this->json('GET', '/api/v1/stockReceiveRefferedBacks/'.$stockReceiveRefferedBack->id);

        $this->assertApiResponse($stockReceiveRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStockReceiveRefferedBack()
    {
        $stockReceiveRefferedBack = $this->makeStockReceiveRefferedBack();
        $editedStockReceiveRefferedBack = $this->fakeStockReceiveRefferedBackData();

        $this->json('PUT', '/api/v1/stockReceiveRefferedBacks/'.$stockReceiveRefferedBack->id, $editedStockReceiveRefferedBack);

        $this->assertApiResponse($editedStockReceiveRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteStockReceiveRefferedBack()
    {
        $stockReceiveRefferedBack = $this->makeStockReceiveRefferedBack();
        $this->json('DELETE', '/api/v1/stockReceiveRefferedBacks/'.$stockReceiveRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/stockReceiveRefferedBacks/'.$stockReceiveRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}

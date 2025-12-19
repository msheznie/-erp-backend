<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockReceiveDetailsRefferedBackApiTest extends TestCase
{
    use MakeStockReceiveDetailsRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStockReceiveDetailsRefferedBack()
    {
        $stockReceiveDetailsRefferedBack = $this->fakeStockReceiveDetailsRefferedBackData();
        $this->json('POST', '/api/v1/stockReceiveDetailsRefferedBacks', $stockReceiveDetailsRefferedBack);

        $this->assertApiResponse($stockReceiveDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function testReadStockReceiveDetailsRefferedBack()
    {
        $stockReceiveDetailsRefferedBack = $this->makeStockReceiveDetailsRefferedBack();
        $this->json('GET', '/api/v1/stockReceiveDetailsRefferedBacks/'.$stockReceiveDetailsRefferedBack->id);

        $this->assertApiResponse($stockReceiveDetailsRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStockReceiveDetailsRefferedBack()
    {
        $stockReceiveDetailsRefferedBack = $this->makeStockReceiveDetailsRefferedBack();
        $editedStockReceiveDetailsRefferedBack = $this->fakeStockReceiveDetailsRefferedBackData();

        $this->json('PUT', '/api/v1/stockReceiveDetailsRefferedBacks/'.$stockReceiveDetailsRefferedBack->id, $editedStockReceiveDetailsRefferedBack);

        $this->assertApiResponse($editedStockReceiveDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteStockReceiveDetailsRefferedBack()
    {
        $stockReceiveDetailsRefferedBack = $this->makeStockReceiveDetailsRefferedBack();
        $this->json('DELETE', '/api/v1/stockReceiveDetailsRefferedBacks/'.$stockReceiveDetailsRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/stockReceiveDetailsRefferedBacks/'.$stockReceiveDetailsRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}

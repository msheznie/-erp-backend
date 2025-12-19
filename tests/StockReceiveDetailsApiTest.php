<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class StockReceiveDetailsApiTest extends TestCase
{
    use MakeStockReceiveDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateStockReceiveDetails()
    {
        $stockReceiveDetails = $this->fakeStockReceiveDetailsData();
        $this->json('POST', '/api/v1/stockReceiveDetails', $stockReceiveDetails);

        $this->assertApiResponse($stockReceiveDetails);
    }

    /**
     * @test
     */
    public function testReadStockReceiveDetails()
    {
        $stockReceiveDetails = $this->makeStockReceiveDetails();
        $this->json('GET', '/api/v1/stockReceiveDetails/'.$stockReceiveDetails->id);

        $this->assertApiResponse($stockReceiveDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateStockReceiveDetails()
    {
        $stockReceiveDetails = $this->makeStockReceiveDetails();
        $editedStockReceiveDetails = $this->fakeStockReceiveDetailsData();

        $this->json('PUT', '/api/v1/stockReceiveDetails/'.$stockReceiveDetails->id, $editedStockReceiveDetails);

        $this->assertApiResponse($editedStockReceiveDetails);
    }

    /**
     * @test
     */
    public function testDeleteStockReceiveDetails()
    {
        $stockReceiveDetails = $this->makeStockReceiveDetails();
        $this->json('DELETE', '/api/v1/stockReceiveDetails/'.$stockReceiveDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/stockReceiveDetails/'.$stockReceiveDetails->id);

        $this->assertResponseStatus(404);
    }
}

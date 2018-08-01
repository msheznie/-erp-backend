<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PrDetailsReferedHistoryApiTest extends TestCase
{
    use MakePrDetailsReferedHistoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePrDetailsReferedHistory()
    {
        $prDetailsReferedHistory = $this->fakePrDetailsReferedHistoryData();
        $this->json('POST', '/api/v1/prDetailsReferedHistories', $prDetailsReferedHistory);

        $this->assertApiResponse($prDetailsReferedHistory);
    }

    /**
     * @test
     */
    public function testReadPrDetailsReferedHistory()
    {
        $prDetailsReferedHistory = $this->makePrDetailsReferedHistory();
        $this->json('GET', '/api/v1/prDetailsReferedHistories/'.$prDetailsReferedHistory->id);

        $this->assertApiResponse($prDetailsReferedHistory->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePrDetailsReferedHistory()
    {
        $prDetailsReferedHistory = $this->makePrDetailsReferedHistory();
        $editedPrDetailsReferedHistory = $this->fakePrDetailsReferedHistoryData();

        $this->json('PUT', '/api/v1/prDetailsReferedHistories/'.$prDetailsReferedHistory->id, $editedPrDetailsReferedHistory);

        $this->assertApiResponse($editedPrDetailsReferedHistory);
    }

    /**
     * @test
     */
    public function testDeletePrDetailsReferedHistory()
    {
        $prDetailsReferedHistory = $this->makePrDetailsReferedHistory();
        $this->json('DELETE', '/api/v1/prDetailsReferedHistories/'.$prDetailsReferedHistory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/prDetailsReferedHistories/'.$prDetailsReferedHistory->id);

        $this->assertResponseStatus(404);
    }
}

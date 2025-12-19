<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DepreciationPeriodsReferredHistoryApiTest extends TestCase
{
    use MakeDepreciationPeriodsReferredHistoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDepreciationPeriodsReferredHistory()
    {
        $depreciationPeriodsReferredHistory = $this->fakeDepreciationPeriodsReferredHistoryData();
        $this->json('POST', '/api/v1/depreciationPeriodsReferredHistories', $depreciationPeriodsReferredHistory);

        $this->assertApiResponse($depreciationPeriodsReferredHistory);
    }

    /**
     * @test
     */
    public function testReadDepreciationPeriodsReferredHistory()
    {
        $depreciationPeriodsReferredHistory = $this->makeDepreciationPeriodsReferredHistory();
        $this->json('GET', '/api/v1/depreciationPeriodsReferredHistories/'.$depreciationPeriodsReferredHistory->id);

        $this->assertApiResponse($depreciationPeriodsReferredHistory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDepreciationPeriodsReferredHistory()
    {
        $depreciationPeriodsReferredHistory = $this->makeDepreciationPeriodsReferredHistory();
        $editedDepreciationPeriodsReferredHistory = $this->fakeDepreciationPeriodsReferredHistoryData();

        $this->json('PUT', '/api/v1/depreciationPeriodsReferredHistories/'.$depreciationPeriodsReferredHistory->id, $editedDepreciationPeriodsReferredHistory);

        $this->assertApiResponse($editedDepreciationPeriodsReferredHistory);
    }

    /**
     * @test
     */
    public function testDeleteDepreciationPeriodsReferredHistory()
    {
        $depreciationPeriodsReferredHistory = $this->makeDepreciationPeriodsReferredHistory();
        $this->json('DELETE', '/api/v1/depreciationPeriodsReferredHistories/'.$depreciationPeriodsReferredHistory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/depreciationPeriodsReferredHistories/'.$depreciationPeriodsReferredHistory->id);

        $this->assertResponseStatus(404);
    }
}

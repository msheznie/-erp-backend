<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DepreciationMasterReferredHistoryApiTest extends TestCase
{
    use MakeDepreciationMasterReferredHistoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDepreciationMasterReferredHistory()
    {
        $depreciationMasterReferredHistory = $this->fakeDepreciationMasterReferredHistoryData();
        $this->json('POST', '/api/v1/depreciationMasterReferredHistories', $depreciationMasterReferredHistory);

        $this->assertApiResponse($depreciationMasterReferredHistory);
    }

    /**
     * @test
     */
    public function testReadDepreciationMasterReferredHistory()
    {
        $depreciationMasterReferredHistory = $this->makeDepreciationMasterReferredHistory();
        $this->json('GET', '/api/v1/depreciationMasterReferredHistories/'.$depreciationMasterReferredHistory->id);

        $this->assertApiResponse($depreciationMasterReferredHistory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDepreciationMasterReferredHistory()
    {
        $depreciationMasterReferredHistory = $this->makeDepreciationMasterReferredHistory();
        $editedDepreciationMasterReferredHistory = $this->fakeDepreciationMasterReferredHistoryData();

        $this->json('PUT', '/api/v1/depreciationMasterReferredHistories/'.$depreciationMasterReferredHistory->id, $editedDepreciationMasterReferredHistory);

        $this->assertApiResponse($editedDepreciationMasterReferredHistory);
    }

    /**
     * @test
     */
    public function testDeleteDepreciationMasterReferredHistory()
    {
        $depreciationMasterReferredHistory = $this->makeDepreciationMasterReferredHistory();
        $this->json('DELETE', '/api/v1/depreciationMasterReferredHistories/'.$depreciationMasterReferredHistory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/depreciationMasterReferredHistories/'.$depreciationMasterReferredHistory->id);

        $this->assertResponseStatus(404);
    }
}

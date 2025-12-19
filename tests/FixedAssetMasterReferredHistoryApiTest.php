<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetMasterReferredHistoryApiTest extends TestCase
{
    use MakeFixedAssetMasterReferredHistoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFixedAssetMasterReferredHistory()
    {
        $fixedAssetMasterReferredHistory = $this->fakeFixedAssetMasterReferredHistoryData();
        $this->json('POST', '/api/v1/fixedAssetMasterReferredHistories', $fixedAssetMasterReferredHistory);

        $this->assertApiResponse($fixedAssetMasterReferredHistory);
    }

    /**
     * @test
     */
    public function testReadFixedAssetMasterReferredHistory()
    {
        $fixedAssetMasterReferredHistory = $this->makeFixedAssetMasterReferredHistory();
        $this->json('GET', '/api/v1/fixedAssetMasterReferredHistories/'.$fixedAssetMasterReferredHistory->id);

        $this->assertApiResponse($fixedAssetMasterReferredHistory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFixedAssetMasterReferredHistory()
    {
        $fixedAssetMasterReferredHistory = $this->makeFixedAssetMasterReferredHistory();
        $editedFixedAssetMasterReferredHistory = $this->fakeFixedAssetMasterReferredHistoryData();

        $this->json('PUT', '/api/v1/fixedAssetMasterReferredHistories/'.$fixedAssetMasterReferredHistory->id, $editedFixedAssetMasterReferredHistory);

        $this->assertApiResponse($editedFixedAssetMasterReferredHistory);
    }

    /**
     * @test
     */
    public function testDeleteFixedAssetMasterReferredHistory()
    {
        $fixedAssetMasterReferredHistory = $this->makeFixedAssetMasterReferredHistory();
        $this->json('DELETE', '/api/v1/fixedAssetMasterReferredHistories/'.$fixedAssetMasterReferredHistory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/fixedAssetMasterReferredHistories/'.$fixedAssetMasterReferredHistory->id);

        $this->assertResponseStatus(404);
    }
}

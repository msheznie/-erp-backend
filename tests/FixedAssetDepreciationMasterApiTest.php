<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetDepreciationMasterApiTest extends TestCase
{
    use MakeFixedAssetDepreciationMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFixedAssetDepreciationMaster()
    {
        $fixedAssetDepreciationMaster = $this->fakeFixedAssetDepreciationMasterData();
        $this->json('POST', '/api/v1/fixedAssetDepreciationMasters', $fixedAssetDepreciationMaster);

        $this->assertApiResponse($fixedAssetDepreciationMaster);
    }

    /**
     * @test
     */
    public function testReadFixedAssetDepreciationMaster()
    {
        $fixedAssetDepreciationMaster = $this->makeFixedAssetDepreciationMaster();
        $this->json('GET', '/api/v1/fixedAssetDepreciationMasters/'.$fixedAssetDepreciationMaster->id);

        $this->assertApiResponse($fixedAssetDepreciationMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFixedAssetDepreciationMaster()
    {
        $fixedAssetDepreciationMaster = $this->makeFixedAssetDepreciationMaster();
        $editedFixedAssetDepreciationMaster = $this->fakeFixedAssetDepreciationMasterData();

        $this->json('PUT', '/api/v1/fixedAssetDepreciationMasters/'.$fixedAssetDepreciationMaster->id, $editedFixedAssetDepreciationMaster);

        $this->assertApiResponse($editedFixedAssetDepreciationMaster);
    }

    /**
     * @test
     */
    public function testDeleteFixedAssetDepreciationMaster()
    {
        $fixedAssetDepreciationMaster = $this->makeFixedAssetDepreciationMaster();
        $this->json('DELETE', '/api/v1/fixedAssetDepreciationMasters/'.$fixedAssetDepreciationMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/fixedAssetDepreciationMasters/'.$fixedAssetDepreciationMaster->id);

        $this->assertResponseStatus(404);
    }
}

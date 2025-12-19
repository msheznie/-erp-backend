<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetMasterApiTest extends TestCase
{
    use MakeFixedAssetMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFixedAssetMaster()
    {
        $fixedAssetMaster = $this->fakeFixedAssetMasterData();
        $this->json('POST', '/api/v1/fixedAssetMasters', $fixedAssetMaster);

        $this->assertApiResponse($fixedAssetMaster);
    }

    /**
     * @test
     */
    public function testReadFixedAssetMaster()
    {
        $fixedAssetMaster = $this->makeFixedAssetMaster();
        $this->json('GET', '/api/v1/fixedAssetMasters/'.$fixedAssetMaster->id);

        $this->assertApiResponse($fixedAssetMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFixedAssetMaster()
    {
        $fixedAssetMaster = $this->makeFixedAssetMaster();
        $editedFixedAssetMaster = $this->fakeFixedAssetMasterData();

        $this->json('PUT', '/api/v1/fixedAssetMasters/'.$fixedAssetMaster->id, $editedFixedAssetMaster);

        $this->assertApiResponse($editedFixedAssetMaster);
    }

    /**
     * @test
     */
    public function testDeleteFixedAssetMaster()
    {
        $fixedAssetMaster = $this->makeFixedAssetMaster();
        $this->json('DELETE', '/api/v1/fixedAssetMasters/'.$fixedAssetMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/fixedAssetMasters/'.$fixedAssetMaster->id);

        $this->assertResponseStatus(404);
    }
}

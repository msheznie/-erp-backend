<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetDepreciationPeriodApiTest extends TestCase
{
    use MakeAssetDepreciationPeriodTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAssetDepreciationPeriod()
    {
        $assetDepreciationPeriod = $this->fakeAssetDepreciationPeriodData();
        $this->json('POST', '/api/v1/assetDepreciationPeriods', $assetDepreciationPeriod);

        $this->assertApiResponse($assetDepreciationPeriod);
    }

    /**
     * @test
     */
    public function testReadAssetDepreciationPeriod()
    {
        $assetDepreciationPeriod = $this->makeAssetDepreciationPeriod();
        $this->json('GET', '/api/v1/assetDepreciationPeriods/'.$assetDepreciationPeriod->id);

        $this->assertApiResponse($assetDepreciationPeriod->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAssetDepreciationPeriod()
    {
        $assetDepreciationPeriod = $this->makeAssetDepreciationPeriod();
        $editedAssetDepreciationPeriod = $this->fakeAssetDepreciationPeriodData();

        $this->json('PUT', '/api/v1/assetDepreciationPeriods/'.$assetDepreciationPeriod->id, $editedAssetDepreciationPeriod);

        $this->assertApiResponse($editedAssetDepreciationPeriod);
    }

    /**
     * @test
     */
    public function testDeleteAssetDepreciationPeriod()
    {
        $assetDepreciationPeriod = $this->makeAssetDepreciationPeriod();
        $this->json('DELETE', '/api/v1/assetDepreciationPeriods/'.$assetDepreciationPeriod->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/assetDepreciationPeriods/'.$assetDepreciationPeriod->id);

        $this->assertResponseStatus(404);
    }
}

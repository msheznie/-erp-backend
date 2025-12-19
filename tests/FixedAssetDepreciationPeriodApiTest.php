<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetDepreciationPeriodApiTest extends TestCase
{
    use MakeFixedAssetDepreciationPeriodTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFixedAssetDepreciationPeriod()
    {
        $fixedAssetDepreciationPeriod = $this->fakeFixedAssetDepreciationPeriodData();
        $this->json('POST', '/api/v1/fixedAssetDepreciationPeriods', $fixedAssetDepreciationPeriod);

        $this->assertApiResponse($fixedAssetDepreciationPeriod);
    }

    /**
     * @test
     */
    public function testReadFixedAssetDepreciationPeriod()
    {
        $fixedAssetDepreciationPeriod = $this->makeFixedAssetDepreciationPeriod();
        $this->json('GET', '/api/v1/fixedAssetDepreciationPeriods/'.$fixedAssetDepreciationPeriod->id);

        $this->assertApiResponse($fixedAssetDepreciationPeriod->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFixedAssetDepreciationPeriod()
    {
        $fixedAssetDepreciationPeriod = $this->makeFixedAssetDepreciationPeriod();
        $editedFixedAssetDepreciationPeriod = $this->fakeFixedAssetDepreciationPeriodData();

        $this->json('PUT', '/api/v1/fixedAssetDepreciationPeriods/'.$fixedAssetDepreciationPeriod->id, $editedFixedAssetDepreciationPeriod);

        $this->assertApiResponse($editedFixedAssetDepreciationPeriod);
    }

    /**
     * @test
     */
    public function testDeleteFixedAssetDepreciationPeriod()
    {
        $fixedAssetDepreciationPeriod = $this->makeFixedAssetDepreciationPeriod();
        $this->json('DELETE', '/api/v1/fixedAssetDepreciationPeriods/'.$fixedAssetDepreciationPeriod->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/fixedAssetDepreciationPeriods/'.$fixedAssetDepreciationPeriod->id);

        $this->assertResponseStatus(404);
    }
}

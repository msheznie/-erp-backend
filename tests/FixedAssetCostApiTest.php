<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetCostApiTest extends TestCase
{
    use MakeFixedAssetCostTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFixedAssetCost()
    {
        $fixedAssetCost = $this->fakeFixedAssetCostData();
        $this->json('POST', '/api/v1/fixedAssetCosts', $fixedAssetCost);

        $this->assertApiResponse($fixedAssetCost);
    }

    /**
     * @test
     */
    public function testReadFixedAssetCost()
    {
        $fixedAssetCost = $this->makeFixedAssetCost();
        $this->json('GET', '/api/v1/fixedAssetCosts/'.$fixedAssetCost->id);

        $this->assertApiResponse($fixedAssetCost->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFixedAssetCost()
    {
        $fixedAssetCost = $this->makeFixedAssetCost();
        $editedFixedAssetCost = $this->fakeFixedAssetCostData();

        $this->json('PUT', '/api/v1/fixedAssetCosts/'.$fixedAssetCost->id, $editedFixedAssetCost);

        $this->assertApiResponse($editedFixedAssetCost);
    }

    /**
     * @test
     */
    public function testDeleteFixedAssetCost()
    {
        $fixedAssetCost = $this->makeFixedAssetCost();
        $this->json('DELETE', '/api/v1/fixedAssetCosts/'.$fixedAssetCost->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/fixedAssetCosts/'.$fixedAssetCost->id);

        $this->assertResponseStatus(404);
    }
}

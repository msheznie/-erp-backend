<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetCategorySubApiTest extends TestCase
{
    use MakeFixedAssetCategorySubTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFixedAssetCategorySub()
    {
        $fixedAssetCategorySub = $this->fakeFixedAssetCategorySubData();
        $this->json('POST', '/api/v1/fixedAssetCategorySubs', $fixedAssetCategorySub);

        $this->assertApiResponse($fixedAssetCategorySub);
    }

    /**
     * @test
     */
    public function testReadFixedAssetCategorySub()
    {
        $fixedAssetCategorySub = $this->makeFixedAssetCategorySub();
        $this->json('GET', '/api/v1/fixedAssetCategorySubs/'.$fixedAssetCategorySub->id);

        $this->assertApiResponse($fixedAssetCategorySub->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFixedAssetCategorySub()
    {
        $fixedAssetCategorySub = $this->makeFixedAssetCategorySub();
        $editedFixedAssetCategorySub = $this->fakeFixedAssetCategorySubData();

        $this->json('PUT', '/api/v1/fixedAssetCategorySubs/'.$fixedAssetCategorySub->id, $editedFixedAssetCategorySub);

        $this->assertApiResponse($editedFixedAssetCategorySub);
    }

    /**
     * @test
     */
    public function testDeleteFixedAssetCategorySub()
    {
        $fixedAssetCategorySub = $this->makeFixedAssetCategorySub();
        $this->json('DELETE', '/api/v1/fixedAssetCategorySubs/'.$fixedAssetCategorySub->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/fixedAssetCategorySubs/'.$fixedAssetCategorySub->id);

        $this->assertResponseStatus(404);
    }
}

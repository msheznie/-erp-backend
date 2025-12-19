<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetCategoryApiTest extends TestCase
{
    use MakeFixedAssetCategoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFixedAssetCategory()
    {
        $fixedAssetCategory = $this->fakeFixedAssetCategoryData();
        $this->json('POST', '/api/v1/fixedAssetCategories', $fixedAssetCategory);

        $this->assertApiResponse($fixedAssetCategory);
    }

    /**
     * @test
     */
    public function testReadFixedAssetCategory()
    {
        $fixedAssetCategory = $this->makeFixedAssetCategory();
        $this->json('GET', '/api/v1/fixedAssetCategories/'.$fixedAssetCategory->id);

        $this->assertApiResponse($fixedAssetCategory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFixedAssetCategory()
    {
        $fixedAssetCategory = $this->makeFixedAssetCategory();
        $editedFixedAssetCategory = $this->fakeFixedAssetCategoryData();

        $this->json('PUT', '/api/v1/fixedAssetCategories/'.$fixedAssetCategory->id, $editedFixedAssetCategory);

        $this->assertApiResponse($editedFixedAssetCategory);
    }

    /**
     * @test
     */
    public function testDeleteFixedAssetCategory()
    {
        $fixedAssetCategory = $this->makeFixedAssetCategory();
        $this->json('DELETE', '/api/v1/fixedAssetCategories/'.$fixedAssetCategory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/fixedAssetCategories/'.$fixedAssetCategory->id);

        $this->assertResponseStatus(404);
    }
}

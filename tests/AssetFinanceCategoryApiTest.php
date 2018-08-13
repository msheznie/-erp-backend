<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetFinanceCategoryApiTest extends TestCase
{
    use MakeAssetFinanceCategoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAssetFinanceCategory()
    {
        $assetFinanceCategory = $this->fakeAssetFinanceCategoryData();
        $this->json('POST', '/api/v1/assetFinanceCategories', $assetFinanceCategory);

        $this->assertApiResponse($assetFinanceCategory);
    }

    /**
     * @test
     */
    public function testReadAssetFinanceCategory()
    {
        $assetFinanceCategory = $this->makeAssetFinanceCategory();
        $this->json('GET', '/api/v1/assetFinanceCategories/'.$assetFinanceCategory->id);

        $this->assertApiResponse($assetFinanceCategory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAssetFinanceCategory()
    {
        $assetFinanceCategory = $this->makeAssetFinanceCategory();
        $editedAssetFinanceCategory = $this->fakeAssetFinanceCategoryData();

        $this->json('PUT', '/api/v1/assetFinanceCategories/'.$assetFinanceCategory->id, $editedAssetFinanceCategory);

        $this->assertApiResponse($editedAssetFinanceCategory);
    }

    /**
     * @test
     */
    public function testDeleteAssetFinanceCategory()
    {
        $assetFinanceCategory = $this->makeAssetFinanceCategory();
        $this->json('DELETE', '/api/v1/assetFinanceCategories/'.$assetFinanceCategory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/assetFinanceCategories/'.$assetFinanceCategory->id);

        $this->assertResponseStatus(404);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetTypeApiTest extends TestCase
{
    use MakeAssetTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAssetType()
    {
        $assetType = $this->fakeAssetTypeData();
        $this->json('POST', '/api/v1/assetTypes', $assetType);

        $this->assertApiResponse($assetType);
    }

    /**
     * @test
     */
    public function testReadAssetType()
    {
        $assetType = $this->makeAssetType();
        $this->json('GET', '/api/v1/assetTypes/'.$assetType->id);

        $this->assertApiResponse($assetType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAssetType()
    {
        $assetType = $this->makeAssetType();
        $editedAssetType = $this->fakeAssetTypeData();

        $this->json('PUT', '/api/v1/assetTypes/'.$assetType->id, $editedAssetType);

        $this->assertApiResponse($editedAssetType);
    }

    /**
     * @test
     */
    public function testDeleteAssetType()
    {
        $assetType = $this->makeAssetType();
        $this->json('DELETE', '/api/v1/assetTypes/'.$assetType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/assetTypes/'.$assetType->id);

        $this->assertResponseStatus(404);
    }
}

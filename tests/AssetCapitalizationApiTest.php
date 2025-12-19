<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetCapitalizationApiTest extends TestCase
{
    use MakeAssetCapitalizationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAssetCapitalization()
    {
        $assetCapitalization = $this->fakeAssetCapitalizationData();
        $this->json('POST', '/api/v1/assetCapitalizations', $assetCapitalization);

        $this->assertApiResponse($assetCapitalization);
    }

    /**
     * @test
     */
    public function testReadAssetCapitalization()
    {
        $assetCapitalization = $this->makeAssetCapitalization();
        $this->json('GET', '/api/v1/assetCapitalizations/'.$assetCapitalization->id);

        $this->assertApiResponse($assetCapitalization->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAssetCapitalization()
    {
        $assetCapitalization = $this->makeAssetCapitalization();
        $editedAssetCapitalization = $this->fakeAssetCapitalizationData();

        $this->json('PUT', '/api/v1/assetCapitalizations/'.$assetCapitalization->id, $editedAssetCapitalization);

        $this->assertApiResponse($editedAssetCapitalization);
    }

    /**
     * @test
     */
    public function testDeleteAssetCapitalization()
    {
        $assetCapitalization = $this->makeAssetCapitalization();
        $this->json('DELETE', '/api/v1/assetCapitalizations/'.$assetCapitalization->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/assetCapitalizations/'.$assetCapitalization->id);

        $this->assertResponseStatus(404);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetCapitalizationReferredApiTest extends TestCase
{
    use MakeAssetCapitalizationReferredTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAssetCapitalizationReferred()
    {
        $assetCapitalizationReferred = $this->fakeAssetCapitalizationReferredData();
        $this->json('POST', '/api/v1/assetCapitalizationReferreds', $assetCapitalizationReferred);

        $this->assertApiResponse($assetCapitalizationReferred);
    }

    /**
     * @test
     */
    public function testReadAssetCapitalizationReferred()
    {
        $assetCapitalizationReferred = $this->makeAssetCapitalizationReferred();
        $this->json('GET', '/api/v1/assetCapitalizationReferreds/'.$assetCapitalizationReferred->id);

        $this->assertApiResponse($assetCapitalizationReferred->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAssetCapitalizationReferred()
    {
        $assetCapitalizationReferred = $this->makeAssetCapitalizationReferred();
        $editedAssetCapitalizationReferred = $this->fakeAssetCapitalizationReferredData();

        $this->json('PUT', '/api/v1/assetCapitalizationReferreds/'.$assetCapitalizationReferred->id, $editedAssetCapitalizationReferred);

        $this->assertApiResponse($editedAssetCapitalizationReferred);
    }

    /**
     * @test
     */
    public function testDeleteAssetCapitalizationReferred()
    {
        $assetCapitalizationReferred = $this->makeAssetCapitalizationReferred();
        $this->json('DELETE', '/api/v1/assetCapitalizationReferreds/'.$assetCapitalizationReferred->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/assetCapitalizationReferreds/'.$assetCapitalizationReferred->id);

        $this->assertResponseStatus(404);
    }
}

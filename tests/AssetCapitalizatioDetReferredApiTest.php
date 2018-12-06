<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetCapitalizatioDetReferredApiTest extends TestCase
{
    use MakeAssetCapitalizatioDetReferredTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAssetCapitalizatioDetReferred()
    {
        $assetCapitalizatioDetReferred = $this->fakeAssetCapitalizatioDetReferredData();
        $this->json('POST', '/api/v1/assetCapitalizatioDetReferreds', $assetCapitalizatioDetReferred);

        $this->assertApiResponse($assetCapitalizatioDetReferred);
    }

    /**
     * @test
     */
    public function testReadAssetCapitalizatioDetReferred()
    {
        $assetCapitalizatioDetReferred = $this->makeAssetCapitalizatioDetReferred();
        $this->json('GET', '/api/v1/assetCapitalizatioDetReferreds/'.$assetCapitalizatioDetReferred->id);

        $this->assertApiResponse($assetCapitalizatioDetReferred->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAssetCapitalizatioDetReferred()
    {
        $assetCapitalizatioDetReferred = $this->makeAssetCapitalizatioDetReferred();
        $editedAssetCapitalizatioDetReferred = $this->fakeAssetCapitalizatioDetReferredData();

        $this->json('PUT', '/api/v1/assetCapitalizatioDetReferreds/'.$assetCapitalizatioDetReferred->id, $editedAssetCapitalizatioDetReferred);

        $this->assertApiResponse($editedAssetCapitalizatioDetReferred);
    }

    /**
     * @test
     */
    public function testDeleteAssetCapitalizatioDetReferred()
    {
        $assetCapitalizatioDetReferred = $this->makeAssetCapitalizatioDetReferred();
        $this->json('DELETE', '/api/v1/assetCapitalizatioDetReferreds/'.$assetCapitalizatioDetReferred->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/assetCapitalizatioDetReferreds/'.$assetCapitalizatioDetReferred->id);

        $this->assertResponseStatus(404);
    }
}

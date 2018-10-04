<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetCapitalizationDetailApiTest extends TestCase
{
    use MakeAssetCapitalizationDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAssetCapitalizationDetail()
    {
        $assetCapitalizationDetail = $this->fakeAssetCapitalizationDetailData();
        $this->json('POST', '/api/v1/assetCapitalizationDetails', $assetCapitalizationDetail);

        $this->assertApiResponse($assetCapitalizationDetail);
    }

    /**
     * @test
     */
    public function testReadAssetCapitalizationDetail()
    {
        $assetCapitalizationDetail = $this->makeAssetCapitalizationDetail();
        $this->json('GET', '/api/v1/assetCapitalizationDetails/'.$assetCapitalizationDetail->id);

        $this->assertApiResponse($assetCapitalizationDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAssetCapitalizationDetail()
    {
        $assetCapitalizationDetail = $this->makeAssetCapitalizationDetail();
        $editedAssetCapitalizationDetail = $this->fakeAssetCapitalizationDetailData();

        $this->json('PUT', '/api/v1/assetCapitalizationDetails/'.$assetCapitalizationDetail->id, $editedAssetCapitalizationDetail);

        $this->assertApiResponse($editedAssetCapitalizationDetail);
    }

    /**
     * @test
     */
    public function testDeleteAssetCapitalizationDetail()
    {
        $assetCapitalizationDetail = $this->makeAssetCapitalizationDetail();
        $this->json('DELETE', '/api/v1/assetCapitalizationDetails/'.$assetCapitalizationDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/assetCapitalizationDetails/'.$assetCapitalizationDetail->id);

        $this->assertResponseStatus(404);
    }
}

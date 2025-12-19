<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetDisposalDetailReferredApiTest extends TestCase
{
    use MakeAssetDisposalDetailReferredTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAssetDisposalDetailReferred()
    {
        $assetDisposalDetailReferred = $this->fakeAssetDisposalDetailReferredData();
        $this->json('POST', '/api/v1/assetDisposalDetailReferreds', $assetDisposalDetailReferred);

        $this->assertApiResponse($assetDisposalDetailReferred);
    }

    /**
     * @test
     */
    public function testReadAssetDisposalDetailReferred()
    {
        $assetDisposalDetailReferred = $this->makeAssetDisposalDetailReferred();
        $this->json('GET', '/api/v1/assetDisposalDetailReferreds/'.$assetDisposalDetailReferred->id);

        $this->assertApiResponse($assetDisposalDetailReferred->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAssetDisposalDetailReferred()
    {
        $assetDisposalDetailReferred = $this->makeAssetDisposalDetailReferred();
        $editedAssetDisposalDetailReferred = $this->fakeAssetDisposalDetailReferredData();

        $this->json('PUT', '/api/v1/assetDisposalDetailReferreds/'.$assetDisposalDetailReferred->id, $editedAssetDisposalDetailReferred);

        $this->assertApiResponse($editedAssetDisposalDetailReferred);
    }

    /**
     * @test
     */
    public function testDeleteAssetDisposalDetailReferred()
    {
        $assetDisposalDetailReferred = $this->makeAssetDisposalDetailReferred();
        $this->json('DELETE', '/api/v1/assetDisposalDetailReferreds/'.$assetDisposalDetailReferred->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/assetDisposalDetailReferreds/'.$assetDisposalDetailReferred->id);

        $this->assertResponseStatus(404);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetDisposalReferredApiTest extends TestCase
{
    use MakeAssetDisposalReferredTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAssetDisposalReferred()
    {
        $assetDisposalReferred = $this->fakeAssetDisposalReferredData();
        $this->json('POST', '/api/v1/assetDisposalReferreds', $assetDisposalReferred);

        $this->assertApiResponse($assetDisposalReferred);
    }

    /**
     * @test
     */
    public function testReadAssetDisposalReferred()
    {
        $assetDisposalReferred = $this->makeAssetDisposalReferred();
        $this->json('GET', '/api/v1/assetDisposalReferreds/'.$assetDisposalReferred->id);

        $this->assertApiResponse($assetDisposalReferred->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAssetDisposalReferred()
    {
        $assetDisposalReferred = $this->makeAssetDisposalReferred();
        $editedAssetDisposalReferred = $this->fakeAssetDisposalReferredData();

        $this->json('PUT', '/api/v1/assetDisposalReferreds/'.$assetDisposalReferred->id, $editedAssetDisposalReferred);

        $this->assertApiResponse($editedAssetDisposalReferred);
    }

    /**
     * @test
     */
    public function testDeleteAssetDisposalReferred()
    {
        $assetDisposalReferred = $this->makeAssetDisposalReferred();
        $this->json('DELETE', '/api/v1/assetDisposalReferreds/'.$assetDisposalReferred->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/assetDisposalReferreds/'.$assetDisposalReferred->id);

        $this->assertResponseStatus(404);
    }
}

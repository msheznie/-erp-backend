<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetDisposalDetailApiTest extends TestCase
{
    use MakeAssetDisposalDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAssetDisposalDetail()
    {
        $assetDisposalDetail = $this->fakeAssetDisposalDetailData();
        $this->json('POST', '/api/v1/assetDisposalDetails', $assetDisposalDetail);

        $this->assertApiResponse($assetDisposalDetail);
    }

    /**
     * @test
     */
    public function testReadAssetDisposalDetail()
    {
        $assetDisposalDetail = $this->makeAssetDisposalDetail();
        $this->json('GET', '/api/v1/assetDisposalDetails/'.$assetDisposalDetail->id);

        $this->assertApiResponse($assetDisposalDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAssetDisposalDetail()
    {
        $assetDisposalDetail = $this->makeAssetDisposalDetail();
        $editedAssetDisposalDetail = $this->fakeAssetDisposalDetailData();

        $this->json('PUT', '/api/v1/assetDisposalDetails/'.$assetDisposalDetail->id, $editedAssetDisposalDetail);

        $this->assertApiResponse($editedAssetDisposalDetail);
    }

    /**
     * @test
     */
    public function testDeleteAssetDisposalDetail()
    {
        $assetDisposalDetail = $this->makeAssetDisposalDetail();
        $this->json('DELETE', '/api/v1/assetDisposalDetails/'.$assetDisposalDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/assetDisposalDetails/'.$assetDisposalDetail->id);

        $this->assertResponseStatus(404);
    }
}

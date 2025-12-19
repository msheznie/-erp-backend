<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetDisposalMasterApiTest extends TestCase
{
    use MakeAssetDisposalMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAssetDisposalMaster()
    {
        $assetDisposalMaster = $this->fakeAssetDisposalMasterData();
        $this->json('POST', '/api/v1/assetDisposalMasters', $assetDisposalMaster);

        $this->assertApiResponse($assetDisposalMaster);
    }

    /**
     * @test
     */
    public function testReadAssetDisposalMaster()
    {
        $assetDisposalMaster = $this->makeAssetDisposalMaster();
        $this->json('GET', '/api/v1/assetDisposalMasters/'.$assetDisposalMaster->id);

        $this->assertApiResponse($assetDisposalMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAssetDisposalMaster()
    {
        $assetDisposalMaster = $this->makeAssetDisposalMaster();
        $editedAssetDisposalMaster = $this->fakeAssetDisposalMasterData();

        $this->json('PUT', '/api/v1/assetDisposalMasters/'.$assetDisposalMaster->id, $editedAssetDisposalMaster);

        $this->assertApiResponse($editedAssetDisposalMaster);
    }

    /**
     * @test
     */
    public function testDeleteAssetDisposalMaster()
    {
        $assetDisposalMaster = $this->makeAssetDisposalMaster();
        $this->json('DELETE', '/api/v1/assetDisposalMasters/'.$assetDisposalMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/assetDisposalMasters/'.$assetDisposalMaster->id);

        $this->assertResponseStatus(404);
    }
}

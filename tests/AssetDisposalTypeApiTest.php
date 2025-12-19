<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetDisposalTypeApiTest extends TestCase
{
    use MakeAssetDisposalTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAssetDisposalType()
    {
        $assetDisposalType = $this->fakeAssetDisposalTypeData();
        $this->json('POST', '/api/v1/assetDisposalTypes', $assetDisposalType);

        $this->assertApiResponse($assetDisposalType);
    }

    /**
     * @test
     */
    public function testReadAssetDisposalType()
    {
        $assetDisposalType = $this->makeAssetDisposalType();
        $this->json('GET', '/api/v1/assetDisposalTypes/'.$assetDisposalType->id);

        $this->assertApiResponse($assetDisposalType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAssetDisposalType()
    {
        $assetDisposalType = $this->makeAssetDisposalType();
        $editedAssetDisposalType = $this->fakeAssetDisposalTypeData();

        $this->json('PUT', '/api/v1/assetDisposalTypes/'.$assetDisposalType->id, $editedAssetDisposalType);

        $this->assertApiResponse($editedAssetDisposalType);
    }

    /**
     * @test
     */
    public function testDeleteAssetDisposalType()
    {
        $assetDisposalType = $this->makeAssetDisposalType();
        $this->json('DELETE', '/api/v1/assetDisposalTypes/'.$assetDisposalType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/assetDisposalTypes/'.$assetDisposalType->id);

        $this->assertResponseStatus(404);
    }
}

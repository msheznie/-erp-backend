<?php

use App\Models\AssetDisposalDetailReferred;
use App\Repositories\AssetDisposalDetailReferredRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetDisposalDetailReferredRepositoryTest extends TestCase
{
    use MakeAssetDisposalDetailReferredTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetDisposalDetailReferredRepository
     */
    protected $assetDisposalDetailReferredRepo;

    public function setUp()
    {
        parent::setUp();
        $this->assetDisposalDetailReferredRepo = App::make(AssetDisposalDetailReferredRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAssetDisposalDetailReferred()
    {
        $assetDisposalDetailReferred = $this->fakeAssetDisposalDetailReferredData();
        $createdAssetDisposalDetailReferred = $this->assetDisposalDetailReferredRepo->create($assetDisposalDetailReferred);
        $createdAssetDisposalDetailReferred = $createdAssetDisposalDetailReferred->toArray();
        $this->assertArrayHasKey('id', $createdAssetDisposalDetailReferred);
        $this->assertNotNull($createdAssetDisposalDetailReferred['id'], 'Created AssetDisposalDetailReferred must have id specified');
        $this->assertNotNull(AssetDisposalDetailReferred::find($createdAssetDisposalDetailReferred['id']), 'AssetDisposalDetailReferred with given id must be in DB');
        $this->assertModelData($assetDisposalDetailReferred, $createdAssetDisposalDetailReferred);
    }

    /**
     * @test read
     */
    public function testReadAssetDisposalDetailReferred()
    {
        $assetDisposalDetailReferred = $this->makeAssetDisposalDetailReferred();
        $dbAssetDisposalDetailReferred = $this->assetDisposalDetailReferredRepo->find($assetDisposalDetailReferred->id);
        $dbAssetDisposalDetailReferred = $dbAssetDisposalDetailReferred->toArray();
        $this->assertModelData($assetDisposalDetailReferred->toArray(), $dbAssetDisposalDetailReferred);
    }

    /**
     * @test update
     */
    public function testUpdateAssetDisposalDetailReferred()
    {
        $assetDisposalDetailReferred = $this->makeAssetDisposalDetailReferred();
        $fakeAssetDisposalDetailReferred = $this->fakeAssetDisposalDetailReferredData();
        $updatedAssetDisposalDetailReferred = $this->assetDisposalDetailReferredRepo->update($fakeAssetDisposalDetailReferred, $assetDisposalDetailReferred->id);
        $this->assertModelData($fakeAssetDisposalDetailReferred, $updatedAssetDisposalDetailReferred->toArray());
        $dbAssetDisposalDetailReferred = $this->assetDisposalDetailReferredRepo->find($assetDisposalDetailReferred->id);
        $this->assertModelData($fakeAssetDisposalDetailReferred, $dbAssetDisposalDetailReferred->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAssetDisposalDetailReferred()
    {
        $assetDisposalDetailReferred = $this->makeAssetDisposalDetailReferred();
        $resp = $this->assetDisposalDetailReferredRepo->delete($assetDisposalDetailReferred->id);
        $this->assertTrue($resp);
        $this->assertNull(AssetDisposalDetailReferred::find($assetDisposalDetailReferred->id), 'AssetDisposalDetailReferred should not exist in DB');
    }
}

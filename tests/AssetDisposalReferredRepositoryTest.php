<?php

use App\Models\AssetDisposalReferred;
use App\Repositories\AssetDisposalReferredRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetDisposalReferredRepositoryTest extends TestCase
{
    use MakeAssetDisposalReferredTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetDisposalReferredRepository
     */
    protected $assetDisposalReferredRepo;

    public function setUp()
    {
        parent::setUp();
        $this->assetDisposalReferredRepo = App::make(AssetDisposalReferredRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAssetDisposalReferred()
    {
        $assetDisposalReferred = $this->fakeAssetDisposalReferredData();
        $createdAssetDisposalReferred = $this->assetDisposalReferredRepo->create($assetDisposalReferred);
        $createdAssetDisposalReferred = $createdAssetDisposalReferred->toArray();
        $this->assertArrayHasKey('id', $createdAssetDisposalReferred);
        $this->assertNotNull($createdAssetDisposalReferred['id'], 'Created AssetDisposalReferred must have id specified');
        $this->assertNotNull(AssetDisposalReferred::find($createdAssetDisposalReferred['id']), 'AssetDisposalReferred with given id must be in DB');
        $this->assertModelData($assetDisposalReferred, $createdAssetDisposalReferred);
    }

    /**
     * @test read
     */
    public function testReadAssetDisposalReferred()
    {
        $assetDisposalReferred = $this->makeAssetDisposalReferred();
        $dbAssetDisposalReferred = $this->assetDisposalReferredRepo->find($assetDisposalReferred->id);
        $dbAssetDisposalReferred = $dbAssetDisposalReferred->toArray();
        $this->assertModelData($assetDisposalReferred->toArray(), $dbAssetDisposalReferred);
    }

    /**
     * @test update
     */
    public function testUpdateAssetDisposalReferred()
    {
        $assetDisposalReferred = $this->makeAssetDisposalReferred();
        $fakeAssetDisposalReferred = $this->fakeAssetDisposalReferredData();
        $updatedAssetDisposalReferred = $this->assetDisposalReferredRepo->update($fakeAssetDisposalReferred, $assetDisposalReferred->id);
        $this->assertModelData($fakeAssetDisposalReferred, $updatedAssetDisposalReferred->toArray());
        $dbAssetDisposalReferred = $this->assetDisposalReferredRepo->find($assetDisposalReferred->id);
        $this->assertModelData($fakeAssetDisposalReferred, $dbAssetDisposalReferred->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAssetDisposalReferred()
    {
        $assetDisposalReferred = $this->makeAssetDisposalReferred();
        $resp = $this->assetDisposalReferredRepo->delete($assetDisposalReferred->id);
        $this->assertTrue($resp);
        $this->assertNull(AssetDisposalReferred::find($assetDisposalReferred->id), 'AssetDisposalReferred should not exist in DB');
    }
}

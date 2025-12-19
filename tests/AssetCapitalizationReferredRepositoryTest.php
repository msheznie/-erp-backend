<?php

use App\Models\AssetCapitalizationReferred;
use App\Repositories\AssetCapitalizationReferredRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetCapitalizationReferredRepositoryTest extends TestCase
{
    use MakeAssetCapitalizationReferredTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetCapitalizationReferredRepository
     */
    protected $assetCapitalizationReferredRepo;

    public function setUp()
    {
        parent::setUp();
        $this->assetCapitalizationReferredRepo = App::make(AssetCapitalizationReferredRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAssetCapitalizationReferred()
    {
        $assetCapitalizationReferred = $this->fakeAssetCapitalizationReferredData();
        $createdAssetCapitalizationReferred = $this->assetCapitalizationReferredRepo->create($assetCapitalizationReferred);
        $createdAssetCapitalizationReferred = $createdAssetCapitalizationReferred->toArray();
        $this->assertArrayHasKey('id', $createdAssetCapitalizationReferred);
        $this->assertNotNull($createdAssetCapitalizationReferred['id'], 'Created AssetCapitalizationReferred must have id specified');
        $this->assertNotNull(AssetCapitalizationReferred::find($createdAssetCapitalizationReferred['id']), 'AssetCapitalizationReferred with given id must be in DB');
        $this->assertModelData($assetCapitalizationReferred, $createdAssetCapitalizationReferred);
    }

    /**
     * @test read
     */
    public function testReadAssetCapitalizationReferred()
    {
        $assetCapitalizationReferred = $this->makeAssetCapitalizationReferred();
        $dbAssetCapitalizationReferred = $this->assetCapitalizationReferredRepo->find($assetCapitalizationReferred->id);
        $dbAssetCapitalizationReferred = $dbAssetCapitalizationReferred->toArray();
        $this->assertModelData($assetCapitalizationReferred->toArray(), $dbAssetCapitalizationReferred);
    }

    /**
     * @test update
     */
    public function testUpdateAssetCapitalizationReferred()
    {
        $assetCapitalizationReferred = $this->makeAssetCapitalizationReferred();
        $fakeAssetCapitalizationReferred = $this->fakeAssetCapitalizationReferredData();
        $updatedAssetCapitalizationReferred = $this->assetCapitalizationReferredRepo->update($fakeAssetCapitalizationReferred, $assetCapitalizationReferred->id);
        $this->assertModelData($fakeAssetCapitalizationReferred, $updatedAssetCapitalizationReferred->toArray());
        $dbAssetCapitalizationReferred = $this->assetCapitalizationReferredRepo->find($assetCapitalizationReferred->id);
        $this->assertModelData($fakeAssetCapitalizationReferred, $dbAssetCapitalizationReferred->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAssetCapitalizationReferred()
    {
        $assetCapitalizationReferred = $this->makeAssetCapitalizationReferred();
        $resp = $this->assetCapitalizationReferredRepo->delete($assetCapitalizationReferred->id);
        $this->assertTrue($resp);
        $this->assertNull(AssetCapitalizationReferred::find($assetCapitalizationReferred->id), 'AssetCapitalizationReferred should not exist in DB');
    }
}

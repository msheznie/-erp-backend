<?php

use App\Models\AssetCapitalizatioDetReferred;
use App\Repositories\AssetCapitalizatioDetReferredRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetCapitalizatioDetReferredRepositoryTest extends TestCase
{
    use MakeAssetCapitalizatioDetReferredTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetCapitalizatioDetReferredRepository
     */
    protected $assetCapitalizatioDetReferredRepo;

    public function setUp()
    {
        parent::setUp();
        $this->assetCapitalizatioDetReferredRepo = App::make(AssetCapitalizatioDetReferredRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAssetCapitalizatioDetReferred()
    {
        $assetCapitalizatioDetReferred = $this->fakeAssetCapitalizatioDetReferredData();
        $createdAssetCapitalizatioDetReferred = $this->assetCapitalizatioDetReferredRepo->create($assetCapitalizatioDetReferred);
        $createdAssetCapitalizatioDetReferred = $createdAssetCapitalizatioDetReferred->toArray();
        $this->assertArrayHasKey('id', $createdAssetCapitalizatioDetReferred);
        $this->assertNotNull($createdAssetCapitalizatioDetReferred['id'], 'Created AssetCapitalizatioDetReferred must have id specified');
        $this->assertNotNull(AssetCapitalizatioDetReferred::find($createdAssetCapitalizatioDetReferred['id']), 'AssetCapitalizatioDetReferred with given id must be in DB');
        $this->assertModelData($assetCapitalizatioDetReferred, $createdAssetCapitalizatioDetReferred);
    }

    /**
     * @test read
     */
    public function testReadAssetCapitalizatioDetReferred()
    {
        $assetCapitalizatioDetReferred = $this->makeAssetCapitalizatioDetReferred();
        $dbAssetCapitalizatioDetReferred = $this->assetCapitalizatioDetReferredRepo->find($assetCapitalizatioDetReferred->id);
        $dbAssetCapitalizatioDetReferred = $dbAssetCapitalizatioDetReferred->toArray();
        $this->assertModelData($assetCapitalizatioDetReferred->toArray(), $dbAssetCapitalizatioDetReferred);
    }

    /**
     * @test update
     */
    public function testUpdateAssetCapitalizatioDetReferred()
    {
        $assetCapitalizatioDetReferred = $this->makeAssetCapitalizatioDetReferred();
        $fakeAssetCapitalizatioDetReferred = $this->fakeAssetCapitalizatioDetReferredData();
        $updatedAssetCapitalizatioDetReferred = $this->assetCapitalizatioDetReferredRepo->update($fakeAssetCapitalizatioDetReferred, $assetCapitalizatioDetReferred->id);
        $this->assertModelData($fakeAssetCapitalizatioDetReferred, $updatedAssetCapitalizatioDetReferred->toArray());
        $dbAssetCapitalizatioDetReferred = $this->assetCapitalizatioDetReferredRepo->find($assetCapitalizatioDetReferred->id);
        $this->assertModelData($fakeAssetCapitalizatioDetReferred, $dbAssetCapitalizatioDetReferred->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAssetCapitalizatioDetReferred()
    {
        $assetCapitalizatioDetReferred = $this->makeAssetCapitalizatioDetReferred();
        $resp = $this->assetCapitalizatioDetReferredRepo->delete($assetCapitalizatioDetReferred->id);
        $this->assertTrue($resp);
        $this->assertNull(AssetCapitalizatioDetReferred::find($assetCapitalizatioDetReferred->id), 'AssetCapitalizatioDetReferred should not exist in DB');
    }
}

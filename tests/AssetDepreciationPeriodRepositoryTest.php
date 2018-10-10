<?php

use App\Models\AssetDepreciationPeriod;
use App\Repositories\AssetDepreciationPeriodRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetDepreciationPeriodRepositoryTest extends TestCase
{
    use MakeAssetDepreciationPeriodTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetDepreciationPeriodRepository
     */
    protected $assetDepreciationPeriodRepo;

    public function setUp()
    {
        parent::setUp();
        $this->assetDepreciationPeriodRepo = App::make(AssetDepreciationPeriodRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAssetDepreciationPeriod()
    {
        $assetDepreciationPeriod = $this->fakeAssetDepreciationPeriodData();
        $createdAssetDepreciationPeriod = $this->assetDepreciationPeriodRepo->create($assetDepreciationPeriod);
        $createdAssetDepreciationPeriod = $createdAssetDepreciationPeriod->toArray();
        $this->assertArrayHasKey('id', $createdAssetDepreciationPeriod);
        $this->assertNotNull($createdAssetDepreciationPeriod['id'], 'Created AssetDepreciationPeriod must have id specified');
        $this->assertNotNull(AssetDepreciationPeriod::find($createdAssetDepreciationPeriod['id']), 'AssetDepreciationPeriod with given id must be in DB');
        $this->assertModelData($assetDepreciationPeriod, $createdAssetDepreciationPeriod);
    }

    /**
     * @test read
     */
    public function testReadAssetDepreciationPeriod()
    {
        $assetDepreciationPeriod = $this->makeAssetDepreciationPeriod();
        $dbAssetDepreciationPeriod = $this->assetDepreciationPeriodRepo->find($assetDepreciationPeriod->id);
        $dbAssetDepreciationPeriod = $dbAssetDepreciationPeriod->toArray();
        $this->assertModelData($assetDepreciationPeriod->toArray(), $dbAssetDepreciationPeriod);
    }

    /**
     * @test update
     */
    public function testUpdateAssetDepreciationPeriod()
    {
        $assetDepreciationPeriod = $this->makeAssetDepreciationPeriod();
        $fakeAssetDepreciationPeriod = $this->fakeAssetDepreciationPeriodData();
        $updatedAssetDepreciationPeriod = $this->assetDepreciationPeriodRepo->update($fakeAssetDepreciationPeriod, $assetDepreciationPeriod->id);
        $this->assertModelData($fakeAssetDepreciationPeriod, $updatedAssetDepreciationPeriod->toArray());
        $dbAssetDepreciationPeriod = $this->assetDepreciationPeriodRepo->find($assetDepreciationPeriod->id);
        $this->assertModelData($fakeAssetDepreciationPeriod, $dbAssetDepreciationPeriod->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAssetDepreciationPeriod()
    {
        $assetDepreciationPeriod = $this->makeAssetDepreciationPeriod();
        $resp = $this->assetDepreciationPeriodRepo->delete($assetDepreciationPeriod->id);
        $this->assertTrue($resp);
        $this->assertNull(AssetDepreciationPeriod::find($assetDepreciationPeriod->id), 'AssetDepreciationPeriod should not exist in DB');
    }
}

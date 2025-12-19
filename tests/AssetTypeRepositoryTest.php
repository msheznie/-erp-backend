<?php

use App\Models\AssetType;
use App\Repositories\AssetTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetTypeRepositoryTest extends TestCase
{
    use MakeAssetTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetTypeRepository
     */
    protected $assetTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->assetTypeRepo = App::make(AssetTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAssetType()
    {
        $assetType = $this->fakeAssetTypeData();
        $createdAssetType = $this->assetTypeRepo->create($assetType);
        $createdAssetType = $createdAssetType->toArray();
        $this->assertArrayHasKey('id', $createdAssetType);
        $this->assertNotNull($createdAssetType['id'], 'Created AssetType must have id specified');
        $this->assertNotNull(AssetType::find($createdAssetType['id']), 'AssetType with given id must be in DB');
        $this->assertModelData($assetType, $createdAssetType);
    }

    /**
     * @test read
     */
    public function testReadAssetType()
    {
        $assetType = $this->makeAssetType();
        $dbAssetType = $this->assetTypeRepo->find($assetType->id);
        $dbAssetType = $dbAssetType->toArray();
        $this->assertModelData($assetType->toArray(), $dbAssetType);
    }

    /**
     * @test update
     */
    public function testUpdateAssetType()
    {
        $assetType = $this->makeAssetType();
        $fakeAssetType = $this->fakeAssetTypeData();
        $updatedAssetType = $this->assetTypeRepo->update($fakeAssetType, $assetType->id);
        $this->assertModelData($fakeAssetType, $updatedAssetType->toArray());
        $dbAssetType = $this->assetTypeRepo->find($assetType->id);
        $this->assertModelData($fakeAssetType, $dbAssetType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAssetType()
    {
        $assetType = $this->makeAssetType();
        $resp = $this->assetTypeRepo->delete($assetType->id);
        $this->assertTrue($resp);
        $this->assertNull(AssetType::find($assetType->id), 'AssetType should not exist in DB');
    }
}

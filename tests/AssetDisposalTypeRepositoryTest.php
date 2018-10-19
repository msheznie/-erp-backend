<?php

use App\Models\AssetDisposalType;
use App\Repositories\AssetDisposalTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetDisposalTypeRepositoryTest extends TestCase
{
    use MakeAssetDisposalTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetDisposalTypeRepository
     */
    protected $assetDisposalTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->assetDisposalTypeRepo = App::make(AssetDisposalTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAssetDisposalType()
    {
        $assetDisposalType = $this->fakeAssetDisposalTypeData();
        $createdAssetDisposalType = $this->assetDisposalTypeRepo->create($assetDisposalType);
        $createdAssetDisposalType = $createdAssetDisposalType->toArray();
        $this->assertArrayHasKey('id', $createdAssetDisposalType);
        $this->assertNotNull($createdAssetDisposalType['id'], 'Created AssetDisposalType must have id specified');
        $this->assertNotNull(AssetDisposalType::find($createdAssetDisposalType['id']), 'AssetDisposalType with given id must be in DB');
        $this->assertModelData($assetDisposalType, $createdAssetDisposalType);
    }

    /**
     * @test read
     */
    public function testReadAssetDisposalType()
    {
        $assetDisposalType = $this->makeAssetDisposalType();
        $dbAssetDisposalType = $this->assetDisposalTypeRepo->find($assetDisposalType->id);
        $dbAssetDisposalType = $dbAssetDisposalType->toArray();
        $this->assertModelData($assetDisposalType->toArray(), $dbAssetDisposalType);
    }

    /**
     * @test update
     */
    public function testUpdateAssetDisposalType()
    {
        $assetDisposalType = $this->makeAssetDisposalType();
        $fakeAssetDisposalType = $this->fakeAssetDisposalTypeData();
        $updatedAssetDisposalType = $this->assetDisposalTypeRepo->update($fakeAssetDisposalType, $assetDisposalType->id);
        $this->assertModelData($fakeAssetDisposalType, $updatedAssetDisposalType->toArray());
        $dbAssetDisposalType = $this->assetDisposalTypeRepo->find($assetDisposalType->id);
        $this->assertModelData($fakeAssetDisposalType, $dbAssetDisposalType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAssetDisposalType()
    {
        $assetDisposalType = $this->makeAssetDisposalType();
        $resp = $this->assetDisposalTypeRepo->delete($assetDisposalType->id);
        $this->assertTrue($resp);
        $this->assertNull(AssetDisposalType::find($assetDisposalType->id), 'AssetDisposalType should not exist in DB');
    }
}

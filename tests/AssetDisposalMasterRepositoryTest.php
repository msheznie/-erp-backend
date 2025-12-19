<?php

use App\Models\AssetDisposalMaster;
use App\Repositories\AssetDisposalMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AssetDisposalMasterRepositoryTest extends TestCase
{
    use MakeAssetDisposalMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AssetDisposalMasterRepository
     */
    protected $assetDisposalMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->assetDisposalMasterRepo = App::make(AssetDisposalMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAssetDisposalMaster()
    {
        $assetDisposalMaster = $this->fakeAssetDisposalMasterData();
        $createdAssetDisposalMaster = $this->assetDisposalMasterRepo->create($assetDisposalMaster);
        $createdAssetDisposalMaster = $createdAssetDisposalMaster->toArray();
        $this->assertArrayHasKey('id', $createdAssetDisposalMaster);
        $this->assertNotNull($createdAssetDisposalMaster['id'], 'Created AssetDisposalMaster must have id specified');
        $this->assertNotNull(AssetDisposalMaster::find($createdAssetDisposalMaster['id']), 'AssetDisposalMaster with given id must be in DB');
        $this->assertModelData($assetDisposalMaster, $createdAssetDisposalMaster);
    }

    /**
     * @test read
     */
    public function testReadAssetDisposalMaster()
    {
        $assetDisposalMaster = $this->makeAssetDisposalMaster();
        $dbAssetDisposalMaster = $this->assetDisposalMasterRepo->find($assetDisposalMaster->id);
        $dbAssetDisposalMaster = $dbAssetDisposalMaster->toArray();
        $this->assertModelData($assetDisposalMaster->toArray(), $dbAssetDisposalMaster);
    }

    /**
     * @test update
     */
    public function testUpdateAssetDisposalMaster()
    {
        $assetDisposalMaster = $this->makeAssetDisposalMaster();
        $fakeAssetDisposalMaster = $this->fakeAssetDisposalMasterData();
        $updatedAssetDisposalMaster = $this->assetDisposalMasterRepo->update($fakeAssetDisposalMaster, $assetDisposalMaster->id);
        $this->assertModelData($fakeAssetDisposalMaster, $updatedAssetDisposalMaster->toArray());
        $dbAssetDisposalMaster = $this->assetDisposalMasterRepo->find($assetDisposalMaster->id);
        $this->assertModelData($fakeAssetDisposalMaster, $dbAssetDisposalMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAssetDisposalMaster()
    {
        $assetDisposalMaster = $this->makeAssetDisposalMaster();
        $resp = $this->assetDisposalMasterRepo->delete($assetDisposalMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(AssetDisposalMaster::find($assetDisposalMaster->id), 'AssetDisposalMaster should not exist in DB');
    }
}

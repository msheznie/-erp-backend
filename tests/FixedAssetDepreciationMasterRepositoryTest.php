<?php

use App\Models\FixedAssetDepreciationMaster;
use App\Repositories\FixedAssetDepreciationMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetDepreciationMasterRepositoryTest extends TestCase
{
    use MakeFixedAssetDepreciationMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FixedAssetDepreciationMasterRepository
     */
    protected $fixedAssetDepreciationMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->fixedAssetDepreciationMasterRepo = App::make(FixedAssetDepreciationMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFixedAssetDepreciationMaster()
    {
        $fixedAssetDepreciationMaster = $this->fakeFixedAssetDepreciationMasterData();
        $createdFixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepo->create($fixedAssetDepreciationMaster);
        $createdFixedAssetDepreciationMaster = $createdFixedAssetDepreciationMaster->toArray();
        $this->assertArrayHasKey('id', $createdFixedAssetDepreciationMaster);
        $this->assertNotNull($createdFixedAssetDepreciationMaster['id'], 'Created FixedAssetDepreciationMaster must have id specified');
        $this->assertNotNull(FixedAssetDepreciationMaster::find($createdFixedAssetDepreciationMaster['id']), 'FixedAssetDepreciationMaster with given id must be in DB');
        $this->assertModelData($fixedAssetDepreciationMaster, $createdFixedAssetDepreciationMaster);
    }

    /**
     * @test read
     */
    public function testReadFixedAssetDepreciationMaster()
    {
        $fixedAssetDepreciationMaster = $this->makeFixedAssetDepreciationMaster();
        $dbFixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepo->find($fixedAssetDepreciationMaster->id);
        $dbFixedAssetDepreciationMaster = $dbFixedAssetDepreciationMaster->toArray();
        $this->assertModelData($fixedAssetDepreciationMaster->toArray(), $dbFixedAssetDepreciationMaster);
    }

    /**
     * @test update
     */
    public function testUpdateFixedAssetDepreciationMaster()
    {
        $fixedAssetDepreciationMaster = $this->makeFixedAssetDepreciationMaster();
        $fakeFixedAssetDepreciationMaster = $this->fakeFixedAssetDepreciationMasterData();
        $updatedFixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepo->update($fakeFixedAssetDepreciationMaster, $fixedAssetDepreciationMaster->id);
        $this->assertModelData($fakeFixedAssetDepreciationMaster, $updatedFixedAssetDepreciationMaster->toArray());
        $dbFixedAssetDepreciationMaster = $this->fixedAssetDepreciationMasterRepo->find($fixedAssetDepreciationMaster->id);
        $this->assertModelData($fakeFixedAssetDepreciationMaster, $dbFixedAssetDepreciationMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFixedAssetDepreciationMaster()
    {
        $fixedAssetDepreciationMaster = $this->makeFixedAssetDepreciationMaster();
        $resp = $this->fixedAssetDepreciationMasterRepo->delete($fixedAssetDepreciationMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(FixedAssetDepreciationMaster::find($fixedAssetDepreciationMaster->id), 'FixedAssetDepreciationMaster should not exist in DB');
    }
}

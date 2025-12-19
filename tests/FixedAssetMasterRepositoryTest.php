<?php

use App\Models\FixedAssetMaster;
use App\Repositories\FixedAssetMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FixedAssetMasterRepositoryTest extends TestCase
{
    use MakeFixedAssetMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FixedAssetMasterRepository
     */
    protected $fixedAssetMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->fixedAssetMasterRepo = App::make(FixedAssetMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFixedAssetMaster()
    {
        $fixedAssetMaster = $this->fakeFixedAssetMasterData();
        $createdFixedAssetMaster = $this->fixedAssetMasterRepo->create($fixedAssetMaster);
        $createdFixedAssetMaster = $createdFixedAssetMaster->toArray();
        $this->assertArrayHasKey('id', $createdFixedAssetMaster);
        $this->assertNotNull($createdFixedAssetMaster['id'], 'Created FixedAssetMaster must have id specified');
        $this->assertNotNull(FixedAssetMaster::find($createdFixedAssetMaster['id']), 'FixedAssetMaster with given id must be in DB');
        $this->assertModelData($fixedAssetMaster, $createdFixedAssetMaster);
    }

    /**
     * @test read
     */
    public function testReadFixedAssetMaster()
    {
        $fixedAssetMaster = $this->makeFixedAssetMaster();
        $dbFixedAssetMaster = $this->fixedAssetMasterRepo->find($fixedAssetMaster->id);
        $dbFixedAssetMaster = $dbFixedAssetMaster->toArray();
        $this->assertModelData($fixedAssetMaster->toArray(), $dbFixedAssetMaster);
    }

    /**
     * @test update
     */
    public function testUpdateFixedAssetMaster()
    {
        $fixedAssetMaster = $this->makeFixedAssetMaster();
        $fakeFixedAssetMaster = $this->fakeFixedAssetMasterData();
        $updatedFixedAssetMaster = $this->fixedAssetMasterRepo->update($fakeFixedAssetMaster, $fixedAssetMaster->id);
        $this->assertModelData($fakeFixedAssetMaster, $updatedFixedAssetMaster->toArray());
        $dbFixedAssetMaster = $this->fixedAssetMasterRepo->find($fixedAssetMaster->id);
        $this->assertModelData($fakeFixedAssetMaster, $dbFixedAssetMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFixedAssetMaster()
    {
        $fixedAssetMaster = $this->makeFixedAssetMaster();
        $resp = $this->fixedAssetMasterRepo->delete($fixedAssetMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(FixedAssetMaster::find($fixedAssetMaster->id), 'FixedAssetMaster should not exist in DB');
    }
}

<?php

use App\Models\RigMaster;
use App\Repositories\RigMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RigMasterRepositoryTest extends TestCase
{
    use MakeRigMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var RigMasterRepository
     */
    protected $rigMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->rigMasterRepo = App::make(RigMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateRigMaster()
    {
        $rigMaster = $this->fakeRigMasterData();
        $createdRigMaster = $this->rigMasterRepo->create($rigMaster);
        $createdRigMaster = $createdRigMaster->toArray();
        $this->assertArrayHasKey('id', $createdRigMaster);
        $this->assertNotNull($createdRigMaster['id'], 'Created RigMaster must have id specified');
        $this->assertNotNull(RigMaster::find($createdRigMaster['id']), 'RigMaster with given id must be in DB');
        $this->assertModelData($rigMaster, $createdRigMaster);
    }

    /**
     * @test read
     */
    public function testReadRigMaster()
    {
        $rigMaster = $this->makeRigMaster();
        $dbRigMaster = $this->rigMasterRepo->find($rigMaster->id);
        $dbRigMaster = $dbRigMaster->toArray();
        $this->assertModelData($rigMaster->toArray(), $dbRigMaster);
    }

    /**
     * @test update
     */
    public function testUpdateRigMaster()
    {
        $rigMaster = $this->makeRigMaster();
        $fakeRigMaster = $this->fakeRigMasterData();
        $updatedRigMaster = $this->rigMasterRepo->update($fakeRigMaster, $rigMaster->id);
        $this->assertModelData($fakeRigMaster, $updatedRigMaster->toArray());
        $dbRigMaster = $this->rigMasterRepo->find($rigMaster->id);
        $this->assertModelData($fakeRigMaster, $dbRigMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteRigMaster()
    {
        $rigMaster = $this->makeRigMaster();
        $resp = $this->rigMasterRepo->delete($rigMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(RigMaster::find($rigMaster->id), 'RigMaster should not exist in DB');
    }
}

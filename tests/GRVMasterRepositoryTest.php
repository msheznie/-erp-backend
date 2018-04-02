<?php

use App\Models\GRVMaster;
use App\Repositories\GRVMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GRVMasterRepositoryTest extends TestCase
{
    use MakeGRVMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var GRVMasterRepository
     */
    protected $gRVMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->gRVMasterRepo = App::make(GRVMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateGRVMaster()
    {
        $gRVMaster = $this->fakeGRVMasterData();
        $createdGRVMaster = $this->gRVMasterRepo->create($gRVMaster);
        $createdGRVMaster = $createdGRVMaster->toArray();
        $this->assertArrayHasKey('id', $createdGRVMaster);
        $this->assertNotNull($createdGRVMaster['id'], 'Created GRVMaster must have id specified');
        $this->assertNotNull(GRVMaster::find($createdGRVMaster['id']), 'GRVMaster with given id must be in DB');
        $this->assertModelData($gRVMaster, $createdGRVMaster);
    }

    /**
     * @test read
     */
    public function testReadGRVMaster()
    {
        $gRVMaster = $this->makeGRVMaster();
        $dbGRVMaster = $this->gRVMasterRepo->find($gRVMaster->id);
        $dbGRVMaster = $dbGRVMaster->toArray();
        $this->assertModelData($gRVMaster->toArray(), $dbGRVMaster);
    }

    /**
     * @test update
     */
    public function testUpdateGRVMaster()
    {
        $gRVMaster = $this->makeGRVMaster();
        $fakeGRVMaster = $this->fakeGRVMasterData();
        $updatedGRVMaster = $this->gRVMasterRepo->update($fakeGRVMaster, $gRVMaster->id);
        $this->assertModelData($fakeGRVMaster, $updatedGRVMaster->toArray());
        $dbGRVMaster = $this->gRVMasterRepo->find($gRVMaster->id);
        $this->assertModelData($fakeGRVMaster, $dbGRVMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteGRVMaster()
    {
        $gRVMaster = $this->makeGRVMaster();
        $resp = $this->gRVMasterRepo->delete($gRVMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(GRVMaster::find($gRVMaster->id), 'GRVMaster should not exist in DB');
    }
}

<?php namespace Tests\Repositories;

use App\Models\AllocationMaster;
use App\Repositories\AllocationMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeAllocationMasterTrait;
use Tests\ApiTestTrait;

class AllocationMasterRepositoryTest extends TestCase
{
    use MakeAllocationMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AllocationMasterRepository
     */
    protected $allocationMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->allocationMasterRepo = \App::make(AllocationMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_allocation_master()
    {
        $allocationMaster = $this->fakeAllocationMasterData();
        $createdAllocationMaster = $this->allocationMasterRepo->create($allocationMaster);
        $createdAllocationMaster = $createdAllocationMaster->toArray();
        $this->assertArrayHasKey('id', $createdAllocationMaster);
        $this->assertNotNull($createdAllocationMaster['id'], 'Created AllocationMaster must have id specified');
        $this->assertNotNull(AllocationMaster::find($createdAllocationMaster['id']), 'AllocationMaster with given id must be in DB');
        $this->assertModelData($allocationMaster, $createdAllocationMaster);
    }

    /**
     * @test read
     */
    public function test_read_allocation_master()
    {
        $allocationMaster = $this->makeAllocationMaster();
        $dbAllocationMaster = $this->allocationMasterRepo->find($allocationMaster->id);
        $dbAllocationMaster = $dbAllocationMaster->toArray();
        $this->assertModelData($allocationMaster->toArray(), $dbAllocationMaster);
    }

    /**
     * @test update
     */
    public function test_update_allocation_master()
    {
        $allocationMaster = $this->makeAllocationMaster();
        $fakeAllocationMaster = $this->fakeAllocationMasterData();
        $updatedAllocationMaster = $this->allocationMasterRepo->update($fakeAllocationMaster, $allocationMaster->id);
        $this->assertModelData($fakeAllocationMaster, $updatedAllocationMaster->toArray());
        $dbAllocationMaster = $this->allocationMasterRepo->find($allocationMaster->id);
        $this->assertModelData($fakeAllocationMaster, $dbAllocationMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_allocation_master()
    {
        $allocationMaster = $this->makeAllocationMaster();
        $resp = $this->allocationMasterRepo->delete($allocationMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(AllocationMaster::find($allocationMaster->id), 'AllocationMaster should not exist in DB');
    }
}

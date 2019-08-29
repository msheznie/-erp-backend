<?php namespace Tests\Repositories;

use App\Models\LeaveDataMaster;
use App\Repositories\LeaveDataMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeLeaveDataMasterTrait;
use Tests\ApiTestTrait;

class LeaveDataMasterRepositoryTest extends TestCase
{
    use MakeLeaveDataMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var LeaveDataMasterRepository
     */
    protected $leaveDataMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->leaveDataMasterRepo = \App::make(LeaveDataMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_leave_data_master()
    {
        $leaveDataMaster = $this->fakeLeaveDataMasterData();
        $createdLeaveDataMaster = $this->leaveDataMasterRepo->create($leaveDataMaster);
        $createdLeaveDataMaster = $createdLeaveDataMaster->toArray();
        $this->assertArrayHasKey('id', $createdLeaveDataMaster);
        $this->assertNotNull($createdLeaveDataMaster['id'], 'Created LeaveDataMaster must have id specified');
        $this->assertNotNull(LeaveDataMaster::find($createdLeaveDataMaster['id']), 'LeaveDataMaster with given id must be in DB');
        $this->assertModelData($leaveDataMaster, $createdLeaveDataMaster);
    }

    /**
     * @test read
     */
    public function test_read_leave_data_master()
    {
        $leaveDataMaster = $this->makeLeaveDataMaster();
        $dbLeaveDataMaster = $this->leaveDataMasterRepo->find($leaveDataMaster->id);
        $dbLeaveDataMaster = $dbLeaveDataMaster->toArray();
        $this->assertModelData($leaveDataMaster->toArray(), $dbLeaveDataMaster);
    }

    /**
     * @test update
     */
    public function test_update_leave_data_master()
    {
        $leaveDataMaster = $this->makeLeaveDataMaster();
        $fakeLeaveDataMaster = $this->fakeLeaveDataMasterData();
        $updatedLeaveDataMaster = $this->leaveDataMasterRepo->update($fakeLeaveDataMaster, $leaveDataMaster->id);
        $this->assertModelData($fakeLeaveDataMaster, $updatedLeaveDataMaster->toArray());
        $dbLeaveDataMaster = $this->leaveDataMasterRepo->find($leaveDataMaster->id);
        $this->assertModelData($fakeLeaveDataMaster, $dbLeaveDataMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_leave_data_master()
    {
        $leaveDataMaster = $this->makeLeaveDataMaster();
        $resp = $this->leaveDataMasterRepo->delete($leaveDataMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(LeaveDataMaster::find($leaveDataMaster->id), 'LeaveDataMaster should not exist in DB');
    }
}

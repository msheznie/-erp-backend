<?php namespace Tests\Repositories;

use App\Models\LeaveAccrualMaster;
use App\Repositories\LeaveAccrualMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class LeaveAccrualMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var LeaveAccrualMasterRepository
     */
    protected $leaveAccrualMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->leaveAccrualMasterRepo = \App::make(LeaveAccrualMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_leave_accrual_master()
    {
        $leaveAccrualMaster = factory(LeaveAccrualMaster::class)->make()->toArray();

        $createdLeaveAccrualMaster = $this->leaveAccrualMasterRepo->create($leaveAccrualMaster);

        $createdLeaveAccrualMaster = $createdLeaveAccrualMaster->toArray();
        $this->assertArrayHasKey('id', $createdLeaveAccrualMaster);
        $this->assertNotNull($createdLeaveAccrualMaster['id'], 'Created LeaveAccrualMaster must have id specified');
        $this->assertNotNull(LeaveAccrualMaster::find($createdLeaveAccrualMaster['id']), 'LeaveAccrualMaster with given id must be in DB');
        $this->assertModelData($leaveAccrualMaster, $createdLeaveAccrualMaster);
    }

    /**
     * @test read
     */
    public function test_read_leave_accrual_master()
    {
        $leaveAccrualMaster = factory(LeaveAccrualMaster::class)->create();

        $dbLeaveAccrualMaster = $this->leaveAccrualMasterRepo->find($leaveAccrualMaster->id);

        $dbLeaveAccrualMaster = $dbLeaveAccrualMaster->toArray();
        $this->assertModelData($leaveAccrualMaster->toArray(), $dbLeaveAccrualMaster);
    }

    /**
     * @test update
     */
    public function test_update_leave_accrual_master()
    {
        $leaveAccrualMaster = factory(LeaveAccrualMaster::class)->create();
        $fakeLeaveAccrualMaster = factory(LeaveAccrualMaster::class)->make()->toArray();

        $updatedLeaveAccrualMaster = $this->leaveAccrualMasterRepo->update($fakeLeaveAccrualMaster, $leaveAccrualMaster->id);

        $this->assertModelData($fakeLeaveAccrualMaster, $updatedLeaveAccrualMaster->toArray());
        $dbLeaveAccrualMaster = $this->leaveAccrualMasterRepo->find($leaveAccrualMaster->id);
        $this->assertModelData($fakeLeaveAccrualMaster, $dbLeaveAccrualMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_leave_accrual_master()
    {
        $leaveAccrualMaster = factory(LeaveAccrualMaster::class)->create();

        $resp = $this->leaveAccrualMasterRepo->delete($leaveAccrualMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(LeaveAccrualMaster::find($leaveAccrualMaster->id), 'LeaveAccrualMaster should not exist in DB');
    }
}

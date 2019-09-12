<?php namespace Tests\Repositories;

use App\Models\LeaveApplicationType;
use App\Repositories\LeaveApplicationTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeLeaveApplicationTypeTrait;
use Tests\ApiTestTrait;

class LeaveApplicationTypeRepositoryTest extends TestCase
{
    use MakeLeaveApplicationTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var LeaveApplicationTypeRepository
     */
    protected $leaveApplicationTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->leaveApplicationTypeRepo = \App::make(LeaveApplicationTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_leave_application_type()
    {
        $leaveApplicationType = $this->fakeLeaveApplicationTypeData();
        $createdLeaveApplicationType = $this->leaveApplicationTypeRepo->create($leaveApplicationType);
        $createdLeaveApplicationType = $createdLeaveApplicationType->toArray();
        $this->assertArrayHasKey('id', $createdLeaveApplicationType);
        $this->assertNotNull($createdLeaveApplicationType['id'], 'Created LeaveApplicationType must have id specified');
        $this->assertNotNull(LeaveApplicationType::find($createdLeaveApplicationType['id']), 'LeaveApplicationType with given id must be in DB');
        $this->assertModelData($leaveApplicationType, $createdLeaveApplicationType);
    }

    /**
     * @test read
     */
    public function test_read_leave_application_type()
    {
        $leaveApplicationType = $this->makeLeaveApplicationType();
        $dbLeaveApplicationType = $this->leaveApplicationTypeRepo->find($leaveApplicationType->id);
        $dbLeaveApplicationType = $dbLeaveApplicationType->toArray();
        $this->assertModelData($leaveApplicationType->toArray(), $dbLeaveApplicationType);
    }

    /**
     * @test update
     */
    public function test_update_leave_application_type()
    {
        $leaveApplicationType = $this->makeLeaveApplicationType();
        $fakeLeaveApplicationType = $this->fakeLeaveApplicationTypeData();
        $updatedLeaveApplicationType = $this->leaveApplicationTypeRepo->update($fakeLeaveApplicationType, $leaveApplicationType->id);
        $this->assertModelData($fakeLeaveApplicationType, $updatedLeaveApplicationType->toArray());
        $dbLeaveApplicationType = $this->leaveApplicationTypeRepo->find($leaveApplicationType->id);
        $this->assertModelData($fakeLeaveApplicationType, $dbLeaveApplicationType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_leave_application_type()
    {
        $leaveApplicationType = $this->makeLeaveApplicationType();
        $resp = $this->leaveApplicationTypeRepo->delete($leaveApplicationType->id);
        $this->assertTrue($resp);
        $this->assertNull(LeaveApplicationType::find($leaveApplicationType->id), 'LeaveApplicationType should not exist in DB');
    }
}

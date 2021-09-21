<?php namespace Tests\Repositories;

use App\Models\LeaveGroup;
use App\Repositories\LeaveGroupRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class LeaveGroupRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var LeaveGroupRepository
     */
    protected $leaveGroupRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->leaveGroupRepo = \App::make(LeaveGroupRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_leave_group()
    {
        $leaveGroup = factory(LeaveGroup::class)->make()->toArray();

        $createdLeaveGroup = $this->leaveGroupRepo->create($leaveGroup);

        $createdLeaveGroup = $createdLeaveGroup->toArray();
        $this->assertArrayHasKey('id', $createdLeaveGroup);
        $this->assertNotNull($createdLeaveGroup['id'], 'Created LeaveGroup must have id specified');
        $this->assertNotNull(LeaveGroup::find($createdLeaveGroup['id']), 'LeaveGroup with given id must be in DB');
        $this->assertModelData($leaveGroup, $createdLeaveGroup);
    }

    /**
     * @test read
     */
    public function test_read_leave_group()
    {
        $leaveGroup = factory(LeaveGroup::class)->create();

        $dbLeaveGroup = $this->leaveGroupRepo->find($leaveGroup->id);

        $dbLeaveGroup = $dbLeaveGroup->toArray();
        $this->assertModelData($leaveGroup->toArray(), $dbLeaveGroup);
    }

    /**
     * @test update
     */
    public function test_update_leave_group()
    {
        $leaveGroup = factory(LeaveGroup::class)->create();
        $fakeLeaveGroup = factory(LeaveGroup::class)->make()->toArray();

        $updatedLeaveGroup = $this->leaveGroupRepo->update($fakeLeaveGroup, $leaveGroup->id);

        $this->assertModelData($fakeLeaveGroup, $updatedLeaveGroup->toArray());
        $dbLeaveGroup = $this->leaveGroupRepo->find($leaveGroup->id);
        $this->assertModelData($fakeLeaveGroup, $dbLeaveGroup->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_leave_group()
    {
        $leaveGroup = factory(LeaveGroup::class)->create();

        $resp = $this->leaveGroupRepo->delete($leaveGroup->id);

        $this->assertTrue($resp);
        $this->assertNull(LeaveGroup::find($leaveGroup->id), 'LeaveGroup should not exist in DB');
    }
}

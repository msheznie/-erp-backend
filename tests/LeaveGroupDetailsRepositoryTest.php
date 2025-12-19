<?php namespace Tests\Repositories;

use App\Models\LeaveGroupDetails;
use App\Repositories\LeaveGroupDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class LeaveGroupDetailsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var LeaveGroupDetailsRepository
     */
    protected $leaveGroupDetailsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->leaveGroupDetailsRepo = \App::make(LeaveGroupDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_leave_group_details()
    {
        $leaveGroupDetails = factory(LeaveGroupDetails::class)->make()->toArray();

        $createdLeaveGroupDetails = $this->leaveGroupDetailsRepo->create($leaveGroupDetails);

        $createdLeaveGroupDetails = $createdLeaveGroupDetails->toArray();
        $this->assertArrayHasKey('id', $createdLeaveGroupDetails);
        $this->assertNotNull($createdLeaveGroupDetails['id'], 'Created LeaveGroupDetails must have id specified');
        $this->assertNotNull(LeaveGroupDetails::find($createdLeaveGroupDetails['id']), 'LeaveGroupDetails with given id must be in DB');
        $this->assertModelData($leaveGroupDetails, $createdLeaveGroupDetails);
    }

    /**
     * @test read
     */
    public function test_read_leave_group_details()
    {
        $leaveGroupDetails = factory(LeaveGroupDetails::class)->create();

        $dbLeaveGroupDetails = $this->leaveGroupDetailsRepo->find($leaveGroupDetails->id);

        $dbLeaveGroupDetails = $dbLeaveGroupDetails->toArray();
        $this->assertModelData($leaveGroupDetails->toArray(), $dbLeaveGroupDetails);
    }

    /**
     * @test update
     */
    public function test_update_leave_group_details()
    {
        $leaveGroupDetails = factory(LeaveGroupDetails::class)->create();
        $fakeLeaveGroupDetails = factory(LeaveGroupDetails::class)->make()->toArray();

        $updatedLeaveGroupDetails = $this->leaveGroupDetailsRepo->update($fakeLeaveGroupDetails, $leaveGroupDetails->id);

        $this->assertModelData($fakeLeaveGroupDetails, $updatedLeaveGroupDetails->toArray());
        $dbLeaveGroupDetails = $this->leaveGroupDetailsRepo->find($leaveGroupDetails->id);
        $this->assertModelData($fakeLeaveGroupDetails, $dbLeaveGroupDetails->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_leave_group_details()
    {
        $leaveGroupDetails = factory(LeaveGroupDetails::class)->create();

        $resp = $this->leaveGroupDetailsRepo->delete($leaveGroupDetails->id);

        $this->assertTrue($resp);
        $this->assertNull(LeaveGroupDetails::find($leaveGroupDetails->id), 'LeaveGroupDetails should not exist in DB');
    }
}

<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\LeaveGroupDetails;

class LeaveGroupDetailsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_leave_group_details()
    {
        $leaveGroupDetails = factory(LeaveGroupDetails::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/leave_group_details', $leaveGroupDetails
        );

        $this->assertApiResponse($leaveGroupDetails);
    }

    /**
     * @test
     */
    public function test_read_leave_group_details()
    {
        $leaveGroupDetails = factory(LeaveGroupDetails::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/leave_group_details/'.$leaveGroupDetails->id
        );

        $this->assertApiResponse($leaveGroupDetails->toArray());
    }

    /**
     * @test
     */
    public function test_update_leave_group_details()
    {
        $leaveGroupDetails = factory(LeaveGroupDetails::class)->create();
        $editedLeaveGroupDetails = factory(LeaveGroupDetails::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/leave_group_details/'.$leaveGroupDetails->id,
            $editedLeaveGroupDetails
        );

        $this->assertApiResponse($editedLeaveGroupDetails);
    }

    /**
     * @test
     */
    public function test_delete_leave_group_details()
    {
        $leaveGroupDetails = factory(LeaveGroupDetails::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/leave_group_details/'.$leaveGroupDetails->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/leave_group_details/'.$leaveGroupDetails->id
        );

        $this->response->assertStatus(404);
    }
}

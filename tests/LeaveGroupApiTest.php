<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\LeaveGroup;

class LeaveGroupApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_leave_group()
    {
        $leaveGroup = factory(LeaveGroup::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/leave_groups', $leaveGroup
        );

        $this->assertApiResponse($leaveGroup);
    }

    /**
     * @test
     */
    public function test_read_leave_group()
    {
        $leaveGroup = factory(LeaveGroup::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/leave_groups/'.$leaveGroup->id
        );

        $this->assertApiResponse($leaveGroup->toArray());
    }

    /**
     * @test
     */
    public function test_update_leave_group()
    {
        $leaveGroup = factory(LeaveGroup::class)->create();
        $editedLeaveGroup = factory(LeaveGroup::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/leave_groups/'.$leaveGroup->id,
            $editedLeaveGroup
        );

        $this->assertApiResponse($editedLeaveGroup);
    }

    /**
     * @test
     */
    public function test_delete_leave_group()
    {
        $leaveGroup = factory(LeaveGroup::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/leave_groups/'.$leaveGroup->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/leave_groups/'.$leaveGroup->id
        );

        $this->response->assertStatus(404);
    }
}

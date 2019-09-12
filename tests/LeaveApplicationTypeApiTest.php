<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeLeaveApplicationTypeTrait;
use Tests\ApiTestTrait;

class LeaveApplicationTypeApiTest extends TestCase
{
    use MakeLeaveApplicationTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_leave_application_type()
    {
        $leaveApplicationType = $this->fakeLeaveApplicationTypeData();
        $this->response = $this->json('POST', '/api/leaveApplicationTypes', $leaveApplicationType);

        $this->assertApiResponse($leaveApplicationType);
    }

    /**
     * @test
     */
    public function test_read_leave_application_type()
    {
        $leaveApplicationType = $this->makeLeaveApplicationType();
        $this->response = $this->json('GET', '/api/leaveApplicationTypes/'.$leaveApplicationType->id);

        $this->assertApiResponse($leaveApplicationType->toArray());
    }

    /**
     * @test
     */
    public function test_update_leave_application_type()
    {
        $leaveApplicationType = $this->makeLeaveApplicationType();
        $editedLeaveApplicationType = $this->fakeLeaveApplicationTypeData();

        $this->response = $this->json('PUT', '/api/leaveApplicationTypes/'.$leaveApplicationType->id, $editedLeaveApplicationType);

        $this->assertApiResponse($editedLeaveApplicationType);
    }

    /**
     * @test
     */
    public function test_delete_leave_application_type()
    {
        $leaveApplicationType = $this->makeLeaveApplicationType();
        $this->response = $this->json('DELETE', '/api/leaveApplicationTypes/'.$leaveApplicationType->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/leaveApplicationTypes/'.$leaveApplicationType->id);

        $this->response->assertStatus(404);
    }
}

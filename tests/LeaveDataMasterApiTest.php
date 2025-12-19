<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeLeaveDataMasterTrait;
use Tests\ApiTestTrait;

class LeaveDataMasterApiTest extends TestCase
{
    use MakeLeaveDataMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_leave_data_master()
    {
        $leaveDataMaster = $this->fakeLeaveDataMasterData();
        $this->response = $this->json('POST', '/api/leaveDataMasters', $leaveDataMaster);

        $this->assertApiResponse($leaveDataMaster);
    }

    /**
     * @test
     */
    public function test_read_leave_data_master()
    {
        $leaveDataMaster = $this->makeLeaveDataMaster();
        $this->response = $this->json('GET', '/api/leaveDataMasters/'.$leaveDataMaster->id);

        $this->assertApiResponse($leaveDataMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_leave_data_master()
    {
        $leaveDataMaster = $this->makeLeaveDataMaster();
        $editedLeaveDataMaster = $this->fakeLeaveDataMasterData();

        $this->response = $this->json('PUT', '/api/leaveDataMasters/'.$leaveDataMaster->id, $editedLeaveDataMaster);

        $this->assertApiResponse($editedLeaveDataMaster);
    }

    /**
     * @test
     */
    public function test_delete_leave_data_master()
    {
        $leaveDataMaster = $this->makeLeaveDataMaster();
        $this->response = $this->json('DELETE', '/api/leaveDataMasters/'.$leaveDataMaster->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/leaveDataMasters/'.$leaveDataMaster->id);

        $this->response->assertStatus(404);
    }
}

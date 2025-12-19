<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeLeaveDataDetailTrait;
use Tests\ApiTestTrait;

class LeaveDataDetailApiTest extends TestCase
{
    use MakeLeaveDataDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_leave_data_detail()
    {
        $leaveDataDetail = $this->fakeLeaveDataDetailData();
        $this->response = $this->json('POST', '/api/leaveDataDetails', $leaveDataDetail);

        $this->assertApiResponse($leaveDataDetail);
    }

    /**
     * @test
     */
    public function test_read_leave_data_detail()
    {
        $leaveDataDetail = $this->makeLeaveDataDetail();
        $this->response = $this->json('GET', '/api/leaveDataDetails/'.$leaveDataDetail->id);

        $this->assertApiResponse($leaveDataDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_leave_data_detail()
    {
        $leaveDataDetail = $this->makeLeaveDataDetail();
        $editedLeaveDataDetail = $this->fakeLeaveDataDetailData();

        $this->response = $this->json('PUT', '/api/leaveDataDetails/'.$leaveDataDetail->id, $editedLeaveDataDetail);

        $this->assertApiResponse($editedLeaveDataDetail);
    }

    /**
     * @test
     */
    public function test_delete_leave_data_detail()
    {
        $leaveDataDetail = $this->makeLeaveDataDetail();
        $this->response = $this->json('DELETE', '/api/leaveDataDetails/'.$leaveDataDetail->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/leaveDataDetails/'.$leaveDataDetail->id);

        $this->response->assertStatus(404);
    }
}

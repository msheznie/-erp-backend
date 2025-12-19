<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\LeaveAccrualDetail;

class LeaveAccrualDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_leave_accrual_detail()
    {
        $leaveAccrualDetail = factory(LeaveAccrualDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/leave_accrual_details', $leaveAccrualDetail
        );

        $this->assertApiResponse($leaveAccrualDetail);
    }

    /**
     * @test
     */
    public function test_read_leave_accrual_detail()
    {
        $leaveAccrualDetail = factory(LeaveAccrualDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/leave_accrual_details/'.$leaveAccrualDetail->id
        );

        $this->assertApiResponse($leaveAccrualDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_leave_accrual_detail()
    {
        $leaveAccrualDetail = factory(LeaveAccrualDetail::class)->create();
        $editedLeaveAccrualDetail = factory(LeaveAccrualDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/leave_accrual_details/'.$leaveAccrualDetail->id,
            $editedLeaveAccrualDetail
        );

        $this->assertApiResponse($editedLeaveAccrualDetail);
    }

    /**
     * @test
     */
    public function test_delete_leave_accrual_detail()
    {
        $leaveAccrualDetail = factory(LeaveAccrualDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/leave_accrual_details/'.$leaveAccrualDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/leave_accrual_details/'.$leaveAccrualDetail->id
        );

        $this->response->assertStatus(404);
    }
}

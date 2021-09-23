<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\LeaveAccrualMaster;

class LeaveAccrualMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_leave_accrual_master()
    {
        $leaveAccrualMaster = factory(LeaveAccrualMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/leave_accrual_masters', $leaveAccrualMaster
        );

        $this->assertApiResponse($leaveAccrualMaster);
    }

    /**
     * @test
     */
    public function test_read_leave_accrual_master()
    {
        $leaveAccrualMaster = factory(LeaveAccrualMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/leave_accrual_masters/'.$leaveAccrualMaster->id
        );

        $this->assertApiResponse($leaveAccrualMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_leave_accrual_master()
    {
        $leaveAccrualMaster = factory(LeaveAccrualMaster::class)->create();
        $editedLeaveAccrualMaster = factory(LeaveAccrualMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/leave_accrual_masters/'.$leaveAccrualMaster->id,
            $editedLeaveAccrualMaster
        );

        $this->assertApiResponse($editedLeaveAccrualMaster);
    }

    /**
     * @test
     */
    public function test_delete_leave_accrual_master()
    {
        $leaveAccrualMaster = factory(LeaveAccrualMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/leave_accrual_masters/'.$leaveAccrualMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/leave_accrual_masters/'.$leaveAccrualMaster->id
        );

        $this->response->assertStatus(404);
    }
}

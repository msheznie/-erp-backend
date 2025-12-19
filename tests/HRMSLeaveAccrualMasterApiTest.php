<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeHRMSLeaveAccrualMasterTrait;
use Tests\ApiTestTrait;

class HRMSLeaveAccrualMasterApiTest extends TestCase
{
    use MakeHRMSLeaveAccrualMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_h_r_m_s_leave_accrual_master()
    {
        $hRMSLeaveAccrualMaster = $this->fakeHRMSLeaveAccrualMasterData();
        $this->response = $this->json('POST', '/api/hRMSLeaveAccrualMasters', $hRMSLeaveAccrualMaster);

        $this->assertApiResponse($hRMSLeaveAccrualMaster);
    }

    /**
     * @test
     */
    public function test_read_h_r_m_s_leave_accrual_master()
    {
        $hRMSLeaveAccrualMaster = $this->makeHRMSLeaveAccrualMaster();
        $this->response = $this->json('GET', '/api/hRMSLeaveAccrualMasters/'.$hRMSLeaveAccrualMaster->id);

        $this->assertApiResponse($hRMSLeaveAccrualMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_h_r_m_s_leave_accrual_master()
    {
        $hRMSLeaveAccrualMaster = $this->makeHRMSLeaveAccrualMaster();
        $editedHRMSLeaveAccrualMaster = $this->fakeHRMSLeaveAccrualMasterData();

        $this->response = $this->json('PUT', '/api/hRMSLeaveAccrualMasters/'.$hRMSLeaveAccrualMaster->id, $editedHRMSLeaveAccrualMaster);

        $this->assertApiResponse($editedHRMSLeaveAccrualMaster);
    }

    /**
     * @test
     */
    public function test_delete_h_r_m_s_leave_accrual_master()
    {
        $hRMSLeaveAccrualMaster = $this->makeHRMSLeaveAccrualMaster();
        $this->response = $this->json('DELETE', '/api/hRMSLeaveAccrualMasters/'.$hRMSLeaveAccrualMaster->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/hRMSLeaveAccrualMasters/'.$hRMSLeaveAccrualMaster->id);

        $this->response->assertStatus(404);
    }
}

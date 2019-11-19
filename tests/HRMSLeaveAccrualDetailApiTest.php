<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeHRMSLeaveAccrualDetailTrait;
use Tests\ApiTestTrait;

class HRMSLeaveAccrualDetailApiTest extends TestCase
{
    use MakeHRMSLeaveAccrualDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_h_r_m_s_leave_accrual_detail()
    {
        $hRMSLeaveAccrualDetail = $this->fakeHRMSLeaveAccrualDetailData();
        $this->response = $this->json('POST', '/api/hRMSLeaveAccrualDetails', $hRMSLeaveAccrualDetail);

        $this->assertApiResponse($hRMSLeaveAccrualDetail);
    }

    /**
     * @test
     */
    public function test_read_h_r_m_s_leave_accrual_detail()
    {
        $hRMSLeaveAccrualDetail = $this->makeHRMSLeaveAccrualDetail();
        $this->response = $this->json('GET', '/api/hRMSLeaveAccrualDetails/'.$hRMSLeaveAccrualDetail->id);

        $this->assertApiResponse($hRMSLeaveAccrualDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_h_r_m_s_leave_accrual_detail()
    {
        $hRMSLeaveAccrualDetail = $this->makeHRMSLeaveAccrualDetail();
        $editedHRMSLeaveAccrualDetail = $this->fakeHRMSLeaveAccrualDetailData();

        $this->response = $this->json('PUT', '/api/hRMSLeaveAccrualDetails/'.$hRMSLeaveAccrualDetail->id, $editedHRMSLeaveAccrualDetail);

        $this->assertApiResponse($editedHRMSLeaveAccrualDetail);
    }

    /**
     * @test
     */
    public function test_delete_h_r_m_s_leave_accrual_detail()
    {
        $hRMSLeaveAccrualDetail = $this->makeHRMSLeaveAccrualDetail();
        $this->response = $this->json('DELETE', '/api/hRMSLeaveAccrualDetails/'.$hRMSLeaveAccrualDetail->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/hRMSLeaveAccrualDetails/'.$hRMSLeaveAccrualDetail->id);

        $this->response->assertStatus(404);
    }
}

<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeHRMSLeaveAccrualPolicyTypeTrait;
use Tests\ApiTestTrait;

class HRMSLeaveAccrualPolicyTypeApiTest extends TestCase
{
    use MakeHRMSLeaveAccrualPolicyTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_h_r_m_s_leave_accrual_policy_type()
    {
        $hRMSLeaveAccrualPolicyType = $this->fakeHRMSLeaveAccrualPolicyTypeData();
        $this->response = $this->json('POST', '/api/hRMSLeaveAccrualPolicyTypes', $hRMSLeaveAccrualPolicyType);

        $this->assertApiResponse($hRMSLeaveAccrualPolicyType);
    }

    /**
     * @test
     */
    public function test_read_h_r_m_s_leave_accrual_policy_type()
    {
        $hRMSLeaveAccrualPolicyType = $this->makeHRMSLeaveAccrualPolicyType();
        $this->response = $this->json('GET', '/api/hRMSLeaveAccrualPolicyTypes/'.$hRMSLeaveAccrualPolicyType->id);

        $this->assertApiResponse($hRMSLeaveAccrualPolicyType->toArray());
    }

    /**
     * @test
     */
    public function test_update_h_r_m_s_leave_accrual_policy_type()
    {
        $hRMSLeaveAccrualPolicyType = $this->makeHRMSLeaveAccrualPolicyType();
        $editedHRMSLeaveAccrualPolicyType = $this->fakeHRMSLeaveAccrualPolicyTypeData();

        $this->response = $this->json('PUT', '/api/hRMSLeaveAccrualPolicyTypes/'.$hRMSLeaveAccrualPolicyType->id, $editedHRMSLeaveAccrualPolicyType);

        $this->assertApiResponse($editedHRMSLeaveAccrualPolicyType);
    }

    /**
     * @test
     */
    public function test_delete_h_r_m_s_leave_accrual_policy_type()
    {
        $hRMSLeaveAccrualPolicyType = $this->makeHRMSLeaveAccrualPolicyType();
        $this->response = $this->json('DELETE', '/api/hRMSLeaveAccrualPolicyTypes/'.$hRMSLeaveAccrualPolicyType->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/hRMSLeaveAccrualPolicyTypes/'.$hRMSLeaveAccrualPolicyType->id);

        $this->response->assertStatus(404);
    }
}

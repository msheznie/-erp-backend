<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeHRMSPeriodMasterTrait;
use Tests\ApiTestTrait;

class HRMSPeriodMasterApiTest extends TestCase
{
    use MakeHRMSPeriodMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_h_r_m_s_period_master()
    {
        $hRMSPeriodMaster = $this->fakeHRMSPeriodMasterData();
        $this->response = $this->json('POST', '/api/hRMSPeriodMasters', $hRMSPeriodMaster);

        $this->assertApiResponse($hRMSPeriodMaster);
    }

    /**
     * @test
     */
    public function test_read_h_r_m_s_period_master()
    {
        $hRMSPeriodMaster = $this->makeHRMSPeriodMaster();
        $this->response = $this->json('GET', '/api/hRMSPeriodMasters/'.$hRMSPeriodMaster->id);

        $this->assertApiResponse($hRMSPeriodMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_h_r_m_s_period_master()
    {
        $hRMSPeriodMaster = $this->makeHRMSPeriodMaster();
        $editedHRMSPeriodMaster = $this->fakeHRMSPeriodMasterData();

        $this->response = $this->json('PUT', '/api/hRMSPeriodMasters/'.$hRMSPeriodMaster->id, $editedHRMSPeriodMaster);

        $this->assertApiResponse($editedHRMSPeriodMaster);
    }

    /**
     * @test
     */
    public function test_delete_h_r_m_s_period_master()
    {
        $hRMSPeriodMaster = $this->makeHRMSPeriodMaster();
        $this->response = $this->json('DELETE', '/api/hRMSPeriodMasters/'.$hRMSPeriodMaster->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/hRMSPeriodMasters/'.$hRMSPeriodMaster->id);

        $this->response->assertStatus(404);
    }
}

<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMECompanyPolicyMaster;

class SMECompanyPolicyMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_company_policy_master()
    {
        $sMECompanyPolicyMaster = factory(SMECompanyPolicyMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_company_policy_masters', $sMECompanyPolicyMaster
        );

        $this->assertApiResponse($sMECompanyPolicyMaster);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_company_policy_master()
    {
        $sMECompanyPolicyMaster = factory(SMECompanyPolicyMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_company_policy_masters/'.$sMECompanyPolicyMaster->id
        );

        $this->assertApiResponse($sMECompanyPolicyMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_company_policy_master()
    {
        $sMECompanyPolicyMaster = factory(SMECompanyPolicyMaster::class)->create();
        $editedSMECompanyPolicyMaster = factory(SMECompanyPolicyMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_company_policy_masters/'.$sMECompanyPolicyMaster->id,
            $editedSMECompanyPolicyMaster
        );

        $this->assertApiResponse($editedSMECompanyPolicyMaster);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_company_policy_master()
    {
        $sMECompanyPolicyMaster = factory(SMECompanyPolicyMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_company_policy_masters/'.$sMECompanyPolicyMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_company_policy_masters/'.$sMECompanyPolicyMaster->id
        );

        $this->response->assertStatus(404);
    }
}

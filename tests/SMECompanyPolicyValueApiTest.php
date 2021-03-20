<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMECompanyPolicyValue;

class SMECompanyPolicyValueApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_company_policy_value()
    {
        $sMECompanyPolicyValue = factory(SMECompanyPolicyValue::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_company_policy_values', $sMECompanyPolicyValue
        );

        $this->assertApiResponse($sMECompanyPolicyValue);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_company_policy_value()
    {
        $sMECompanyPolicyValue = factory(SMECompanyPolicyValue::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_company_policy_values/'.$sMECompanyPolicyValue->id
        );

        $this->assertApiResponse($sMECompanyPolicyValue->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_company_policy_value()
    {
        $sMECompanyPolicyValue = factory(SMECompanyPolicyValue::class)->create();
        $editedSMECompanyPolicyValue = factory(SMECompanyPolicyValue::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_company_policy_values/'.$sMECompanyPolicyValue->id,
            $editedSMECompanyPolicyValue
        );

        $this->assertApiResponse($editedSMECompanyPolicyValue);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_company_policy_value()
    {
        $sMECompanyPolicyValue = factory(SMECompanyPolicyValue::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_company_policy_values/'.$sMECompanyPolicyValue->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_company_policy_values/'.$sMECompanyPolicyValue->id
        );

        $this->response->assertStatus(404);
    }
}

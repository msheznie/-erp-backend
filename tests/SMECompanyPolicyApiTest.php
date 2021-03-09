<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SMECompanyPolicy;

class SMECompanyPolicyApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_s_m_e_company_policy()
    {
        $sMECompanyPolicy = factory(SMECompanyPolicy::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/s_m_e_company_policies', $sMECompanyPolicy
        );

        $this->assertApiResponse($sMECompanyPolicy);
    }

    /**
     * @test
     */
    public function test_read_s_m_e_company_policy()
    {
        $sMECompanyPolicy = factory(SMECompanyPolicy::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/s_m_e_company_policies/'.$sMECompanyPolicy->id
        );

        $this->assertApiResponse($sMECompanyPolicy->toArray());
    }

    /**
     * @test
     */
    public function test_update_s_m_e_company_policy()
    {
        $sMECompanyPolicy = factory(SMECompanyPolicy::class)->create();
        $editedSMECompanyPolicy = factory(SMECompanyPolicy::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/s_m_e_company_policies/'.$sMECompanyPolicy->id,
            $editedSMECompanyPolicy
        );

        $this->assertApiResponse($editedSMECompanyPolicy);
    }

    /**
     * @test
     */
    public function test_delete_s_m_e_company_policy()
    {
        $sMECompanyPolicy = factory(SMECompanyPolicy::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/s_m_e_company_policies/'.$sMECompanyPolicy->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/s_m_e_company_policies/'.$sMECompanyPolicy->id
        );

        $this->response->assertStatus(404);
    }
}

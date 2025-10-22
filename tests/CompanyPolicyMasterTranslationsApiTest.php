<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CompanyPolicyMasterTranslations;

class CompanyPolicyMasterTranslationsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_company_policy_master_translations()
    {
        $companyPolicyMasterTranslations = factory(CompanyPolicyMasterTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/company_policy_master_translations', $companyPolicyMasterTranslations
        );

        $this->assertApiResponse($companyPolicyMasterTranslations);
    }

    /**
     * @test
     */
    public function test_read_company_policy_master_translations()
    {
        $companyPolicyMasterTranslations = factory(CompanyPolicyMasterTranslations::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/company_policy_master_translations/'.$companyPolicyMasterTranslations->id
        );

        $this->assertApiResponse($companyPolicyMasterTranslations->toArray());
    }

    /**
     * @test
     */
    public function test_update_company_policy_master_translations()
    {
        $companyPolicyMasterTranslations = factory(CompanyPolicyMasterTranslations::class)->create();
        $editedCompanyPolicyMasterTranslations = factory(CompanyPolicyMasterTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/company_policy_master_translations/'.$companyPolicyMasterTranslations->id,
            $editedCompanyPolicyMasterTranslations
        );

        $this->assertApiResponse($editedCompanyPolicyMasterTranslations);
    }

    /**
     * @test
     */
    public function test_delete_company_policy_master_translations()
    {
        $companyPolicyMasterTranslations = factory(CompanyPolicyMasterTranslations::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/company_policy_master_translations/'.$companyPolicyMasterTranslations->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/company_policy_master_translations/'.$companyPolicyMasterTranslations->id
        );

        $this->response->assertStatus(404);
    }
}

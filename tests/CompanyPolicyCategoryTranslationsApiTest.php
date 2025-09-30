<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CompanyPolicyCategoryTranslations;

class CompanyPolicyCategoryTranslationsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_company_policy_category_translations()
    {
        $companyPolicyCategoryTranslations = factory(CompanyPolicyCategoryTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/company_policy_category_translations', $companyPolicyCategoryTranslations
        );

        $this->assertApiResponse($companyPolicyCategoryTranslations);
    }

    /**
     * @test
     */
    public function test_read_company_policy_category_translations()
    {
        $companyPolicyCategoryTranslations = factory(CompanyPolicyCategoryTranslations::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/company_policy_category_translations/'.$companyPolicyCategoryTranslations->id
        );

        $this->assertApiResponse($companyPolicyCategoryTranslations->toArray());
    }

    /**
     * @test
     */
    public function test_update_company_policy_category_translations()
    {
        $companyPolicyCategoryTranslations = factory(CompanyPolicyCategoryTranslations::class)->create();
        $editedCompanyPolicyCategoryTranslations = factory(CompanyPolicyCategoryTranslations::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/company_policy_category_translations/'.$companyPolicyCategoryTranslations->id,
            $editedCompanyPolicyCategoryTranslations
        );

        $this->assertApiResponse($editedCompanyPolicyCategoryTranslations);
    }

    /**
     * @test
     */
    public function test_delete_company_policy_category_translations()
    {
        $companyPolicyCategoryTranslations = factory(CompanyPolicyCategoryTranslations::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/company_policy_category_translations/'.$companyPolicyCategoryTranslations->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/company_policy_category_translations/'.$companyPolicyCategoryTranslations->id
        );

        $this->response->assertStatus(404);
    }
}

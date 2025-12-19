<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyPolicyCategoryApiTest extends TestCase
{
    use MakeCompanyPolicyCategoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCompanyPolicyCategory()
    {
        $companyPolicyCategory = $this->fakeCompanyPolicyCategoryData();
        $this->json('POST', '/api/v1/companyPolicyCategories', $companyPolicyCategory);

        $this->assertApiResponse($companyPolicyCategory);
    }

    /**
     * @test
     */
    public function testReadCompanyPolicyCategory()
    {
        $companyPolicyCategory = $this->makeCompanyPolicyCategory();
        $this->json('GET', '/api/v1/companyPolicyCategories/'.$companyPolicyCategory->id);

        $this->assertApiResponse($companyPolicyCategory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCompanyPolicyCategory()
    {
        $companyPolicyCategory = $this->makeCompanyPolicyCategory();
        $editedCompanyPolicyCategory = $this->fakeCompanyPolicyCategoryData();

        $this->json('PUT', '/api/v1/companyPolicyCategories/'.$companyPolicyCategory->id, $editedCompanyPolicyCategory);

        $this->assertApiResponse($editedCompanyPolicyCategory);
    }

    /**
     * @test
     */
    public function testDeleteCompanyPolicyCategory()
    {
        $companyPolicyCategory = $this->makeCompanyPolicyCategory();
        $this->json('DELETE', '/api/v1/companyPolicyCategories/'.$companyPolicyCategory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/companyPolicyCategories/'.$companyPolicyCategory->id);

        $this->assertResponseStatus(404);
    }
}

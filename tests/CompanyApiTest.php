<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyApiTest extends TestCase
{
    use MakeCompanyTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCompany()
    {
        $company = $this->fakeCompanyData();
        $this->json('POST', '/api/v1/companies', $company);

        $this->assertApiResponse($company);
    }

    /**
     * @test
     */
    public function testReadCompany()
    {
        $company = $this->makeCompany();
        $this->json('GET', '/api/v1/companies/'.$company->id);

        $this->assertApiResponse($company->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCompany()
    {
        $company = $this->makeCompany();
        $editedCompany = $this->fakeCompanyData();

        $this->json('PUT', '/api/v1/companies/'.$company->id, $editedCompany);

        $this->assertApiResponse($editedCompany);
    }

    /**
     * @test
     */
    public function testDeleteCompany()
    {
        $company = $this->makeCompany();
        $this->json('DELETE', '/api/v1/companies/'.$company->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/companies/'.$company->id);

        $this->assertResponseStatus(404);
    }
}

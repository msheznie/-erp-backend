<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyFinanceYearApiTest extends TestCase
{
    use MakeCompanyFinanceYearTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCompanyFinanceYear()
    {
        $companyFinanceYear = $this->fakeCompanyFinanceYearData();
        $this->json('POST', '/api/v1/companyFinanceYears', $companyFinanceYear);

        $this->assertApiResponse($companyFinanceYear);
    }

    /**
     * @test
     */
    public function testReadCompanyFinanceYear()
    {
        $companyFinanceYear = $this->makeCompanyFinanceYear();
        $this->json('GET', '/api/v1/companyFinanceYears/'.$companyFinanceYear->id);

        $this->assertApiResponse($companyFinanceYear->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCompanyFinanceYear()
    {
        $companyFinanceYear = $this->makeCompanyFinanceYear();
        $editedCompanyFinanceYear = $this->fakeCompanyFinanceYearData();

        $this->json('PUT', '/api/v1/companyFinanceYears/'.$companyFinanceYear->id, $editedCompanyFinanceYear);

        $this->assertApiResponse($editedCompanyFinanceYear);
    }

    /**
     * @test
     */
    public function testDeleteCompanyFinanceYear()
    {
        $companyFinanceYear = $this->makeCompanyFinanceYear();
        $this->json('DELETE', '/api/v1/companyFinanceYears/'.$companyFinanceYear->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/companyFinanceYears/'.$companyFinanceYear->id);

        $this->assertResponseStatus(404);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyFinancePeriodApiTest extends TestCase
{
    use MakeCompanyFinancePeriodTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCompanyFinancePeriod()
    {
        $companyFinancePeriod = $this->fakeCompanyFinancePeriodData();
        $this->json('POST', '/api/v1/companyFinancePeriods', $companyFinancePeriod);

        $this->assertApiResponse($companyFinancePeriod);
    }

    /**
     * @test
     */
    public function testReadCompanyFinancePeriod()
    {
        $companyFinancePeriod = $this->makeCompanyFinancePeriod();
        $this->json('GET', '/api/v1/companyFinancePeriods/'.$companyFinancePeriod->id);

        $this->assertApiResponse($companyFinancePeriod->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCompanyFinancePeriod()
    {
        $companyFinancePeriod = $this->makeCompanyFinancePeriod();
        $editedCompanyFinancePeriod = $this->fakeCompanyFinancePeriodData();

        $this->json('PUT', '/api/v1/companyFinancePeriods/'.$companyFinancePeriod->id, $editedCompanyFinancePeriod);

        $this->assertApiResponse($editedCompanyFinancePeriod);
    }

    /**
     * @test
     */
    public function testDeleteCompanyFinancePeriod()
    {
        $companyFinancePeriod = $this->makeCompanyFinancePeriod();
        $this->json('DELETE', '/api/v1/companyFinancePeriods/'.$companyFinancePeriod->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/companyFinancePeriods/'.$companyFinancePeriod->id);

        $this->assertResponseStatus(404);
    }
}

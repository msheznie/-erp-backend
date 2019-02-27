<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyFinanceYearperiodMasterApiTest extends TestCase
{
    use MakeCompanyFinanceYearperiodMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCompanyFinanceYearperiodMaster()
    {
        $companyFinanceYearperiodMaster = $this->fakeCompanyFinanceYearperiodMasterData();
        $this->json('POST', '/api/v1/companyFinanceYearperiodMasters', $companyFinanceYearperiodMaster);

        $this->assertApiResponse($companyFinanceYearperiodMaster);
    }

    /**
     * @test
     */
    public function testReadCompanyFinanceYearperiodMaster()
    {
        $companyFinanceYearperiodMaster = $this->makeCompanyFinanceYearperiodMaster();
        $this->json('GET', '/api/v1/companyFinanceYearperiodMasters/'.$companyFinanceYearperiodMaster->id);

        $this->assertApiResponse($companyFinanceYearperiodMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCompanyFinanceYearperiodMaster()
    {
        $companyFinanceYearperiodMaster = $this->makeCompanyFinanceYearperiodMaster();
        $editedCompanyFinanceYearperiodMaster = $this->fakeCompanyFinanceYearperiodMasterData();

        $this->json('PUT', '/api/v1/companyFinanceYearperiodMasters/'.$companyFinanceYearperiodMaster->id, $editedCompanyFinanceYearperiodMaster);

        $this->assertApiResponse($editedCompanyFinanceYearperiodMaster);
    }

    /**
     * @test
     */
    public function testDeleteCompanyFinanceYearperiodMaster()
    {
        $companyFinanceYearperiodMaster = $this->makeCompanyFinanceYearperiodMaster();
        $this->json('DELETE', '/api/v1/companyFinanceYearperiodMasters/'.$companyFinanceYearperiodMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/companyFinanceYearperiodMasters/'.$companyFinanceYearperiodMaster->id);

        $this->assertResponseStatus(404);
    }
}

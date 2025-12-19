<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyPolicyMasterApiTest extends TestCase
{
    use MakeCompanyPolicyMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCompanyPolicyMaster()
    {
        $companyPolicyMaster = $this->fakeCompanyPolicyMasterData();
        $this->json('POST', '/api/v1/companyPolicyMasters', $companyPolicyMaster);

        $this->assertApiResponse($companyPolicyMaster);
    }

    /**
     * @test
     */
    public function testReadCompanyPolicyMaster()
    {
        $companyPolicyMaster = $this->makeCompanyPolicyMaster();
        $this->json('GET', '/api/v1/companyPolicyMasters/'.$companyPolicyMaster->id);

        $this->assertApiResponse($companyPolicyMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCompanyPolicyMaster()
    {
        $companyPolicyMaster = $this->makeCompanyPolicyMaster();
        $editedCompanyPolicyMaster = $this->fakeCompanyPolicyMasterData();

        $this->json('PUT', '/api/v1/companyPolicyMasters/'.$companyPolicyMaster->id, $editedCompanyPolicyMaster);

        $this->assertApiResponse($editedCompanyPolicyMaster);
    }

    /**
     * @test
     */
    public function testDeleteCompanyPolicyMaster()
    {
        $companyPolicyMaster = $this->makeCompanyPolicyMaster();
        $this->json('DELETE', '/api/v1/companyPolicyMasters/'.$companyPolicyMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/companyPolicyMasters/'.$companyPolicyMaster->id);

        $this->assertResponseStatus(404);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class InsurancePolicyTypeApiTest extends TestCase
{
    use MakeInsurancePolicyTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateInsurancePolicyType()
    {
        $insurancePolicyType = $this->fakeInsurancePolicyTypeData();
        $this->json('POST', '/api/v1/insurancePolicyTypes', $insurancePolicyType);

        $this->assertApiResponse($insurancePolicyType);
    }

    /**
     * @test
     */
    public function testReadInsurancePolicyType()
    {
        $insurancePolicyType = $this->makeInsurancePolicyType();
        $this->json('GET', '/api/v1/insurancePolicyTypes/'.$insurancePolicyType->id);

        $this->assertApiResponse($insurancePolicyType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateInsurancePolicyType()
    {
        $insurancePolicyType = $this->makeInsurancePolicyType();
        $editedInsurancePolicyType = $this->fakeInsurancePolicyTypeData();

        $this->json('PUT', '/api/v1/insurancePolicyTypes/'.$insurancePolicyType->id, $editedInsurancePolicyType);

        $this->assertApiResponse($editedInsurancePolicyType);
    }

    /**
     * @test
     */
    public function testDeleteInsurancePolicyType()
    {
        $insurancePolicyType = $this->makeInsurancePolicyType();
        $this->json('DELETE', '/api/v1/insurancePolicyTypes/'.$insurancePolicyType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/insurancePolicyTypes/'.$insurancePolicyType->id);

        $this->assertResponseStatus(404);
    }
}

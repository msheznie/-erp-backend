<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SalaryProcessEmploymentTypesApiTest extends TestCase
{
    use MakeSalaryProcessEmploymentTypesTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSalaryProcessEmploymentTypes()
    {
        $salaryProcessEmploymentTypes = $this->fakeSalaryProcessEmploymentTypesData();
        $this->json('POST', '/api/v1/salaryProcessEmploymentTypes', $salaryProcessEmploymentTypes);

        $this->assertApiResponse($salaryProcessEmploymentTypes);
    }

    /**
     * @test
     */
    public function testReadSalaryProcessEmploymentTypes()
    {
        $salaryProcessEmploymentTypes = $this->makeSalaryProcessEmploymentTypes();
        $this->json('GET', '/api/v1/salaryProcessEmploymentTypes/'.$salaryProcessEmploymentTypes->id);

        $this->assertApiResponse($salaryProcessEmploymentTypes->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSalaryProcessEmploymentTypes()
    {
        $salaryProcessEmploymentTypes = $this->makeSalaryProcessEmploymentTypes();
        $editedSalaryProcessEmploymentTypes = $this->fakeSalaryProcessEmploymentTypesData();

        $this->json('PUT', '/api/v1/salaryProcessEmploymentTypes/'.$salaryProcessEmploymentTypes->id, $editedSalaryProcessEmploymentTypes);

        $this->assertApiResponse($editedSalaryProcessEmploymentTypes);
    }

    /**
     * @test
     */
    public function testDeleteSalaryProcessEmploymentTypes()
    {
        $salaryProcessEmploymentTypes = $this->makeSalaryProcessEmploymentTypes();
        $this->json('DELETE', '/api/v1/salaryProcessEmploymentTypes/'.$salaryProcessEmploymentTypes->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/salaryProcessEmploymentTypes/'.$salaryProcessEmploymentTypes->id);

        $this->assertResponseStatus(404);
    }
}

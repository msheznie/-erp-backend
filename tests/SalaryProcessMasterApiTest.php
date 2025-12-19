<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SalaryProcessMasterApiTest extends TestCase
{
    use MakeSalaryProcessMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateSalaryProcessMaster()
    {
        $salaryProcessMaster = $this->fakeSalaryProcessMasterData();
        $this->json('POST', '/api/v1/salaryProcessMasters', $salaryProcessMaster);

        $this->assertApiResponse($salaryProcessMaster);
    }

    /**
     * @test
     */
    public function testReadSalaryProcessMaster()
    {
        $salaryProcessMaster = $this->makeSalaryProcessMaster();
        $this->json('GET', '/api/v1/salaryProcessMasters/'.$salaryProcessMaster->id);

        $this->assertApiResponse($salaryProcessMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateSalaryProcessMaster()
    {
        $salaryProcessMaster = $this->makeSalaryProcessMaster();
        $editedSalaryProcessMaster = $this->fakeSalaryProcessMasterData();

        $this->json('PUT', '/api/v1/salaryProcessMasters/'.$salaryProcessMaster->id, $editedSalaryProcessMaster);

        $this->assertApiResponse($editedSalaryProcessMaster);
    }

    /**
     * @test
     */
    public function testDeleteSalaryProcessMaster()
    {
        $salaryProcessMaster = $this->makeSalaryProcessMaster();
        $this->json('DELETE', '/api/v1/salaryProcessMasters/'.$salaryProcessMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/salaryProcessMasters/'.$salaryProcessMaster->id);

        $this->assertResponseStatus(404);
    }
}

<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DepartmentMasterApiTest extends TestCase
{
    use MakeDepartmentMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDepartmentMaster()
    {
        $departmentMaster = $this->fakeDepartmentMasterData();
        $this->json('POST', '/api/v1/departmentMasters', $departmentMaster);

        $this->assertApiResponse($departmentMaster);
    }

    /**
     * @test
     */
    public function testReadDepartmentMaster()
    {
        $departmentMaster = $this->makeDepartmentMaster();
        $this->json('GET', '/api/v1/departmentMasters/'.$departmentMaster->id);

        $this->assertApiResponse($departmentMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDepartmentMaster()
    {
        $departmentMaster = $this->makeDepartmentMaster();
        $editedDepartmentMaster = $this->fakeDepartmentMasterData();

        $this->json('PUT', '/api/v1/departmentMasters/'.$departmentMaster->id, $editedDepartmentMaster);

        $this->assertApiResponse($editedDepartmentMaster);
    }

    /**
     * @test
     */
    public function testDeleteDepartmentMaster()
    {
        $departmentMaster = $this->makeDepartmentMaster();
        $this->json('DELETE', '/api/v1/departmentMasters/'.$departmentMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/departmentMasters/'.$departmentMaster->id);

        $this->assertResponseStatus(404);
    }
}

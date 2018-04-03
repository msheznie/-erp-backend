<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmployeesDepartmentApiTest extends TestCase
{
    use MakeEmployeesDepartmentTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateEmployeesDepartment()
    {
        $employeesDepartment = $this->fakeEmployeesDepartmentData();
        $this->json('POST', '/api/v1/employeesDepartments', $employeesDepartment);

        $this->assertApiResponse($employeesDepartment);
    }

    /**
     * @test
     */
    public function testReadEmployeesDepartment()
    {
        $employeesDepartment = $this->makeEmployeesDepartment();
        $this->json('GET', '/api/v1/employeesDepartments/'.$employeesDepartment->id);

        $this->assertApiResponse($employeesDepartment->toArray());
    }

    /**
     * @test
     */
    public function testUpdateEmployeesDepartment()
    {
        $employeesDepartment = $this->makeEmployeesDepartment();
        $editedEmployeesDepartment = $this->fakeEmployeesDepartmentData();

        $this->json('PUT', '/api/v1/employeesDepartments/'.$employeesDepartment->id, $editedEmployeesDepartment);

        $this->assertApiResponse($editedEmployeesDepartment);
    }

    /**
     * @test
     */
    public function testDeleteEmployeesDepartment()
    {
        $employeesDepartment = $this->makeEmployeesDepartment();
        $this->json('DELETE', '/api/v1/employeesDepartments/'.$employeesDepartment->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/employeesDepartments/'.$employeesDepartment->id);

        $this->assertResponseStatus(404);
    }
}

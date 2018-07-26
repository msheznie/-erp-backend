<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmployeeDetailsApiTest extends TestCase
{
    use MakeEmployeeDetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateEmployeeDetails()
    {
        $employeeDetails = $this->fakeEmployeeDetailsData();
        $this->json('POST', '/api/v1/employeeDetails', $employeeDetails);

        $this->assertApiResponse($employeeDetails);
    }

    /**
     * @test
     */
    public function testReadEmployeeDetails()
    {
        $employeeDetails = $this->makeEmployeeDetails();
        $this->json('GET', '/api/v1/employeeDetails/'.$employeeDetails->id);

        $this->assertApiResponse($employeeDetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateEmployeeDetails()
    {
        $employeeDetails = $this->makeEmployeeDetails();
        $editedEmployeeDetails = $this->fakeEmployeeDetailsData();

        $this->json('PUT', '/api/v1/employeeDetails/'.$employeeDetails->id, $editedEmployeeDetails);

        $this->assertApiResponse($editedEmployeeDetails);
    }

    /**
     * @test
     */
    public function testDeleteEmployeeDetails()
    {
        $employeeDetails = $this->makeEmployeeDetails();
        $this->json('DELETE', '/api/v1/employeeDetails/'.$employeeDetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/employeeDetails/'.$employeeDetails->id);

        $this->assertResponseStatus(404);
    }
}

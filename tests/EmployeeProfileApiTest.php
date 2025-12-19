<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmployeeProfileApiTest extends TestCase
{
    use MakeEmployeeProfileTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateEmployeeProfile()
    {
        $employeeProfile = $this->fakeEmployeeProfileData();
        $this->json('POST', '/api/v1/employeeProfiles', $employeeProfile);

        $this->assertApiResponse($employeeProfile);
    }

    /**
     * @test
     */
    public function testReadEmployeeProfile()
    {
        $employeeProfile = $this->makeEmployeeProfile();
        $this->json('GET', '/api/v1/employeeProfiles/'.$employeeProfile->id);

        $this->assertApiResponse($employeeProfile->toArray());
    }

    /**
     * @test
     */
    public function testUpdateEmployeeProfile()
    {
        $employeeProfile = $this->makeEmployeeProfile();
        $editedEmployeeProfile = $this->fakeEmployeeProfileData();

        $this->json('PUT', '/api/v1/employeeProfiles/'.$employeeProfile->id, $editedEmployeeProfile);

        $this->assertApiResponse($editedEmployeeProfile);
    }

    /**
     * @test
     */
    public function testDeleteEmployeeProfile()
    {
        $employeeProfile = $this->makeEmployeeProfile();
        $this->json('DELETE', '/api/v1/employeeProfiles/'.$employeeProfile->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/employeeProfiles/'.$employeeProfile->id);

        $this->assertResponseStatus(404);
    }
}

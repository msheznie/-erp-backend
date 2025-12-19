<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmployeeNavigationApiTest extends TestCase
{
    use MakeEmployeeNavigationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateEmployeeNavigation()
    {
        $employeeNavigation = $this->fakeEmployeeNavigationData();
        $this->json('POST', '/api/v1/employeeNavigations', $employeeNavigation);

        $this->assertApiResponse($employeeNavigation);
    }

    /**
     * @test
     */
    public function testReadEmployeeNavigation()
    {
        $employeeNavigation = $this->makeEmployeeNavigation();
        $this->json('GET', '/api/v1/employeeNavigations/'.$employeeNavigation->id);

        $this->assertApiResponse($employeeNavigation->toArray());
    }

    /**
     * @test
     */
    public function testUpdateEmployeeNavigation()
    {
        $employeeNavigation = $this->makeEmployeeNavigation();
        $editedEmployeeNavigation = $this->fakeEmployeeNavigationData();

        $this->json('PUT', '/api/v1/employeeNavigations/'.$employeeNavigation->id, $editedEmployeeNavigation);

        $this->assertApiResponse($editedEmployeeNavigation);
    }

    /**
     * @test
     */
    public function testDeleteEmployeeNavigation()
    {
        $employeeNavigation = $this->makeEmployeeNavigation();
        $this->json('DELETE', '/api/v1/employeeNavigations/'.$employeeNavigation->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/employeeNavigations/'.$employeeNavigation->id);

        $this->assertResponseStatus(404);
    }
}

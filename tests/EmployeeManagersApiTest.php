<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeEmployeeManagersTrait;
use Tests\ApiTestTrait;

class EmployeeManagersApiTest extends TestCase
{
    use MakeEmployeeManagersTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_employee_managers()
    {
        $employeeManagers = $this->fakeEmployeeManagersData();
        $this->response = $this->json('POST', '/api/employeeManagers', $employeeManagers);

        $this->assertApiResponse($employeeManagers);
    }

    /**
     * @test
     */
    public function test_read_employee_managers()
    {
        $employeeManagers = $this->makeEmployeeManagers();
        $this->response = $this->json('GET', '/api/employeeManagers/'.$employeeManagers->id);

        $this->assertApiResponse($employeeManagers->toArray());
    }

    /**
     * @test
     */
    public function test_update_employee_managers()
    {
        $employeeManagers = $this->makeEmployeeManagers();
        $editedEmployeeManagers = $this->fakeEmployeeManagersData();

        $this->response = $this->json('PUT', '/api/employeeManagers/'.$employeeManagers->id, $editedEmployeeManagers);

        $this->assertApiResponse($editedEmployeeManagers);
    }

    /**
     * @test
     */
    public function test_delete_employee_managers()
    {
        $employeeManagers = $this->makeEmployeeManagers();
        $this->response = $this->json('DELETE', '/api/employeeManagers/'.$employeeManagers->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/employeeManagers/'.$employeeManagers->id);

        $this->response->assertStatus(404);
    }
}

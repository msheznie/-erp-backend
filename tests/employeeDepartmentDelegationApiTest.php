<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeemployeeDepartmentDelegationTrait;
use Tests\ApiTestTrait;

class employeeDepartmentDelegationApiTest extends TestCase
{
    use MakeemployeeDepartmentDelegationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_employee_department_delegation()
    {
        $employeeDepartmentDelegation = $this->fakeemployeeDepartmentDelegationData();
        $this->response = $this->json('POST', '/api/employeeDepartmentDelegations', $employeeDepartmentDelegation);

        $this->assertApiResponse($employeeDepartmentDelegation);
    }

    /**
     * @test
     */
    public function test_read_employee_department_delegation()
    {
        $employeeDepartmentDelegation = $this->makeemployeeDepartmentDelegation();
        $this->response = $this->json('GET', '/api/employeeDepartmentDelegations/'.$employeeDepartmentDelegation->id);

        $this->assertApiResponse($employeeDepartmentDelegation->toArray());
    }

    /**
     * @test
     */
    public function test_update_employee_department_delegation()
    {
        $employeeDepartmentDelegation = $this->makeemployeeDepartmentDelegation();
        $editedemployeeDepartmentDelegation = $this->fakeemployeeDepartmentDelegationData();

        $this->response = $this->json('PUT', '/api/employeeDepartmentDelegations/'.$employeeDepartmentDelegation->id, $editedemployeeDepartmentDelegation);

        $this->assertApiResponse($editedemployeeDepartmentDelegation);
    }

    /**
     * @test
     */
    public function test_delete_employee_department_delegation()
    {
        $employeeDepartmentDelegation = $this->makeemployeeDepartmentDelegation();
        $this->response = $this->json('DELETE', '/api/employeeDepartmentDelegations/'.$employeeDepartmentDelegation->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/employeeDepartmentDelegations/'.$employeeDepartmentDelegation->id);

        $this->response->assertStatus(404);
    }
}

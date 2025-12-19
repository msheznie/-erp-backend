<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DepartmentBudgetPlanning;

class DepartmentBudgetPlanningApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_department_budget_planning()
    {
        $departmentBudgetPlanning = factory(DepartmentBudgetPlanning::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/department_budget_plannings', $departmentBudgetPlanning
        );

        $this->assertApiResponse($departmentBudgetPlanning);
    }

    /**
     * @test
     */
    public function test_read_department_budget_planning()
    {
        $departmentBudgetPlanning = factory(DepartmentBudgetPlanning::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/department_budget_plannings/'.$departmentBudgetPlanning->id
        );

        $this->assertApiResponse($departmentBudgetPlanning->toArray());
    }

    /**
     * @test
     */
    public function test_update_department_budget_planning()
    {
        $departmentBudgetPlanning = factory(DepartmentBudgetPlanning::class)->create();
        $editedDepartmentBudgetPlanning = factory(DepartmentBudgetPlanning::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/department_budget_plannings/'.$departmentBudgetPlanning->id,
            $editedDepartmentBudgetPlanning
        );

        $this->assertApiResponse($editedDepartmentBudgetPlanning);
    }

    /**
     * @test
     */
    public function test_delete_department_budget_planning()
    {
        $departmentBudgetPlanning = factory(DepartmentBudgetPlanning::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/department_budget_plannings/'.$departmentBudgetPlanning->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/department_budget_plannings/'.$departmentBudgetPlanning->id
        );

        $this->response->assertStatus(404);
    }
}

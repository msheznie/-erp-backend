<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DepBudgetPlDetEmpColumn;

class DepBudgetPlDetEmpColumnApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_dep_budget_pl_det_emp_column()
    {
        $depBudgetPlDetEmpColumn = factory(DepBudgetPlDetEmpColumn::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/dep_budget_pl_det_emp_columns', $depBudgetPlDetEmpColumn
        );

        $this->assertApiResponse($depBudgetPlDetEmpColumn);
    }

    /**
     * @test
     */
    public function test_read_dep_budget_pl_det_emp_column()
    {
        $depBudgetPlDetEmpColumn = factory(DepBudgetPlDetEmpColumn::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/dep_budget_pl_det_emp_columns/'.$depBudgetPlDetEmpColumn->id
        );

        $this->assertApiResponse($depBudgetPlDetEmpColumn->toArray());
    }

    /**
     * @test
     */
    public function test_update_dep_budget_pl_det_emp_column()
    {
        $depBudgetPlDetEmpColumn = factory(DepBudgetPlDetEmpColumn::class)->create();
        $editedDepBudgetPlDetEmpColumn = factory(DepBudgetPlDetEmpColumn::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/dep_budget_pl_det_emp_columns/'.$depBudgetPlDetEmpColumn->id,
            $editedDepBudgetPlDetEmpColumn
        );

        $this->assertApiResponse($editedDepBudgetPlDetEmpColumn);
    }

    /**
     * @test
     */
    public function test_delete_dep_budget_pl_det_emp_column()
    {
        $depBudgetPlDetEmpColumn = factory(DepBudgetPlDetEmpColumn::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/dep_budget_pl_det_emp_columns/'.$depBudgetPlDetEmpColumn->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/dep_budget_pl_det_emp_columns/'.$depBudgetPlDetEmpColumn->id
        );

        $this->response->assertStatus(404);
    }
}

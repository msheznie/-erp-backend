<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\DepBudgetPlDetColumn;

class DepBudgetPlDetColumnApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_dep_budget_pl_det_column()
    {
        $depBudgetPlDetColumn = factory(DepBudgetPlDetColumn::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/dep_budget_pl_det_columns', $depBudgetPlDetColumn
        );

        $this->assertApiResponse($depBudgetPlDetColumn);
    }

    /**
     * @test
     */
    public function test_read_dep_budget_pl_det_column()
    {
        $depBudgetPlDetColumn = factory(DepBudgetPlDetColumn::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/dep_budget_pl_det_columns/'.$depBudgetPlDetColumn->id
        );

        $this->assertApiResponse($depBudgetPlDetColumn->toArray());
    }

    /**
     * @test
     */
    public function test_update_dep_budget_pl_det_column()
    {
        $depBudgetPlDetColumn = factory(DepBudgetPlDetColumn::class)->create();
        $editedDepBudgetPlDetColumn = factory(DepBudgetPlDetColumn::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/dep_budget_pl_det_columns/'.$depBudgetPlDetColumn->id,
            $editedDepBudgetPlDetColumn
        );

        $this->assertApiResponse($editedDepBudgetPlDetColumn);
    }

    /**
     * @test
     */
    public function test_delete_dep_budget_pl_det_column()
    {
        $depBudgetPlDetColumn = factory(DepBudgetPlDetColumn::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/dep_budget_pl_det_columns/'.$depBudgetPlDetColumn->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/dep_budget_pl_det_columns/'.$depBudgetPlDetColumn->id
        );

        $this->response->assertStatus(404);
    }
}

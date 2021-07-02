<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ContingencyBudgetPlan;

class ContingencyBudgetPlanApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_contingency_budget_plan()
    {
        $contingencyBudgetPlan = factory(ContingencyBudgetPlan::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/contingency_budget_plans', $contingencyBudgetPlan
        );

        $this->assertApiResponse($contingencyBudgetPlan);
    }

    /**
     * @test
     */
    public function test_read_contingency_budget_plan()
    {
        $contingencyBudgetPlan = factory(ContingencyBudgetPlan::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/contingency_budget_plans/'.$contingencyBudgetPlan->id
        );

        $this->assertApiResponse($contingencyBudgetPlan->toArray());
    }

    /**
     * @test
     */
    public function test_update_contingency_budget_plan()
    {
        $contingencyBudgetPlan = factory(ContingencyBudgetPlan::class)->create();
        $editedContingencyBudgetPlan = factory(ContingencyBudgetPlan::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/contingency_budget_plans/'.$contingencyBudgetPlan->id,
            $editedContingencyBudgetPlan
        );

        $this->assertApiResponse($editedContingencyBudgetPlan);
    }

    /**
     * @test
     */
    public function test_delete_contingency_budget_plan()
    {
        $contingencyBudgetPlan = factory(ContingencyBudgetPlan::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/contingency_budget_plans/'.$contingencyBudgetPlan->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/contingency_budget_plans/'.$contingencyBudgetPlan->id
        );

        $this->response->assertStatus(404);
    }
}

<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BudgetAdditionRefferedBack;

class BudgetAdditionRefferedBackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_budget_addition_reffered_back()
    {
        $budgetAdditionRefferedBack = factory(BudgetAdditionRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/budget_addition_reffered_backs', $budgetAdditionRefferedBack
        );

        $this->assertApiResponse($budgetAdditionRefferedBack);
    }

    /**
     * @test
     */
    public function test_read_budget_addition_reffered_back()
    {
        $budgetAdditionRefferedBack = factory(BudgetAdditionRefferedBack::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/budget_addition_reffered_backs/'.$budgetAdditionRefferedBack->id
        );

        $this->assertApiResponse($budgetAdditionRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function test_update_budget_addition_reffered_back()
    {
        $budgetAdditionRefferedBack = factory(BudgetAdditionRefferedBack::class)->create();
        $editedBudgetAdditionRefferedBack = factory(BudgetAdditionRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/budget_addition_reffered_backs/'.$budgetAdditionRefferedBack->id,
            $editedBudgetAdditionRefferedBack
        );

        $this->assertApiResponse($editedBudgetAdditionRefferedBack);
    }

    /**
     * @test
     */
    public function test_delete_budget_addition_reffered_back()
    {
        $budgetAdditionRefferedBack = factory(BudgetAdditionRefferedBack::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/budget_addition_reffered_backs/'.$budgetAdditionRefferedBack->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/budget_addition_reffered_backs/'.$budgetAdditionRefferedBack->id
        );

        $this->response->assertStatus(404);
    }
}

<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BudgetAdditionDetailRefferedBack;

class BudgetAdditionDetailRefferedBackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_budget_addition_detail_reffered_back()
    {
        $budgetAdditionDetailRefferedBack = factory(BudgetAdditionDetailRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/budget_addition_detail_reffered_backs', $budgetAdditionDetailRefferedBack
        );

        $this->assertApiResponse($budgetAdditionDetailRefferedBack);
    }

    /**
     * @test
     */
    public function test_read_budget_addition_detail_reffered_back()
    {
        $budgetAdditionDetailRefferedBack = factory(BudgetAdditionDetailRefferedBack::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/budget_addition_detail_reffered_backs/'.$budgetAdditionDetailRefferedBack->id
        );

        $this->assertApiResponse($budgetAdditionDetailRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function test_update_budget_addition_detail_reffered_back()
    {
        $budgetAdditionDetailRefferedBack = factory(BudgetAdditionDetailRefferedBack::class)->create();
        $editedBudgetAdditionDetailRefferedBack = factory(BudgetAdditionDetailRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/budget_addition_detail_reffered_backs/'.$budgetAdditionDetailRefferedBack->id,
            $editedBudgetAdditionDetailRefferedBack
        );

        $this->assertApiResponse($editedBudgetAdditionDetailRefferedBack);
    }

    /**
     * @test
     */
    public function test_delete_budget_addition_detail_reffered_back()
    {
        $budgetAdditionDetailRefferedBack = factory(BudgetAdditionDetailRefferedBack::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/budget_addition_detail_reffered_backs/'.$budgetAdditionDetailRefferedBack->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/budget_addition_detail_reffered_backs/'.$budgetAdditionDetailRefferedBack->id
        );

        $this->response->assertStatus(404);
    }
}

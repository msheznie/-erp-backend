<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BudgetDetailsRefferedHistory;

class BudgetDetailsRefferedHistoryApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_budget_details_reffered_history()
    {
        $budgetDetailsRefferedHistory = factory(BudgetDetailsRefferedHistory::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/budget_details_reffered_histories', $budgetDetailsRefferedHistory
        );

        $this->assertApiResponse($budgetDetailsRefferedHistory);
    }

    /**
     * @test
     */
    public function test_read_budget_details_reffered_history()
    {
        $budgetDetailsRefferedHistory = factory(BudgetDetailsRefferedHistory::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/budget_details_reffered_histories/'.$budgetDetailsRefferedHistory->id
        );

        $this->assertApiResponse($budgetDetailsRefferedHistory->toArray());
    }

    /**
     * @test
     */
    public function test_update_budget_details_reffered_history()
    {
        $budgetDetailsRefferedHistory = factory(BudgetDetailsRefferedHistory::class)->create();
        $editedBudgetDetailsRefferedHistory = factory(BudgetDetailsRefferedHistory::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/budget_details_reffered_histories/'.$budgetDetailsRefferedHistory->id,
            $editedBudgetDetailsRefferedHistory
        );

        $this->assertApiResponse($editedBudgetDetailsRefferedHistory);
    }

    /**
     * @test
     */
    public function test_delete_budget_details_reffered_history()
    {
        $budgetDetailsRefferedHistory = factory(BudgetDetailsRefferedHistory::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/budget_details_reffered_histories/'.$budgetDetailsRefferedHistory->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/budget_details_reffered_histories/'.$budgetDetailsRefferedHistory->id
        );

        $this->response->assertStatus(404);
    }
}

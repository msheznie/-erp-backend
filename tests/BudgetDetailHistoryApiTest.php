<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BudgetDetailHistory;

class BudgetDetailHistoryApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_budget_detail_history()
    {
        $budgetDetailHistory = factory(BudgetDetailHistory::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/budget_detail_histories', $budgetDetailHistory
        );

        $this->assertApiResponse($budgetDetailHistory);
    }

    /**
     * @test
     */
    public function test_read_budget_detail_history()
    {
        $budgetDetailHistory = factory(BudgetDetailHistory::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/budget_detail_histories/'.$budgetDetailHistory->id
        );

        $this->assertApiResponse($budgetDetailHistory->toArray());
    }

    /**
     * @test
     */
    public function test_update_budget_detail_history()
    {
        $budgetDetailHistory = factory(BudgetDetailHistory::class)->create();
        $editedBudgetDetailHistory = factory(BudgetDetailHistory::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/budget_detail_histories/'.$budgetDetailHistory->id,
            $editedBudgetDetailHistory
        );

        $this->assertApiResponse($editedBudgetDetailHistory);
    }

    /**
     * @test
     */
    public function test_delete_budget_detail_history()
    {
        $budgetDetailHistory = factory(BudgetDetailHistory::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/budget_detail_histories/'.$budgetDetailHistory->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/budget_detail_histories/'.$budgetDetailHistory->id
        );

        $this->response->assertStatus(404);
    }
}

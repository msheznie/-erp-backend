<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BudgetMasterRefferedHistory;

class BudgetMasterRefferedHistoryApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_budget_master_reffered_history()
    {
        $budgetMasterRefferedHistory = factory(BudgetMasterRefferedHistory::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/budget_master_reffered_histories', $budgetMasterRefferedHistory
        );

        $this->assertApiResponse($budgetMasterRefferedHistory);
    }

    /**
     * @test
     */
    public function test_read_budget_master_reffered_history()
    {
        $budgetMasterRefferedHistory = factory(BudgetMasterRefferedHistory::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/budget_master_reffered_histories/'.$budgetMasterRefferedHistory->id
        );

        $this->assertApiResponse($budgetMasterRefferedHistory->toArray());
    }

    /**
     * @test
     */
    public function test_update_budget_master_reffered_history()
    {
        $budgetMasterRefferedHistory = factory(BudgetMasterRefferedHistory::class)->create();
        $editedBudgetMasterRefferedHistory = factory(BudgetMasterRefferedHistory::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/budget_master_reffered_histories/'.$budgetMasterRefferedHistory->id,
            $editedBudgetMasterRefferedHistory
        );

        $this->assertApiResponse($editedBudgetMasterRefferedHistory);
    }

    /**
     * @test
     */
    public function test_delete_budget_master_reffered_history()
    {
        $budgetMasterRefferedHistory = factory(BudgetMasterRefferedHistory::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/budget_master_reffered_histories/'.$budgetMasterRefferedHistory->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/budget_master_reffered_histories/'.$budgetMasterRefferedHistory->id
        );

        $this->response->assertStatus(404);
    }
}

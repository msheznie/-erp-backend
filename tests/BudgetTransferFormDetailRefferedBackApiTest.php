<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BudgetTransferFormDetailRefferedBack;

class BudgetTransferFormDetailRefferedBackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_budget_transfer_form_detail_reffered_back()
    {
        $budgetTransferFormDetailRefferedBack = factory(BudgetTransferFormDetailRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/budget_transfer_form_detail_reffered_backs', $budgetTransferFormDetailRefferedBack
        );

        $this->assertApiResponse($budgetTransferFormDetailRefferedBack);
    }

    /**
     * @test
     */
    public function test_read_budget_transfer_form_detail_reffered_back()
    {
        $budgetTransferFormDetailRefferedBack = factory(BudgetTransferFormDetailRefferedBack::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/budget_transfer_form_detail_reffered_backs/'.$budgetTransferFormDetailRefferedBack->id
        );

        $this->assertApiResponse($budgetTransferFormDetailRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function test_update_budget_transfer_form_detail_reffered_back()
    {
        $budgetTransferFormDetailRefferedBack = factory(BudgetTransferFormDetailRefferedBack::class)->create();
        $editedBudgetTransferFormDetailRefferedBack = factory(BudgetTransferFormDetailRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/budget_transfer_form_detail_reffered_backs/'.$budgetTransferFormDetailRefferedBack->id,
            $editedBudgetTransferFormDetailRefferedBack
        );

        $this->assertApiResponse($editedBudgetTransferFormDetailRefferedBack);
    }

    /**
     * @test
     */
    public function test_delete_budget_transfer_form_detail_reffered_back()
    {
        $budgetTransferFormDetailRefferedBack = factory(BudgetTransferFormDetailRefferedBack::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/budget_transfer_form_detail_reffered_backs/'.$budgetTransferFormDetailRefferedBack->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/budget_transfer_form_detail_reffered_backs/'.$budgetTransferFormDetailRefferedBack->id
        );

        $this->response->assertStatus(404);
    }
}

<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BudgetTransferFormRefferedBack;

class BudgetTransferFormRefferedBackApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_budget_transfer_form_reffered_back()
    {
        $budgetTransferFormRefferedBack = factory(BudgetTransferFormRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/budget_transfer_form_reffered_backs', $budgetTransferFormRefferedBack
        );

        $this->assertApiResponse($budgetTransferFormRefferedBack);
    }

    /**
     * @test
     */
    public function test_read_budget_transfer_form_reffered_back()
    {
        $budgetTransferFormRefferedBack = factory(BudgetTransferFormRefferedBack::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/budget_transfer_form_reffered_backs/'.$budgetTransferFormRefferedBack->id
        );

        $this->assertApiResponse($budgetTransferFormRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function test_update_budget_transfer_form_reffered_back()
    {
        $budgetTransferFormRefferedBack = factory(BudgetTransferFormRefferedBack::class)->create();
        $editedBudgetTransferFormRefferedBack = factory(BudgetTransferFormRefferedBack::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/budget_transfer_form_reffered_backs/'.$budgetTransferFormRefferedBack->id,
            $editedBudgetTransferFormRefferedBack
        );

        $this->assertApiResponse($editedBudgetTransferFormRefferedBack);
    }

    /**
     * @test
     */
    public function test_delete_budget_transfer_form_reffered_back()
    {
        $budgetTransferFormRefferedBack = factory(BudgetTransferFormRefferedBack::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/budget_transfer_form_reffered_backs/'.$budgetTransferFormRefferedBack->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/budget_transfer_form_reffered_backs/'.$budgetTransferFormRefferedBack->id
        );

        $this->response->assertStatus(404);
    }
}

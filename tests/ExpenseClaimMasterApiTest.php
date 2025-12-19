<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ExpenseClaimMaster;

class ExpenseClaimMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_expense_claim_master()
    {
        $expenseClaimMaster = factory(ExpenseClaimMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/expense_claim_masters', $expenseClaimMaster
        );

        $this->assertApiResponse($expenseClaimMaster);
    }

    /**
     * @test
     */
    public function test_read_expense_claim_master()
    {
        $expenseClaimMaster = factory(ExpenseClaimMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/expense_claim_masters/'.$expenseClaimMaster->id
        );

        $this->assertApiResponse($expenseClaimMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_expense_claim_master()
    {
        $expenseClaimMaster = factory(ExpenseClaimMaster::class)->create();
        $editedExpenseClaimMaster = factory(ExpenseClaimMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/expense_claim_masters/'.$expenseClaimMaster->id,
            $editedExpenseClaimMaster
        );

        $this->assertApiResponse($editedExpenseClaimMaster);
    }

    /**
     * @test
     */
    public function test_delete_expense_claim_master()
    {
        $expenseClaimMaster = factory(ExpenseClaimMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/expense_claim_masters/'.$expenseClaimMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/expense_claim_masters/'.$expenseClaimMaster->id
        );

        $this->response->assertStatus(404);
    }
}

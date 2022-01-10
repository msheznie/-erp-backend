<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ExpenseClaimCategoriesMaster;

class ExpenseClaimCategoriesMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_expense_claim_categories_master()
    {
        $expenseClaimCategoriesMaster = factory(ExpenseClaimCategoriesMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/expense_claim_categories_masters', $expenseClaimCategoriesMaster
        );

        $this->assertApiResponse($expenseClaimCategoriesMaster);
    }

    /**
     * @test
     */
    public function test_read_expense_claim_categories_master()
    {
        $expenseClaimCategoriesMaster = factory(ExpenseClaimCategoriesMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/expense_claim_categories_masters/'.$expenseClaimCategoriesMaster->id
        );

        $this->assertApiResponse($expenseClaimCategoriesMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_expense_claim_categories_master()
    {
        $expenseClaimCategoriesMaster = factory(ExpenseClaimCategoriesMaster::class)->create();
        $editedExpenseClaimCategoriesMaster = factory(ExpenseClaimCategoriesMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/expense_claim_categories_masters/'.$expenseClaimCategoriesMaster->id,
            $editedExpenseClaimCategoriesMaster
        );

        $this->assertApiResponse($editedExpenseClaimCategoriesMaster);
    }

    /**
     * @test
     */
    public function test_delete_expense_claim_categories_master()
    {
        $expenseClaimCategoriesMaster = factory(ExpenseClaimCategoriesMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/expense_claim_categories_masters/'.$expenseClaimCategoriesMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/expense_claim_categories_masters/'.$expenseClaimCategoriesMaster->id
        );

        $this->response->assertStatus(404);
    }
}

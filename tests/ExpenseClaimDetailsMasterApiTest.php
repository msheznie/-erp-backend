<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ExpenseClaimDetailsMaster;

class ExpenseClaimDetailsMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_expense_claim_details_master()
    {
        $expenseClaimDetailsMaster = factory(ExpenseClaimDetailsMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/expense_claim_details_masters', $expenseClaimDetailsMaster
        );

        $this->assertApiResponse($expenseClaimDetailsMaster);
    }

    /**
     * @test
     */
    public function test_read_expense_claim_details_master()
    {
        $expenseClaimDetailsMaster = factory(ExpenseClaimDetailsMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/expense_claim_details_masters/'.$expenseClaimDetailsMaster->id
        );

        $this->assertApiResponse($expenseClaimDetailsMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_expense_claim_details_master()
    {
        $expenseClaimDetailsMaster = factory(ExpenseClaimDetailsMaster::class)->create();
        $editedExpenseClaimDetailsMaster = factory(ExpenseClaimDetailsMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/expense_claim_details_masters/'.$expenseClaimDetailsMaster->id,
            $editedExpenseClaimDetailsMaster
        );

        $this->assertApiResponse($editedExpenseClaimDetailsMaster);
    }

    /**
     * @test
     */
    public function test_delete_expense_claim_details_master()
    {
        $expenseClaimDetailsMaster = factory(ExpenseClaimDetailsMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/expense_claim_details_masters/'.$expenseClaimDetailsMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/expense_claim_details_masters/'.$expenseClaimDetailsMaster->id
        );

        $this->response->assertStatus(404);
    }
}

<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ExpenseAssetAllocation;

class ExpenseAssetAllocationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_expense_asset_allocation()
    {
        $expenseAssetAllocation = factory(ExpenseAssetAllocation::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/expense_asset_allocations', $expenseAssetAllocation
        );

        $this->assertApiResponse($expenseAssetAllocation);
    }

    /**
     * @test
     */
    public function test_read_expense_asset_allocation()
    {
        $expenseAssetAllocation = factory(ExpenseAssetAllocation::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/expense_asset_allocations/'.$expenseAssetAllocation->id
        );

        $this->assertApiResponse($expenseAssetAllocation->toArray());
    }

    /**
     * @test
     */
    public function test_update_expense_asset_allocation()
    {
        $expenseAssetAllocation = factory(ExpenseAssetAllocation::class)->create();
        $editedExpenseAssetAllocation = factory(ExpenseAssetAllocation::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/expense_asset_allocations/'.$expenseAssetAllocation->id,
            $editedExpenseAssetAllocation
        );

        $this->assertApiResponse($editedExpenseAssetAllocation);
    }

    /**
     * @test
     */
    public function test_delete_expense_asset_allocation()
    {
        $expenseAssetAllocation = factory(ExpenseAssetAllocation::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/expense_asset_allocations/'.$expenseAssetAllocation->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/expense_asset_allocations/'.$expenseAssetAllocation->id
        );

        $this->response->assertStatus(404);
    }
}

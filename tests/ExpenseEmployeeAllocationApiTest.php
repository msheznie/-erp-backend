<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ExpenseEmployeeAllocation;

class ExpenseEmployeeAllocationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_expense_employee_allocation()
    {
        $expenseEmployeeAllocation = factory(ExpenseEmployeeAllocation::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/expense_employee_allocations', $expenseEmployeeAllocation
        );

        $this->assertApiResponse($expenseEmployeeAllocation);
    }

    /**
     * @test
     */
    public function test_read_expense_employee_allocation()
    {
        $expenseEmployeeAllocation = factory(ExpenseEmployeeAllocation::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/expense_employee_allocations/'.$expenseEmployeeAllocation->id
        );

        $this->assertApiResponse($expenseEmployeeAllocation->toArray());
    }

    /**
     * @test
     */
    public function test_update_expense_employee_allocation()
    {
        $expenseEmployeeAllocation = factory(ExpenseEmployeeAllocation::class)->create();
        $editedExpenseEmployeeAllocation = factory(ExpenseEmployeeAllocation::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/expense_employee_allocations/'.$expenseEmployeeAllocation->id,
            $editedExpenseEmployeeAllocation
        );

        $this->assertApiResponse($editedExpenseEmployeeAllocation);
    }

    /**
     * @test
     */
    public function test_delete_expense_employee_allocation()
    {
        $expenseEmployeeAllocation = factory(ExpenseEmployeeAllocation::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/expense_employee_allocations/'.$expenseEmployeeAllocation->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/expense_employee_allocations/'.$expenseEmployeeAllocation->id
        );

        $this->response->assertStatus(404);
    }
}

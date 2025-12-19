<?php namespace Tests\Repositories;

use App\Models\ExpenseEmployeeAllocation;
use App\Repositories\ExpenseEmployeeAllocationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ExpenseEmployeeAllocationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ExpenseEmployeeAllocationRepository
     */
    protected $expenseEmployeeAllocationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->expenseEmployeeAllocationRepo = \App::make(ExpenseEmployeeAllocationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_expense_employee_allocation()
    {
        $expenseEmployeeAllocation = factory(ExpenseEmployeeAllocation::class)->make()->toArray();

        $createdExpenseEmployeeAllocation = $this->expenseEmployeeAllocationRepo->create($expenseEmployeeAllocation);

        $createdExpenseEmployeeAllocation = $createdExpenseEmployeeAllocation->toArray();
        $this->assertArrayHasKey('id', $createdExpenseEmployeeAllocation);
        $this->assertNotNull($createdExpenseEmployeeAllocation['id'], 'Created ExpenseEmployeeAllocation must have id specified');
        $this->assertNotNull(ExpenseEmployeeAllocation::find($createdExpenseEmployeeAllocation['id']), 'ExpenseEmployeeAllocation with given id must be in DB');
        $this->assertModelData($expenseEmployeeAllocation, $createdExpenseEmployeeAllocation);
    }

    /**
     * @test read
     */
    public function test_read_expense_employee_allocation()
    {
        $expenseEmployeeAllocation = factory(ExpenseEmployeeAllocation::class)->create();

        $dbExpenseEmployeeAllocation = $this->expenseEmployeeAllocationRepo->find($expenseEmployeeAllocation->id);

        $dbExpenseEmployeeAllocation = $dbExpenseEmployeeAllocation->toArray();
        $this->assertModelData($expenseEmployeeAllocation->toArray(), $dbExpenseEmployeeAllocation);
    }

    /**
     * @test update
     */
    public function test_update_expense_employee_allocation()
    {
        $expenseEmployeeAllocation = factory(ExpenseEmployeeAllocation::class)->create();
        $fakeExpenseEmployeeAllocation = factory(ExpenseEmployeeAllocation::class)->make()->toArray();

        $updatedExpenseEmployeeAllocation = $this->expenseEmployeeAllocationRepo->update($fakeExpenseEmployeeAllocation, $expenseEmployeeAllocation->id);

        $this->assertModelData($fakeExpenseEmployeeAllocation, $updatedExpenseEmployeeAllocation->toArray());
        $dbExpenseEmployeeAllocation = $this->expenseEmployeeAllocationRepo->find($expenseEmployeeAllocation->id);
        $this->assertModelData($fakeExpenseEmployeeAllocation, $dbExpenseEmployeeAllocation->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_expense_employee_allocation()
    {
        $expenseEmployeeAllocation = factory(ExpenseEmployeeAllocation::class)->create();

        $resp = $this->expenseEmployeeAllocationRepo->delete($expenseEmployeeAllocation->id);

        $this->assertTrue($resp);
        $this->assertNull(ExpenseEmployeeAllocation::find($expenseEmployeeAllocation->id), 'ExpenseEmployeeAllocation should not exist in DB');
    }
}

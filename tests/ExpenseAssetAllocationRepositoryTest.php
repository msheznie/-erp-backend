<?php namespace Tests\Repositories;

use App\Models\ExpenseAssetAllocation;
use App\Repositories\ExpenseAssetAllocationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ExpenseAssetAllocationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ExpenseAssetAllocationRepository
     */
    protected $expenseAssetAllocationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->expenseAssetAllocationRepo = \App::make(ExpenseAssetAllocationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_expense_asset_allocation()
    {
        $expenseAssetAllocation = factory(ExpenseAssetAllocation::class)->make()->toArray();

        $createdExpenseAssetAllocation = $this->expenseAssetAllocationRepo->create($expenseAssetAllocation);

        $createdExpenseAssetAllocation = $createdExpenseAssetAllocation->toArray();
        $this->assertArrayHasKey('id', $createdExpenseAssetAllocation);
        $this->assertNotNull($createdExpenseAssetAllocation['id'], 'Created ExpenseAssetAllocation must have id specified');
        $this->assertNotNull(ExpenseAssetAllocation::find($createdExpenseAssetAllocation['id']), 'ExpenseAssetAllocation with given id must be in DB');
        $this->assertModelData($expenseAssetAllocation, $createdExpenseAssetAllocation);
    }

    /**
     * @test read
     */
    public function test_read_expense_asset_allocation()
    {
        $expenseAssetAllocation = factory(ExpenseAssetAllocation::class)->create();

        $dbExpenseAssetAllocation = $this->expenseAssetAllocationRepo->find($expenseAssetAllocation->id);

        $dbExpenseAssetAllocation = $dbExpenseAssetAllocation->toArray();
        $this->assertModelData($expenseAssetAllocation->toArray(), $dbExpenseAssetAllocation);
    }

    /**
     * @test update
     */
    public function test_update_expense_asset_allocation()
    {
        $expenseAssetAllocation = factory(ExpenseAssetAllocation::class)->create();
        $fakeExpenseAssetAllocation = factory(ExpenseAssetAllocation::class)->make()->toArray();

        $updatedExpenseAssetAllocation = $this->expenseAssetAllocationRepo->update($fakeExpenseAssetAllocation, $expenseAssetAllocation->id);

        $this->assertModelData($fakeExpenseAssetAllocation, $updatedExpenseAssetAllocation->toArray());
        $dbExpenseAssetAllocation = $this->expenseAssetAllocationRepo->find($expenseAssetAllocation->id);
        $this->assertModelData($fakeExpenseAssetAllocation, $dbExpenseAssetAllocation->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_expense_asset_allocation()
    {
        $expenseAssetAllocation = factory(ExpenseAssetAllocation::class)->create();

        $resp = $this->expenseAssetAllocationRepo->delete($expenseAssetAllocation->id);

        $this->assertTrue($resp);
        $this->assertNull(ExpenseAssetAllocation::find($expenseAssetAllocation->id), 'ExpenseAssetAllocation should not exist in DB');
    }
}

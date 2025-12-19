<?php

use App\Models\ExpenseClaimCategories;
use App\Repositories\ExpenseClaimCategoriesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExpenseClaimCategoriesRepositoryTest extends TestCase
{
    use MakeExpenseClaimCategoriesTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ExpenseClaimCategoriesRepository
     */
    protected $expenseClaimCategoriesRepo;

    public function setUp()
    {
        parent::setUp();
        $this->expenseClaimCategoriesRepo = App::make(ExpenseClaimCategoriesRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateExpenseClaimCategories()
    {
        $expenseClaimCategories = $this->fakeExpenseClaimCategoriesData();
        $createdExpenseClaimCategories = $this->expenseClaimCategoriesRepo->create($expenseClaimCategories);
        $createdExpenseClaimCategories = $createdExpenseClaimCategories->toArray();
        $this->assertArrayHasKey('id', $createdExpenseClaimCategories);
        $this->assertNotNull($createdExpenseClaimCategories['id'], 'Created ExpenseClaimCategories must have id specified');
        $this->assertNotNull(ExpenseClaimCategories::find($createdExpenseClaimCategories['id']), 'ExpenseClaimCategories with given id must be in DB');
        $this->assertModelData($expenseClaimCategories, $createdExpenseClaimCategories);
    }

    /**
     * @test read
     */
    public function testReadExpenseClaimCategories()
    {
        $expenseClaimCategories = $this->makeExpenseClaimCategories();
        $dbExpenseClaimCategories = $this->expenseClaimCategoriesRepo->find($expenseClaimCategories->id);
        $dbExpenseClaimCategories = $dbExpenseClaimCategories->toArray();
        $this->assertModelData($expenseClaimCategories->toArray(), $dbExpenseClaimCategories);
    }

    /**
     * @test update
     */
    public function testUpdateExpenseClaimCategories()
    {
        $expenseClaimCategories = $this->makeExpenseClaimCategories();
        $fakeExpenseClaimCategories = $this->fakeExpenseClaimCategoriesData();
        $updatedExpenseClaimCategories = $this->expenseClaimCategoriesRepo->update($fakeExpenseClaimCategories, $expenseClaimCategories->id);
        $this->assertModelData($fakeExpenseClaimCategories, $updatedExpenseClaimCategories->toArray());
        $dbExpenseClaimCategories = $this->expenseClaimCategoriesRepo->find($expenseClaimCategories->id);
        $this->assertModelData($fakeExpenseClaimCategories, $dbExpenseClaimCategories->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteExpenseClaimCategories()
    {
        $expenseClaimCategories = $this->makeExpenseClaimCategories();
        $resp = $this->expenseClaimCategoriesRepo->delete($expenseClaimCategories->id);
        $this->assertTrue($resp);
        $this->assertNull(ExpenseClaimCategories::find($expenseClaimCategories->id), 'ExpenseClaimCategories should not exist in DB');
    }
}

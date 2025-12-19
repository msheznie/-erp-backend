<?php

use App\Models\ExpenseClaim;
use App\Repositories\ExpenseClaimRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExpenseClaimRepositoryTest extends TestCase
{
    use MakeExpenseClaimTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ExpenseClaimRepository
     */
    protected $expenseClaimRepo;

    public function setUp()
    {
        parent::setUp();
        $this->expenseClaimRepo = App::make(ExpenseClaimRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateExpenseClaim()
    {
        $expenseClaim = $this->fakeExpenseClaimData();
        $createdExpenseClaim = $this->expenseClaimRepo->create($expenseClaim);
        $createdExpenseClaim = $createdExpenseClaim->toArray();
        $this->assertArrayHasKey('id', $createdExpenseClaim);
        $this->assertNotNull($createdExpenseClaim['id'], 'Created ExpenseClaim must have id specified');
        $this->assertNotNull(ExpenseClaim::find($createdExpenseClaim['id']), 'ExpenseClaim with given id must be in DB');
        $this->assertModelData($expenseClaim, $createdExpenseClaim);
    }

    /**
     * @test read
     */
    public function testReadExpenseClaim()
    {
        $expenseClaim = $this->makeExpenseClaim();
        $dbExpenseClaim = $this->expenseClaimRepo->find($expenseClaim->id);
        $dbExpenseClaim = $dbExpenseClaim->toArray();
        $this->assertModelData($expenseClaim->toArray(), $dbExpenseClaim);
    }

    /**
     * @test update
     */
    public function testUpdateExpenseClaim()
    {
        $expenseClaim = $this->makeExpenseClaim();
        $fakeExpenseClaim = $this->fakeExpenseClaimData();
        $updatedExpenseClaim = $this->expenseClaimRepo->update($fakeExpenseClaim, $expenseClaim->id);
        $this->assertModelData($fakeExpenseClaim, $updatedExpenseClaim->toArray());
        $dbExpenseClaim = $this->expenseClaimRepo->find($expenseClaim->id);
        $this->assertModelData($fakeExpenseClaim, $dbExpenseClaim->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteExpenseClaim()
    {
        $expenseClaim = $this->makeExpenseClaim();
        $resp = $this->expenseClaimRepo->delete($expenseClaim->id);
        $this->assertTrue($resp);
        $this->assertNull(ExpenseClaim::find($expenseClaim->id), 'ExpenseClaim should not exist in DB');
    }
}

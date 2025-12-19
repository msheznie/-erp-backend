<?php

use App\Models\ExpenseClaimType;
use App\Repositories\ExpenseClaimTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExpenseClaimTypeRepositoryTest extends TestCase
{
    use MakeExpenseClaimTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ExpenseClaimTypeRepository
     */
    protected $expenseClaimTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->expenseClaimTypeRepo = App::make(ExpenseClaimTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateExpenseClaimType()
    {
        $expenseClaimType = $this->fakeExpenseClaimTypeData();
        $createdExpenseClaimType = $this->expenseClaimTypeRepo->create($expenseClaimType);
        $createdExpenseClaimType = $createdExpenseClaimType->toArray();
        $this->assertArrayHasKey('id', $createdExpenseClaimType);
        $this->assertNotNull($createdExpenseClaimType['id'], 'Created ExpenseClaimType must have id specified');
        $this->assertNotNull(ExpenseClaimType::find($createdExpenseClaimType['id']), 'ExpenseClaimType with given id must be in DB');
        $this->assertModelData($expenseClaimType, $createdExpenseClaimType);
    }

    /**
     * @test read
     */
    public function testReadExpenseClaimType()
    {
        $expenseClaimType = $this->makeExpenseClaimType();
        $dbExpenseClaimType = $this->expenseClaimTypeRepo->find($expenseClaimType->id);
        $dbExpenseClaimType = $dbExpenseClaimType->toArray();
        $this->assertModelData($expenseClaimType->toArray(), $dbExpenseClaimType);
    }

    /**
     * @test update
     */
    public function testUpdateExpenseClaimType()
    {
        $expenseClaimType = $this->makeExpenseClaimType();
        $fakeExpenseClaimType = $this->fakeExpenseClaimTypeData();
        $updatedExpenseClaimType = $this->expenseClaimTypeRepo->update($fakeExpenseClaimType, $expenseClaimType->id);
        $this->assertModelData($fakeExpenseClaimType, $updatedExpenseClaimType->toArray());
        $dbExpenseClaimType = $this->expenseClaimTypeRepo->find($expenseClaimType->id);
        $this->assertModelData($fakeExpenseClaimType, $dbExpenseClaimType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteExpenseClaimType()
    {
        $expenseClaimType = $this->makeExpenseClaimType();
        $resp = $this->expenseClaimTypeRepo->delete($expenseClaimType->id);
        $this->assertTrue($resp);
        $this->assertNull(ExpenseClaimType::find($expenseClaimType->id), 'ExpenseClaimType should not exist in DB');
    }
}

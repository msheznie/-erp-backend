<?php namespace Tests\Repositories;

use App\Models\ExpenseClaimMaster;
use App\Repositories\ExpenseClaimMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ExpenseClaimMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ExpenseClaimMasterRepository
     */
    protected $expenseClaimMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->expenseClaimMasterRepo = \App::make(ExpenseClaimMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_expense_claim_master()
    {
        $expenseClaimMaster = factory(ExpenseClaimMaster::class)->make()->toArray();

        $createdExpenseClaimMaster = $this->expenseClaimMasterRepo->create($expenseClaimMaster);

        $createdExpenseClaimMaster = $createdExpenseClaimMaster->toArray();
        $this->assertArrayHasKey('id', $createdExpenseClaimMaster);
        $this->assertNotNull($createdExpenseClaimMaster['id'], 'Created ExpenseClaimMaster must have id specified');
        $this->assertNotNull(ExpenseClaimMaster::find($createdExpenseClaimMaster['id']), 'ExpenseClaimMaster with given id must be in DB');
        $this->assertModelData($expenseClaimMaster, $createdExpenseClaimMaster);
    }

    /**
     * @test read
     */
    public function test_read_expense_claim_master()
    {
        $expenseClaimMaster = factory(ExpenseClaimMaster::class)->create();

        $dbExpenseClaimMaster = $this->expenseClaimMasterRepo->find($expenseClaimMaster->id);

        $dbExpenseClaimMaster = $dbExpenseClaimMaster->toArray();
        $this->assertModelData($expenseClaimMaster->toArray(), $dbExpenseClaimMaster);
    }

    /**
     * @test update
     */
    public function test_update_expense_claim_master()
    {
        $expenseClaimMaster = factory(ExpenseClaimMaster::class)->create();
        $fakeExpenseClaimMaster = factory(ExpenseClaimMaster::class)->make()->toArray();

        $updatedExpenseClaimMaster = $this->expenseClaimMasterRepo->update($fakeExpenseClaimMaster, $expenseClaimMaster->id);

        $this->assertModelData($fakeExpenseClaimMaster, $updatedExpenseClaimMaster->toArray());
        $dbExpenseClaimMaster = $this->expenseClaimMasterRepo->find($expenseClaimMaster->id);
        $this->assertModelData($fakeExpenseClaimMaster, $dbExpenseClaimMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_expense_claim_master()
    {
        $expenseClaimMaster = factory(ExpenseClaimMaster::class)->create();

        $resp = $this->expenseClaimMasterRepo->delete($expenseClaimMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(ExpenseClaimMaster::find($expenseClaimMaster->id), 'ExpenseClaimMaster should not exist in DB');
    }
}

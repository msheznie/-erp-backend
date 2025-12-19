<?php namespace Tests\Repositories;

use App\Models\ExpenseClaimCategoriesMaster;
use App\Repositories\ExpenseClaimCategoriesMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ExpenseClaimCategoriesMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ExpenseClaimCategoriesMasterRepository
     */
    protected $expenseClaimCategoriesMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->expenseClaimCategoriesMasterRepo = \App::make(ExpenseClaimCategoriesMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_expense_claim_categories_master()
    {
        $expenseClaimCategoriesMaster = factory(ExpenseClaimCategoriesMaster::class)->make()->toArray();

        $createdExpenseClaimCategoriesMaster = $this->expenseClaimCategoriesMasterRepo->create($expenseClaimCategoriesMaster);

        $createdExpenseClaimCategoriesMaster = $createdExpenseClaimCategoriesMaster->toArray();
        $this->assertArrayHasKey('id', $createdExpenseClaimCategoriesMaster);
        $this->assertNotNull($createdExpenseClaimCategoriesMaster['id'], 'Created ExpenseClaimCategoriesMaster must have id specified');
        $this->assertNotNull(ExpenseClaimCategoriesMaster::find($createdExpenseClaimCategoriesMaster['id']), 'ExpenseClaimCategoriesMaster with given id must be in DB');
        $this->assertModelData($expenseClaimCategoriesMaster, $createdExpenseClaimCategoriesMaster);
    }

    /**
     * @test read
     */
    public function test_read_expense_claim_categories_master()
    {
        $expenseClaimCategoriesMaster = factory(ExpenseClaimCategoriesMaster::class)->create();

        $dbExpenseClaimCategoriesMaster = $this->expenseClaimCategoriesMasterRepo->find($expenseClaimCategoriesMaster->id);

        $dbExpenseClaimCategoriesMaster = $dbExpenseClaimCategoriesMaster->toArray();
        $this->assertModelData($expenseClaimCategoriesMaster->toArray(), $dbExpenseClaimCategoriesMaster);
    }

    /**
     * @test update
     */
    public function test_update_expense_claim_categories_master()
    {
        $expenseClaimCategoriesMaster = factory(ExpenseClaimCategoriesMaster::class)->create();
        $fakeExpenseClaimCategoriesMaster = factory(ExpenseClaimCategoriesMaster::class)->make()->toArray();

        $updatedExpenseClaimCategoriesMaster = $this->expenseClaimCategoriesMasterRepo->update($fakeExpenseClaimCategoriesMaster, $expenseClaimCategoriesMaster->id);

        $this->assertModelData($fakeExpenseClaimCategoriesMaster, $updatedExpenseClaimCategoriesMaster->toArray());
        $dbExpenseClaimCategoriesMaster = $this->expenseClaimCategoriesMasterRepo->find($expenseClaimCategoriesMaster->id);
        $this->assertModelData($fakeExpenseClaimCategoriesMaster, $dbExpenseClaimCategoriesMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_expense_claim_categories_master()
    {
        $expenseClaimCategoriesMaster = factory(ExpenseClaimCategoriesMaster::class)->create();

        $resp = $this->expenseClaimCategoriesMasterRepo->delete($expenseClaimCategoriesMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(ExpenseClaimCategoriesMaster::find($expenseClaimCategoriesMaster->id), 'ExpenseClaimCategoriesMaster should not exist in DB');
    }
}

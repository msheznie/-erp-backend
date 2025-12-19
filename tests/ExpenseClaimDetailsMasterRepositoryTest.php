<?php namespace Tests\Repositories;

use App\Models\ExpenseClaimDetailsMaster;
use App\Repositories\ExpenseClaimDetailsMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ExpenseClaimDetailsMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ExpenseClaimDetailsMasterRepository
     */
    protected $expenseClaimDetailsMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->expenseClaimDetailsMasterRepo = \App::make(ExpenseClaimDetailsMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_expense_claim_details_master()
    {
        $expenseClaimDetailsMaster = factory(ExpenseClaimDetailsMaster::class)->make()->toArray();

        $createdExpenseClaimDetailsMaster = $this->expenseClaimDetailsMasterRepo->create($expenseClaimDetailsMaster);

        $createdExpenseClaimDetailsMaster = $createdExpenseClaimDetailsMaster->toArray();
        $this->assertArrayHasKey('id', $createdExpenseClaimDetailsMaster);
        $this->assertNotNull($createdExpenseClaimDetailsMaster['id'], 'Created ExpenseClaimDetailsMaster must have id specified');
        $this->assertNotNull(ExpenseClaimDetailsMaster::find($createdExpenseClaimDetailsMaster['id']), 'ExpenseClaimDetailsMaster with given id must be in DB');
        $this->assertModelData($expenseClaimDetailsMaster, $createdExpenseClaimDetailsMaster);
    }

    /**
     * @test read
     */
    public function test_read_expense_claim_details_master()
    {
        $expenseClaimDetailsMaster = factory(ExpenseClaimDetailsMaster::class)->create();

        $dbExpenseClaimDetailsMaster = $this->expenseClaimDetailsMasterRepo->find($expenseClaimDetailsMaster->id);

        $dbExpenseClaimDetailsMaster = $dbExpenseClaimDetailsMaster->toArray();
        $this->assertModelData($expenseClaimDetailsMaster->toArray(), $dbExpenseClaimDetailsMaster);
    }

    /**
     * @test update
     */
    public function test_update_expense_claim_details_master()
    {
        $expenseClaimDetailsMaster = factory(ExpenseClaimDetailsMaster::class)->create();
        $fakeExpenseClaimDetailsMaster = factory(ExpenseClaimDetailsMaster::class)->make()->toArray();

        $updatedExpenseClaimDetailsMaster = $this->expenseClaimDetailsMasterRepo->update($fakeExpenseClaimDetailsMaster, $expenseClaimDetailsMaster->id);

        $this->assertModelData($fakeExpenseClaimDetailsMaster, $updatedExpenseClaimDetailsMaster->toArray());
        $dbExpenseClaimDetailsMaster = $this->expenseClaimDetailsMasterRepo->find($expenseClaimDetailsMaster->id);
        $this->assertModelData($fakeExpenseClaimDetailsMaster, $dbExpenseClaimDetailsMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_expense_claim_details_master()
    {
        $expenseClaimDetailsMaster = factory(ExpenseClaimDetailsMaster::class)->create();

        $resp = $this->expenseClaimDetailsMasterRepo->delete($expenseClaimDetailsMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(ExpenseClaimDetailsMaster::find($expenseClaimDetailsMaster->id), 'ExpenseClaimDetailsMaster should not exist in DB');
    }
}

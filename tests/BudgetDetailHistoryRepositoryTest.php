<?php namespace Tests\Repositories;

use App\Models\BudgetDetailHistory;
use App\Repositories\BudgetDetailHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BudgetDetailHistoryRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetDetailHistoryRepository
     */
    protected $budgetDetailHistoryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->budgetDetailHistoryRepo = \App::make(BudgetDetailHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_budget_detail_history()
    {
        $budgetDetailHistory = factory(BudgetDetailHistory::class)->make()->toArray();

        $createdBudgetDetailHistory = $this->budgetDetailHistoryRepo->create($budgetDetailHistory);

        $createdBudgetDetailHistory = $createdBudgetDetailHistory->toArray();
        $this->assertArrayHasKey('id', $createdBudgetDetailHistory);
        $this->assertNotNull($createdBudgetDetailHistory['id'], 'Created BudgetDetailHistory must have id specified');
        $this->assertNotNull(BudgetDetailHistory::find($createdBudgetDetailHistory['id']), 'BudgetDetailHistory with given id must be in DB');
        $this->assertModelData($budgetDetailHistory, $createdBudgetDetailHistory);
    }

    /**
     * @test read
     */
    public function test_read_budget_detail_history()
    {
        $budgetDetailHistory = factory(BudgetDetailHistory::class)->create();

        $dbBudgetDetailHistory = $this->budgetDetailHistoryRepo->find($budgetDetailHistory->id);

        $dbBudgetDetailHistory = $dbBudgetDetailHistory->toArray();
        $this->assertModelData($budgetDetailHistory->toArray(), $dbBudgetDetailHistory);
    }

    /**
     * @test update
     */
    public function test_update_budget_detail_history()
    {
        $budgetDetailHistory = factory(BudgetDetailHistory::class)->create();
        $fakeBudgetDetailHistory = factory(BudgetDetailHistory::class)->make()->toArray();

        $updatedBudgetDetailHistory = $this->budgetDetailHistoryRepo->update($fakeBudgetDetailHistory, $budgetDetailHistory->id);

        $this->assertModelData($fakeBudgetDetailHistory, $updatedBudgetDetailHistory->toArray());
        $dbBudgetDetailHistory = $this->budgetDetailHistoryRepo->find($budgetDetailHistory->id);
        $this->assertModelData($fakeBudgetDetailHistory, $dbBudgetDetailHistory->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_budget_detail_history()
    {
        $budgetDetailHistory = factory(BudgetDetailHistory::class)->create();

        $resp = $this->budgetDetailHistoryRepo->delete($budgetDetailHistory->id);

        $this->assertTrue($resp);
        $this->assertNull(BudgetDetailHistory::find($budgetDetailHistory->id), 'BudgetDetailHistory should not exist in DB');
    }
}

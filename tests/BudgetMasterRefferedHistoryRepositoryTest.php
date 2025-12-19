<?php namespace Tests\Repositories;

use App\Models\BudgetMasterRefferedHistory;
use App\Repositories\BudgetMasterRefferedHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BudgetMasterRefferedHistoryRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetMasterRefferedHistoryRepository
     */
    protected $budgetMasterRefferedHistoryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->budgetMasterRefferedHistoryRepo = \App::make(BudgetMasterRefferedHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_budget_master_reffered_history()
    {
        $budgetMasterRefferedHistory = factory(BudgetMasterRefferedHistory::class)->make()->toArray();

        $createdBudgetMasterRefferedHistory = $this->budgetMasterRefferedHistoryRepo->create($budgetMasterRefferedHistory);

        $createdBudgetMasterRefferedHistory = $createdBudgetMasterRefferedHistory->toArray();
        $this->assertArrayHasKey('id', $createdBudgetMasterRefferedHistory);
        $this->assertNotNull($createdBudgetMasterRefferedHistory['id'], 'Created BudgetMasterRefferedHistory must have id specified');
        $this->assertNotNull(BudgetMasterRefferedHistory::find($createdBudgetMasterRefferedHistory['id']), 'BudgetMasterRefferedHistory with given id must be in DB');
        $this->assertModelData($budgetMasterRefferedHistory, $createdBudgetMasterRefferedHistory);
    }

    /**
     * @test read
     */
    public function test_read_budget_master_reffered_history()
    {
        $budgetMasterRefferedHistory = factory(BudgetMasterRefferedHistory::class)->create();

        $dbBudgetMasterRefferedHistory = $this->budgetMasterRefferedHistoryRepo->find($budgetMasterRefferedHistory->id);

        $dbBudgetMasterRefferedHistory = $dbBudgetMasterRefferedHistory->toArray();
        $this->assertModelData($budgetMasterRefferedHistory->toArray(), $dbBudgetMasterRefferedHistory);
    }

    /**
     * @test update
     */
    public function test_update_budget_master_reffered_history()
    {
        $budgetMasterRefferedHistory = factory(BudgetMasterRefferedHistory::class)->create();
        $fakeBudgetMasterRefferedHistory = factory(BudgetMasterRefferedHistory::class)->make()->toArray();

        $updatedBudgetMasterRefferedHistory = $this->budgetMasterRefferedHistoryRepo->update($fakeBudgetMasterRefferedHistory, $budgetMasterRefferedHistory->id);

        $this->assertModelData($fakeBudgetMasterRefferedHistory, $updatedBudgetMasterRefferedHistory->toArray());
        $dbBudgetMasterRefferedHistory = $this->budgetMasterRefferedHistoryRepo->find($budgetMasterRefferedHistory->id);
        $this->assertModelData($fakeBudgetMasterRefferedHistory, $dbBudgetMasterRefferedHistory->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_budget_master_reffered_history()
    {
        $budgetMasterRefferedHistory = factory(BudgetMasterRefferedHistory::class)->create();

        $resp = $this->budgetMasterRefferedHistoryRepo->delete($budgetMasterRefferedHistory->id);

        $this->assertTrue($resp);
        $this->assertNull(BudgetMasterRefferedHistory::find($budgetMasterRefferedHistory->id), 'BudgetMasterRefferedHistory should not exist in DB');
    }
}

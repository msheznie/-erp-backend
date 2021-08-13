<?php namespace Tests\Repositories;

use App\Models\BudgetDetailsRefferedHistory;
use App\Repositories\BudgetDetailsRefferedHistoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BudgetDetailsRefferedHistoryRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetDetailsRefferedHistoryRepository
     */
    protected $budgetDetailsRefferedHistoryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->budgetDetailsRefferedHistoryRepo = \App::make(BudgetDetailsRefferedHistoryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_budget_details_reffered_history()
    {
        $budgetDetailsRefferedHistory = factory(BudgetDetailsRefferedHistory::class)->make()->toArray();

        $createdBudgetDetailsRefferedHistory = $this->budgetDetailsRefferedHistoryRepo->create($budgetDetailsRefferedHistory);

        $createdBudgetDetailsRefferedHistory = $createdBudgetDetailsRefferedHistory->toArray();
        $this->assertArrayHasKey('id', $createdBudgetDetailsRefferedHistory);
        $this->assertNotNull($createdBudgetDetailsRefferedHistory['id'], 'Created BudgetDetailsRefferedHistory must have id specified');
        $this->assertNotNull(BudgetDetailsRefferedHistory::find($createdBudgetDetailsRefferedHistory['id']), 'BudgetDetailsRefferedHistory with given id must be in DB');
        $this->assertModelData($budgetDetailsRefferedHistory, $createdBudgetDetailsRefferedHistory);
    }

    /**
     * @test read
     */
    public function test_read_budget_details_reffered_history()
    {
        $budgetDetailsRefferedHistory = factory(BudgetDetailsRefferedHistory::class)->create();

        $dbBudgetDetailsRefferedHistory = $this->budgetDetailsRefferedHistoryRepo->find($budgetDetailsRefferedHistory->id);

        $dbBudgetDetailsRefferedHistory = $dbBudgetDetailsRefferedHistory->toArray();
        $this->assertModelData($budgetDetailsRefferedHistory->toArray(), $dbBudgetDetailsRefferedHistory);
    }

    /**
     * @test update
     */
    public function test_update_budget_details_reffered_history()
    {
        $budgetDetailsRefferedHistory = factory(BudgetDetailsRefferedHistory::class)->create();
        $fakeBudgetDetailsRefferedHistory = factory(BudgetDetailsRefferedHistory::class)->make()->toArray();

        $updatedBudgetDetailsRefferedHistory = $this->budgetDetailsRefferedHistoryRepo->update($fakeBudgetDetailsRefferedHistory, $budgetDetailsRefferedHistory->id);

        $this->assertModelData($fakeBudgetDetailsRefferedHistory, $updatedBudgetDetailsRefferedHistory->toArray());
        $dbBudgetDetailsRefferedHistory = $this->budgetDetailsRefferedHistoryRepo->find($budgetDetailsRefferedHistory->id);
        $this->assertModelData($fakeBudgetDetailsRefferedHistory, $dbBudgetDetailsRefferedHistory->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_budget_details_reffered_history()
    {
        $budgetDetailsRefferedHistory = factory(BudgetDetailsRefferedHistory::class)->create();

        $resp = $this->budgetDetailsRefferedHistoryRepo->delete($budgetDetailsRefferedHistory->id);

        $this->assertTrue($resp);
        $this->assertNull(BudgetDetailsRefferedHistory::find($budgetDetailsRefferedHistory->id), 'BudgetDetailsRefferedHistory should not exist in DB');
    }
}

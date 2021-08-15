<?php namespace Tests\Repositories;

use App\Models\BudgetAdditionDetailRefferedBack;
use App\Repositories\BudgetAdditionDetailRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BudgetAdditionDetailRefferedBackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetAdditionDetailRefferedBackRepository
     */
    protected $budgetAdditionDetailRefferedBackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->budgetAdditionDetailRefferedBackRepo = \App::make(BudgetAdditionDetailRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_budget_addition_detail_reffered_back()
    {
        $budgetAdditionDetailRefferedBack = factory(BudgetAdditionDetailRefferedBack::class)->make()->toArray();

        $createdBudgetAdditionDetailRefferedBack = $this->budgetAdditionDetailRefferedBackRepo->create($budgetAdditionDetailRefferedBack);

        $createdBudgetAdditionDetailRefferedBack = $createdBudgetAdditionDetailRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdBudgetAdditionDetailRefferedBack);
        $this->assertNotNull($createdBudgetAdditionDetailRefferedBack['id'], 'Created BudgetAdditionDetailRefferedBack must have id specified');
        $this->assertNotNull(BudgetAdditionDetailRefferedBack::find($createdBudgetAdditionDetailRefferedBack['id']), 'BudgetAdditionDetailRefferedBack with given id must be in DB');
        $this->assertModelData($budgetAdditionDetailRefferedBack, $createdBudgetAdditionDetailRefferedBack);
    }

    /**
     * @test read
     */
    public function test_read_budget_addition_detail_reffered_back()
    {
        $budgetAdditionDetailRefferedBack = factory(BudgetAdditionDetailRefferedBack::class)->create();

        $dbBudgetAdditionDetailRefferedBack = $this->budgetAdditionDetailRefferedBackRepo->find($budgetAdditionDetailRefferedBack->id);

        $dbBudgetAdditionDetailRefferedBack = $dbBudgetAdditionDetailRefferedBack->toArray();
        $this->assertModelData($budgetAdditionDetailRefferedBack->toArray(), $dbBudgetAdditionDetailRefferedBack);
    }

    /**
     * @test update
     */
    public function test_update_budget_addition_detail_reffered_back()
    {
        $budgetAdditionDetailRefferedBack = factory(BudgetAdditionDetailRefferedBack::class)->create();
        $fakeBudgetAdditionDetailRefferedBack = factory(BudgetAdditionDetailRefferedBack::class)->make()->toArray();

        $updatedBudgetAdditionDetailRefferedBack = $this->budgetAdditionDetailRefferedBackRepo->update($fakeBudgetAdditionDetailRefferedBack, $budgetAdditionDetailRefferedBack->id);

        $this->assertModelData($fakeBudgetAdditionDetailRefferedBack, $updatedBudgetAdditionDetailRefferedBack->toArray());
        $dbBudgetAdditionDetailRefferedBack = $this->budgetAdditionDetailRefferedBackRepo->find($budgetAdditionDetailRefferedBack->id);
        $this->assertModelData($fakeBudgetAdditionDetailRefferedBack, $dbBudgetAdditionDetailRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_budget_addition_detail_reffered_back()
    {
        $budgetAdditionDetailRefferedBack = factory(BudgetAdditionDetailRefferedBack::class)->create();

        $resp = $this->budgetAdditionDetailRefferedBackRepo->delete($budgetAdditionDetailRefferedBack->id);

        $this->assertTrue($resp);
        $this->assertNull(BudgetAdditionDetailRefferedBack::find($budgetAdditionDetailRefferedBack->id), 'BudgetAdditionDetailRefferedBack should not exist in DB');
    }
}

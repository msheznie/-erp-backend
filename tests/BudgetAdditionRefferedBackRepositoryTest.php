<?php namespace Tests\Repositories;

use App\Models\BudgetAdditionRefferedBack;
use App\Repositories\BudgetAdditionRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BudgetAdditionRefferedBackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetAdditionRefferedBackRepository
     */
    protected $budgetAdditionRefferedBackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->budgetAdditionRefferedBackRepo = \App::make(BudgetAdditionRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_budget_addition_reffered_back()
    {
        $budgetAdditionRefferedBack = factory(BudgetAdditionRefferedBack::class)->make()->toArray();

        $createdBudgetAdditionRefferedBack = $this->budgetAdditionRefferedBackRepo->create($budgetAdditionRefferedBack);

        $createdBudgetAdditionRefferedBack = $createdBudgetAdditionRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdBudgetAdditionRefferedBack);
        $this->assertNotNull($createdBudgetAdditionRefferedBack['id'], 'Created BudgetAdditionRefferedBack must have id specified');
        $this->assertNotNull(BudgetAdditionRefferedBack::find($createdBudgetAdditionRefferedBack['id']), 'BudgetAdditionRefferedBack with given id must be in DB');
        $this->assertModelData($budgetAdditionRefferedBack, $createdBudgetAdditionRefferedBack);
    }

    /**
     * @test read
     */
    public function test_read_budget_addition_reffered_back()
    {
        $budgetAdditionRefferedBack = factory(BudgetAdditionRefferedBack::class)->create();

        $dbBudgetAdditionRefferedBack = $this->budgetAdditionRefferedBackRepo->find($budgetAdditionRefferedBack->id);

        $dbBudgetAdditionRefferedBack = $dbBudgetAdditionRefferedBack->toArray();
        $this->assertModelData($budgetAdditionRefferedBack->toArray(), $dbBudgetAdditionRefferedBack);
    }

    /**
     * @test update
     */
    public function test_update_budget_addition_reffered_back()
    {
        $budgetAdditionRefferedBack = factory(BudgetAdditionRefferedBack::class)->create();
        $fakeBudgetAdditionRefferedBack = factory(BudgetAdditionRefferedBack::class)->make()->toArray();

        $updatedBudgetAdditionRefferedBack = $this->budgetAdditionRefferedBackRepo->update($fakeBudgetAdditionRefferedBack, $budgetAdditionRefferedBack->id);

        $this->assertModelData($fakeBudgetAdditionRefferedBack, $updatedBudgetAdditionRefferedBack->toArray());
        $dbBudgetAdditionRefferedBack = $this->budgetAdditionRefferedBackRepo->find($budgetAdditionRefferedBack->id);
        $this->assertModelData($fakeBudgetAdditionRefferedBack, $dbBudgetAdditionRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_budget_addition_reffered_back()
    {
        $budgetAdditionRefferedBack = factory(BudgetAdditionRefferedBack::class)->create();

        $resp = $this->budgetAdditionRefferedBackRepo->delete($budgetAdditionRefferedBack->id);

        $this->assertTrue($resp);
        $this->assertNull(BudgetAdditionRefferedBack::find($budgetAdditionRefferedBack->id), 'BudgetAdditionRefferedBack should not exist in DB');
    }
}

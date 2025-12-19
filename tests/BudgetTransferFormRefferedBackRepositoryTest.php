<?php namespace Tests\Repositories;

use App\Models\BudgetTransferFormRefferedBack;
use App\Repositories\BudgetTransferFormRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BudgetTransferFormRefferedBackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetTransferFormRefferedBackRepository
     */
    protected $budgetTransferFormRefferedBackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->budgetTransferFormRefferedBackRepo = \App::make(BudgetTransferFormRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_budget_transfer_form_reffered_back()
    {
        $budgetTransferFormRefferedBack = factory(BudgetTransferFormRefferedBack::class)->make()->toArray();

        $createdBudgetTransferFormRefferedBack = $this->budgetTransferFormRefferedBackRepo->create($budgetTransferFormRefferedBack);

        $createdBudgetTransferFormRefferedBack = $createdBudgetTransferFormRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdBudgetTransferFormRefferedBack);
        $this->assertNotNull($createdBudgetTransferFormRefferedBack['id'], 'Created BudgetTransferFormRefferedBack must have id specified');
        $this->assertNotNull(BudgetTransferFormRefferedBack::find($createdBudgetTransferFormRefferedBack['id']), 'BudgetTransferFormRefferedBack with given id must be in DB');
        $this->assertModelData($budgetTransferFormRefferedBack, $createdBudgetTransferFormRefferedBack);
    }

    /**
     * @test read
     */
    public function test_read_budget_transfer_form_reffered_back()
    {
        $budgetTransferFormRefferedBack = factory(BudgetTransferFormRefferedBack::class)->create();

        $dbBudgetTransferFormRefferedBack = $this->budgetTransferFormRefferedBackRepo->find($budgetTransferFormRefferedBack->id);

        $dbBudgetTransferFormRefferedBack = $dbBudgetTransferFormRefferedBack->toArray();
        $this->assertModelData($budgetTransferFormRefferedBack->toArray(), $dbBudgetTransferFormRefferedBack);
    }

    /**
     * @test update
     */
    public function test_update_budget_transfer_form_reffered_back()
    {
        $budgetTransferFormRefferedBack = factory(BudgetTransferFormRefferedBack::class)->create();
        $fakeBudgetTransferFormRefferedBack = factory(BudgetTransferFormRefferedBack::class)->make()->toArray();

        $updatedBudgetTransferFormRefferedBack = $this->budgetTransferFormRefferedBackRepo->update($fakeBudgetTransferFormRefferedBack, $budgetTransferFormRefferedBack->id);

        $this->assertModelData($fakeBudgetTransferFormRefferedBack, $updatedBudgetTransferFormRefferedBack->toArray());
        $dbBudgetTransferFormRefferedBack = $this->budgetTransferFormRefferedBackRepo->find($budgetTransferFormRefferedBack->id);
        $this->assertModelData($fakeBudgetTransferFormRefferedBack, $dbBudgetTransferFormRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_budget_transfer_form_reffered_back()
    {
        $budgetTransferFormRefferedBack = factory(BudgetTransferFormRefferedBack::class)->create();

        $resp = $this->budgetTransferFormRefferedBackRepo->delete($budgetTransferFormRefferedBack->id);

        $this->assertTrue($resp);
        $this->assertNull(BudgetTransferFormRefferedBack::find($budgetTransferFormRefferedBack->id), 'BudgetTransferFormRefferedBack should not exist in DB');
    }
}

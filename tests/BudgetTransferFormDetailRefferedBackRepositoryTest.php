<?php namespace Tests\Repositories;

use App\Models\BudgetTransferFormDetailRefferedBack;
use App\Repositories\BudgetTransferFormDetailRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BudgetTransferFormDetailRefferedBackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetTransferFormDetailRefferedBackRepository
     */
    protected $budgetTransferFormDetailRefferedBackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->budgetTransferFormDetailRefferedBackRepo = \App::make(BudgetTransferFormDetailRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_budget_transfer_form_detail_reffered_back()
    {
        $budgetTransferFormDetailRefferedBack = factory(BudgetTransferFormDetailRefferedBack::class)->make()->toArray();

        $createdBudgetTransferFormDetailRefferedBack = $this->budgetTransferFormDetailRefferedBackRepo->create($budgetTransferFormDetailRefferedBack);

        $createdBudgetTransferFormDetailRefferedBack = $createdBudgetTransferFormDetailRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdBudgetTransferFormDetailRefferedBack);
        $this->assertNotNull($createdBudgetTransferFormDetailRefferedBack['id'], 'Created BudgetTransferFormDetailRefferedBack must have id specified');
        $this->assertNotNull(BudgetTransferFormDetailRefferedBack::find($createdBudgetTransferFormDetailRefferedBack['id']), 'BudgetTransferFormDetailRefferedBack with given id must be in DB');
        $this->assertModelData($budgetTransferFormDetailRefferedBack, $createdBudgetTransferFormDetailRefferedBack);
    }

    /**
     * @test read
     */
    public function test_read_budget_transfer_form_detail_reffered_back()
    {
        $budgetTransferFormDetailRefferedBack = factory(BudgetTransferFormDetailRefferedBack::class)->create();

        $dbBudgetTransferFormDetailRefferedBack = $this->budgetTransferFormDetailRefferedBackRepo->find($budgetTransferFormDetailRefferedBack->id);

        $dbBudgetTransferFormDetailRefferedBack = $dbBudgetTransferFormDetailRefferedBack->toArray();
        $this->assertModelData($budgetTransferFormDetailRefferedBack->toArray(), $dbBudgetTransferFormDetailRefferedBack);
    }

    /**
     * @test update
     */
    public function test_update_budget_transfer_form_detail_reffered_back()
    {
        $budgetTransferFormDetailRefferedBack = factory(BudgetTransferFormDetailRefferedBack::class)->create();
        $fakeBudgetTransferFormDetailRefferedBack = factory(BudgetTransferFormDetailRefferedBack::class)->make()->toArray();

        $updatedBudgetTransferFormDetailRefferedBack = $this->budgetTransferFormDetailRefferedBackRepo->update($fakeBudgetTransferFormDetailRefferedBack, $budgetTransferFormDetailRefferedBack->id);

        $this->assertModelData($fakeBudgetTransferFormDetailRefferedBack, $updatedBudgetTransferFormDetailRefferedBack->toArray());
        $dbBudgetTransferFormDetailRefferedBack = $this->budgetTransferFormDetailRefferedBackRepo->find($budgetTransferFormDetailRefferedBack->id);
        $this->assertModelData($fakeBudgetTransferFormDetailRefferedBack, $dbBudgetTransferFormDetailRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_budget_transfer_form_detail_reffered_back()
    {
        $budgetTransferFormDetailRefferedBack = factory(BudgetTransferFormDetailRefferedBack::class)->create();

        $resp = $this->budgetTransferFormDetailRefferedBackRepo->delete($budgetTransferFormDetailRefferedBack->id);

        $this->assertTrue($resp);
        $this->assertNull(BudgetTransferFormDetailRefferedBack::find($budgetTransferFormDetailRefferedBack->id), 'BudgetTransferFormDetailRefferedBack should not exist in DB');
    }
}

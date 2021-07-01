<?php namespace Tests\Repositories;

use App\Models\BudgetReviewTransferAddition;
use App\Repositories\BudgetReviewTransferAdditionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BudgetReviewTransferAdditionRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetReviewTransferAdditionRepository
     */
    protected $budgetReviewTransferAdditionRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->budgetReviewTransferAdditionRepo = \App::make(BudgetReviewTransferAdditionRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_budget_review_transfer_addition()
    {
        $budgetReviewTransferAddition = factory(BudgetReviewTransferAddition::class)->make()->toArray();

        $createdBudgetReviewTransferAddition = $this->budgetReviewTransferAdditionRepo->create($budgetReviewTransferAddition);

        $createdBudgetReviewTransferAddition = $createdBudgetReviewTransferAddition->toArray();
        $this->assertArrayHasKey('id', $createdBudgetReviewTransferAddition);
        $this->assertNotNull($createdBudgetReviewTransferAddition['id'], 'Created BudgetReviewTransferAddition must have id specified');
        $this->assertNotNull(BudgetReviewTransferAddition::find($createdBudgetReviewTransferAddition['id']), 'BudgetReviewTransferAddition with given id must be in DB');
        $this->assertModelData($budgetReviewTransferAddition, $createdBudgetReviewTransferAddition);
    }

    /**
     * @test read
     */
    public function test_read_budget_review_transfer_addition()
    {
        $budgetReviewTransferAddition = factory(BudgetReviewTransferAddition::class)->create();

        $dbBudgetReviewTransferAddition = $this->budgetReviewTransferAdditionRepo->find($budgetReviewTransferAddition->id);

        $dbBudgetReviewTransferAddition = $dbBudgetReviewTransferAddition->toArray();
        $this->assertModelData($budgetReviewTransferAddition->toArray(), $dbBudgetReviewTransferAddition);
    }

    /**
     * @test update
     */
    public function test_update_budget_review_transfer_addition()
    {
        $budgetReviewTransferAddition = factory(BudgetReviewTransferAddition::class)->create();
        $fakeBudgetReviewTransferAddition = factory(BudgetReviewTransferAddition::class)->make()->toArray();

        $updatedBudgetReviewTransferAddition = $this->budgetReviewTransferAdditionRepo->update($fakeBudgetReviewTransferAddition, $budgetReviewTransferAddition->id);

        $this->assertModelData($fakeBudgetReviewTransferAddition, $updatedBudgetReviewTransferAddition->toArray());
        $dbBudgetReviewTransferAddition = $this->budgetReviewTransferAdditionRepo->find($budgetReviewTransferAddition->id);
        $this->assertModelData($fakeBudgetReviewTransferAddition, $dbBudgetReviewTransferAddition->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_budget_review_transfer_addition()
    {
        $budgetReviewTransferAddition = factory(BudgetReviewTransferAddition::class)->create();

        $resp = $this->budgetReviewTransferAdditionRepo->delete($budgetReviewTransferAddition->id);

        $this->assertTrue($resp);
        $this->assertNull(BudgetReviewTransferAddition::find($budgetReviewTransferAddition->id), 'BudgetReviewTransferAddition should not exist in DB');
    }
}

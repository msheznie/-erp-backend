<?php namespace Tests\Repositories;

use App\Models\BudgetDetailComment;
use App\Repositories\BudgetDetailCommentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BudgetDetailCommentRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetDetailCommentRepository
     */
    protected $budgetDetailCommentRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->budgetDetailCommentRepo = \App::make(BudgetDetailCommentRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_budget_detail_comment()
    {
        $budgetDetailComment = factory(BudgetDetailComment::class)->make()->toArray();

        $createdBudgetDetailComment = $this->budgetDetailCommentRepo->create($budgetDetailComment);

        $createdBudgetDetailComment = $createdBudgetDetailComment->toArray();
        $this->assertArrayHasKey('id', $createdBudgetDetailComment);
        $this->assertNotNull($createdBudgetDetailComment['id'], 'Created BudgetDetailComment must have id specified');
        $this->assertNotNull(BudgetDetailComment::find($createdBudgetDetailComment['id']), 'BudgetDetailComment with given id must be in DB');
        $this->assertModelData($budgetDetailComment, $createdBudgetDetailComment);
    }

    /**
     * @test read
     */
    public function test_read_budget_detail_comment()
    {
        $budgetDetailComment = factory(BudgetDetailComment::class)->create();

        $dbBudgetDetailComment = $this->budgetDetailCommentRepo->find($budgetDetailComment->id);

        $dbBudgetDetailComment = $dbBudgetDetailComment->toArray();
        $this->assertModelData($budgetDetailComment->toArray(), $dbBudgetDetailComment);
    }

    /**
     * @test update
     */
    public function test_update_budget_detail_comment()
    {
        $budgetDetailComment = factory(BudgetDetailComment::class)->create();
        $fakeBudgetDetailComment = factory(BudgetDetailComment::class)->make()->toArray();

        $updatedBudgetDetailComment = $this->budgetDetailCommentRepo->update($fakeBudgetDetailComment, $budgetDetailComment->id);

        $this->assertModelData($fakeBudgetDetailComment, $updatedBudgetDetailComment->toArray());
        $dbBudgetDetailComment = $this->budgetDetailCommentRepo->find($budgetDetailComment->id);
        $this->assertModelData($fakeBudgetDetailComment, $dbBudgetDetailComment->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_budget_detail_comment()
    {
        $budgetDetailComment = factory(BudgetDetailComment::class)->create();

        $resp = $this->budgetDetailCommentRepo->delete($budgetDetailComment->id);

        $this->assertTrue($resp);
        $this->assertNull(BudgetDetailComment::find($budgetDetailComment->id), 'BudgetDetailComment should not exist in DB');
    }
}

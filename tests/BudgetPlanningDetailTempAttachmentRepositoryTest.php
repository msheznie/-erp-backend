<?php namespace Tests\Repositories;

use App\Models\BudgetPlanningDetailTempAttachment;
use App\Repositories\BudgetPlanningDetailTempAttachmentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BudgetPlanningDetailTempAttachmentRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudgetPlanningDetailTempAttachmentRepository
     */
    protected $budgetPlanningDetailTempAttachmentRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->budgetPlanningDetailTempAttachmentRepo = \App::make(BudgetPlanningDetailTempAttachmentRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_budget_planning_detail_temp_attachment()
    {
        $budgetPlanningDetailTempAttachment = factory(BudgetPlanningDetailTempAttachment::class)->make()->toArray();

        $createdBudgetPlanningDetailTempAttachment = $this->budgetPlanningDetailTempAttachmentRepo->create($budgetPlanningDetailTempAttachment);

        $createdBudgetPlanningDetailTempAttachment = $createdBudgetPlanningDetailTempAttachment->toArray();
        $this->assertArrayHasKey('id', $createdBudgetPlanningDetailTempAttachment);
        $this->assertNotNull($createdBudgetPlanningDetailTempAttachment['id'], 'Created BudgetPlanningDetailTempAttachment must have id specified');
        $this->assertNotNull(BudgetPlanningDetailTempAttachment::find($createdBudgetPlanningDetailTempAttachment['id']), 'BudgetPlanningDetailTempAttachment with given id must be in DB');
        $this->assertModelData($budgetPlanningDetailTempAttachment, $createdBudgetPlanningDetailTempAttachment);
    }

    /**
     * @test read
     */
    public function test_read_budget_planning_detail_temp_attachment()
    {
        $budgetPlanningDetailTempAttachment = factory(BudgetPlanningDetailTempAttachment::class)->create();

        $dbBudgetPlanningDetailTempAttachment = $this->budgetPlanningDetailTempAttachmentRepo->find($budgetPlanningDetailTempAttachment->id);

        $dbBudgetPlanningDetailTempAttachment = $dbBudgetPlanningDetailTempAttachment->toArray();
        $this->assertModelData($budgetPlanningDetailTempAttachment->toArray(), $dbBudgetPlanningDetailTempAttachment);
    }

    /**
     * @test update
     */
    public function test_update_budget_planning_detail_temp_attachment()
    {
        $budgetPlanningDetailTempAttachment = factory(BudgetPlanningDetailTempAttachment::class)->create();
        $fakeBudgetPlanningDetailTempAttachment = factory(BudgetPlanningDetailTempAttachment::class)->make()->toArray();

        $updatedBudgetPlanningDetailTempAttachment = $this->budgetPlanningDetailTempAttachmentRepo->update($fakeBudgetPlanningDetailTempAttachment, $budgetPlanningDetailTempAttachment->id);

        $this->assertModelData($fakeBudgetPlanningDetailTempAttachment, $updatedBudgetPlanningDetailTempAttachment->toArray());
        $dbBudgetPlanningDetailTempAttachment = $this->budgetPlanningDetailTempAttachmentRepo->find($budgetPlanningDetailTempAttachment->id);
        $this->assertModelData($fakeBudgetPlanningDetailTempAttachment, $dbBudgetPlanningDetailTempAttachment->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_budget_planning_detail_temp_attachment()
    {
        $budgetPlanningDetailTempAttachment = factory(BudgetPlanningDetailTempAttachment::class)->create();

        $resp = $this->budgetPlanningDetailTempAttachmentRepo->delete($budgetPlanningDetailTempAttachment->id);

        $this->assertTrue($resp);
        $this->assertNull(BudgetPlanningDetailTempAttachment::find($budgetPlanningDetailTempAttachment->id), 'BudgetPlanningDetailTempAttachment should not exist in DB');
    }
}

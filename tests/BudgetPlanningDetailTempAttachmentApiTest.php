<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BudgetPlanningDetailTempAttachment;

class BudgetPlanningDetailTempAttachmentApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_budget_planning_detail_temp_attachment()
    {
        $budgetPlanningDetailTempAttachment = factory(BudgetPlanningDetailTempAttachment::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/budget_planning_detail_temp_attachments', $budgetPlanningDetailTempAttachment
        );

        $this->assertApiResponse($budgetPlanningDetailTempAttachment);
    }

    /**
     * @test
     */
    public function test_read_budget_planning_detail_temp_attachment()
    {
        $budgetPlanningDetailTempAttachment = factory(BudgetPlanningDetailTempAttachment::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/budget_planning_detail_temp_attachments/'.$budgetPlanningDetailTempAttachment->id
        );

        $this->assertApiResponse($budgetPlanningDetailTempAttachment->toArray());
    }

    /**
     * @test
     */
    public function test_update_budget_planning_detail_temp_attachment()
    {
        $budgetPlanningDetailTempAttachment = factory(BudgetPlanningDetailTempAttachment::class)->create();
        $editedBudgetPlanningDetailTempAttachment = factory(BudgetPlanningDetailTempAttachment::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/budget_planning_detail_temp_attachments/'.$budgetPlanningDetailTempAttachment->id,
            $editedBudgetPlanningDetailTempAttachment
        );

        $this->assertApiResponse($editedBudgetPlanningDetailTempAttachment);
    }

    /**
     * @test
     */
    public function test_delete_budget_planning_detail_temp_attachment()
    {
        $budgetPlanningDetailTempAttachment = factory(BudgetPlanningDetailTempAttachment::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/budget_planning_detail_temp_attachments/'.$budgetPlanningDetailTempAttachment->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/budget_planning_detail_temp_attachments/'.$budgetPlanningDetailTempAttachment->id
        );

        $this->response->assertStatus(404);
    }
}

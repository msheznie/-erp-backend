<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\WorkflowConfigurationHodAction;

class WorkflowConfigurationHodActionApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_workflow_configuration_hod_action()
    {
        $workflowConfigurationHodAction = factory(WorkflowConfigurationHodAction::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/workflow_configuration_hod_actions', $workflowConfigurationHodAction
        );

        $this->assertApiResponse($workflowConfigurationHodAction);
    }

    /**
     * @test
     */
    public function test_read_workflow_configuration_hod_action()
    {
        $workflowConfigurationHodAction = factory(WorkflowConfigurationHodAction::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/workflow_configuration_hod_actions/'.$workflowConfigurationHodAction->id
        );

        $this->assertApiResponse($workflowConfigurationHodAction->toArray());
    }

    /**
     * @test
     */
    public function test_update_workflow_configuration_hod_action()
    {
        $workflowConfigurationHodAction = factory(WorkflowConfigurationHodAction::class)->create();
        $editedWorkflowConfigurationHodAction = factory(WorkflowConfigurationHodAction::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/workflow_configuration_hod_actions/'.$workflowConfigurationHodAction->id,
            $editedWorkflowConfigurationHodAction
        );

        $this->assertApiResponse($editedWorkflowConfigurationHodAction);
    }

    /**
     * @test
     */
    public function test_delete_workflow_configuration_hod_action()
    {
        $workflowConfigurationHodAction = factory(WorkflowConfigurationHodAction::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/workflow_configuration_hod_actions/'.$workflowConfigurationHodAction->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/workflow_configuration_hod_actions/'.$workflowConfigurationHodAction->id
        );

        $this->response->assertStatus(404);
    }
}

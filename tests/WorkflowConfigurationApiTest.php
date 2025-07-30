<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\WorkflowConfiguration;

class WorkflowConfigurationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_workflow_configuration()
    {
        $workflowConfiguration = factory(WorkflowConfiguration::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/workflow_configurations', $workflowConfiguration
        );

        $this->assertApiResponse($workflowConfiguration);
    }

    /**
     * @test
     */
    public function test_read_workflow_configuration()
    {
        $workflowConfiguration = factory(WorkflowConfiguration::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/workflow_configurations/'.$workflowConfiguration->id
        );

        $this->assertApiResponse($workflowConfiguration->toArray());
    }

    /**
     * @test
     */
    public function test_update_workflow_configuration()
    {
        $workflowConfiguration = factory(WorkflowConfiguration::class)->create();
        $editedWorkflowConfiguration = factory(WorkflowConfiguration::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/workflow_configurations/'.$workflowConfiguration->id,
            $editedWorkflowConfiguration
        );

        $this->assertApiResponse($editedWorkflowConfiguration);
    }

    /**
     * @test
     */
    public function test_delete_workflow_configuration()
    {
        $workflowConfiguration = factory(WorkflowConfiguration::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/workflow_configurations/'.$workflowConfiguration->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/workflow_configurations/'.$workflowConfiguration->id
        );

        $this->response->assertStatus(404);
    }
}

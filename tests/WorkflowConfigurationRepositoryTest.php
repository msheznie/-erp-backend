<?php namespace Tests\Repositories;

use App\Models\WorkflowConfiguration;
use App\Repositories\WorkflowConfigurationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class WorkflowConfigurationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var WorkflowConfigurationRepository
     */
    protected $workflowConfigurationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->workflowConfigurationRepo = \App::make(WorkflowConfigurationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_workflow_configuration()
    {
        $workflowConfiguration = factory(WorkflowConfiguration::class)->make()->toArray();

        $createdWorkflowConfiguration = $this->workflowConfigurationRepo->create($workflowConfiguration);

        $createdWorkflowConfiguration = $createdWorkflowConfiguration->toArray();
        $this->assertArrayHasKey('id', $createdWorkflowConfiguration);
        $this->assertNotNull($createdWorkflowConfiguration['id'], 'Created WorkflowConfiguration must have id specified');
        $this->assertNotNull(WorkflowConfiguration::find($createdWorkflowConfiguration['id']), 'WorkflowConfiguration with given id must be in DB');
        $this->assertModelData($workflowConfiguration, $createdWorkflowConfiguration);
    }

    /**
     * @test read
     */
    public function test_read_workflow_configuration()
    {
        $workflowConfiguration = factory(WorkflowConfiguration::class)->create();

        $dbWorkflowConfiguration = $this->workflowConfigurationRepo->find($workflowConfiguration->id);

        $dbWorkflowConfiguration = $dbWorkflowConfiguration->toArray();
        $this->assertModelData($workflowConfiguration->toArray(), $dbWorkflowConfiguration);
    }

    /**
     * @test update
     */
    public function test_update_workflow_configuration()
    {
        $workflowConfiguration = factory(WorkflowConfiguration::class)->create();
        $fakeWorkflowConfiguration = factory(WorkflowConfiguration::class)->make()->toArray();

        $updatedWorkflowConfiguration = $this->workflowConfigurationRepo->update($fakeWorkflowConfiguration, $workflowConfiguration->id);

        $this->assertModelData($fakeWorkflowConfiguration, $updatedWorkflowConfiguration->toArray());
        $dbWorkflowConfiguration = $this->workflowConfigurationRepo->find($workflowConfiguration->id);
        $this->assertModelData($fakeWorkflowConfiguration, $dbWorkflowConfiguration->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_workflow_configuration()
    {
        $workflowConfiguration = factory(WorkflowConfiguration::class)->create();

        $resp = $this->workflowConfigurationRepo->delete($workflowConfiguration->id);

        $this->assertTrue($resp);
        $this->assertNull(WorkflowConfiguration::find($workflowConfiguration->id), 'WorkflowConfiguration should not exist in DB');
    }
}

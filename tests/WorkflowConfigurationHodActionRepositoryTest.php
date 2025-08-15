<?php namespace Tests\Repositories;

use App\Models\WorkflowConfigurationHodAction;
use App\Repositories\WorkflowConfigurationHodActionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class WorkflowConfigurationHodActionRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var WorkflowConfigurationHodActionRepository
     */
    protected $workflowConfigurationHodActionRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->workflowConfigurationHodActionRepo = \App::make(WorkflowConfigurationHodActionRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_workflow_configuration_hod_action()
    {
        $workflowConfigurationHodAction = factory(WorkflowConfigurationHodAction::class)->make()->toArray();

        $createdWorkflowConfigurationHodAction = $this->workflowConfigurationHodActionRepo->create($workflowConfigurationHodAction);

        $createdWorkflowConfigurationHodAction = $createdWorkflowConfigurationHodAction->toArray();
        $this->assertArrayHasKey('id', $createdWorkflowConfigurationHodAction);
        $this->assertNotNull($createdWorkflowConfigurationHodAction['id'], 'Created WorkflowConfigurationHodAction must have id specified');
        $this->assertNotNull(WorkflowConfigurationHodAction::find($createdWorkflowConfigurationHodAction['id']), 'WorkflowConfigurationHodAction with given id must be in DB');
        $this->assertModelData($workflowConfigurationHodAction, $createdWorkflowConfigurationHodAction);
    }

    /**
     * @test read
     */
    public function test_read_workflow_configuration_hod_action()
    {
        $workflowConfigurationHodAction = factory(WorkflowConfigurationHodAction::class)->create();

        $dbWorkflowConfigurationHodAction = $this->workflowConfigurationHodActionRepo->find($workflowConfigurationHodAction->id);

        $dbWorkflowConfigurationHodAction = $dbWorkflowConfigurationHodAction->toArray();
        $this->assertModelData($workflowConfigurationHodAction->toArray(), $dbWorkflowConfigurationHodAction);
    }

    /**
     * @test update
     */
    public function test_update_workflow_configuration_hod_action()
    {
        $workflowConfigurationHodAction = factory(WorkflowConfigurationHodAction::class)->create();
        $fakeWorkflowConfigurationHodAction = factory(WorkflowConfigurationHodAction::class)->make()->toArray();

        $updatedWorkflowConfigurationHodAction = $this->workflowConfigurationHodActionRepo->update($fakeWorkflowConfigurationHodAction, $workflowConfigurationHodAction->id);

        $this->assertModelData($fakeWorkflowConfigurationHodAction, $updatedWorkflowConfigurationHodAction->toArray());
        $dbWorkflowConfigurationHodAction = $this->workflowConfigurationHodActionRepo->find($workflowConfigurationHodAction->id);
        $this->assertModelData($fakeWorkflowConfigurationHodAction, $dbWorkflowConfigurationHodAction->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_workflow_configuration_hod_action()
    {
        $workflowConfigurationHodAction = factory(WorkflowConfigurationHodAction::class)->create();

        $resp = $this->workflowConfigurationHodActionRepo->delete($workflowConfigurationHodAction->id);

        $this->assertTrue($resp);
        $this->assertNull(WorkflowConfigurationHodAction::find($workflowConfigurationHodAction->id), 'WorkflowConfigurationHodAction should not exist in DB');
    }
}

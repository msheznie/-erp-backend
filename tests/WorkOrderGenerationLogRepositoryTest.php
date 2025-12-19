<?php namespace Tests\Repositories;

use App\Models\WorkOrderGenerationLog;
use App\Repositories\WorkOrderGenerationLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class WorkOrderGenerationLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var WorkOrderGenerationLogRepository
     */
    protected $workOrderGenerationLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->workOrderGenerationLogRepo = \App::make(WorkOrderGenerationLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_work_order_generation_log()
    {
        $workOrderGenerationLog = factory(WorkOrderGenerationLog::class)->make()->toArray();

        $createdWorkOrderGenerationLog = $this->workOrderGenerationLogRepo->create($workOrderGenerationLog);

        $createdWorkOrderGenerationLog = $createdWorkOrderGenerationLog->toArray();
        $this->assertArrayHasKey('id', $createdWorkOrderGenerationLog);
        $this->assertNotNull($createdWorkOrderGenerationLog['id'], 'Created WorkOrderGenerationLog must have id specified');
        $this->assertNotNull(WorkOrderGenerationLog::find($createdWorkOrderGenerationLog['id']), 'WorkOrderGenerationLog with given id must be in DB');
        $this->assertModelData($workOrderGenerationLog, $createdWorkOrderGenerationLog);
    }

    /**
     * @test read
     */
    public function test_read_work_order_generation_log()
    {
        $workOrderGenerationLog = factory(WorkOrderGenerationLog::class)->create();

        $dbWorkOrderGenerationLog = $this->workOrderGenerationLogRepo->find($workOrderGenerationLog->id);

        $dbWorkOrderGenerationLog = $dbWorkOrderGenerationLog->toArray();
        $this->assertModelData($workOrderGenerationLog->toArray(), $dbWorkOrderGenerationLog);
    }

    /**
     * @test update
     */
    public function test_update_work_order_generation_log()
    {
        $workOrderGenerationLog = factory(WorkOrderGenerationLog::class)->create();
        $fakeWorkOrderGenerationLog = factory(WorkOrderGenerationLog::class)->make()->toArray();

        $updatedWorkOrderGenerationLog = $this->workOrderGenerationLogRepo->update($fakeWorkOrderGenerationLog, $workOrderGenerationLog->id);

        $this->assertModelData($fakeWorkOrderGenerationLog, $updatedWorkOrderGenerationLog->toArray());
        $dbWorkOrderGenerationLog = $this->workOrderGenerationLogRepo->find($workOrderGenerationLog->id);
        $this->assertModelData($fakeWorkOrderGenerationLog, $dbWorkOrderGenerationLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_work_order_generation_log()
    {
        $workOrderGenerationLog = factory(WorkOrderGenerationLog::class)->create();

        $resp = $this->workOrderGenerationLogRepo->delete($workOrderGenerationLog->id);

        $this->assertTrue($resp);
        $this->assertNull(WorkOrderGenerationLog::find($workOrderGenerationLog->id), 'WorkOrderGenerationLog should not exist in DB');
    }
}
